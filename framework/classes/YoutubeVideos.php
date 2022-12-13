<?php

namespace GYBC;

class YoutubePosts {

	public $youtube;
	public $options;

	public function __construct(){
		$this->options = get_option( 'youtube_options' );

		// default the max results by ten if not set
		$this->options['maxResults'] 
		= isset($this->options['maxResults']) && !empty($this->options['maxResults']) ? $this->options['maxResults'] : 10;

		$this->youtube = new \Madcoda\Youtube\Youtube(array('key' => $this->options['google_api_key']));
	}

	// THree main functions used
	public function updateYoutubePostsByCron(){

		$options = get_option( 'youtube_options' );

		$this->options = $options;

		$pageToken = get_option( 'pageToken', $default = false );

		if( empty($this->options['playlist']) ){
			$videos = $this->getVideosByChannelId($options['yt_channel_id'], $pageToken);
			$this->updateVideoPostsByVideos($videos);
		} else {
			$videos = $this->getVideosByPlaylistId($this->options['playlist'], $pageToken);
			$this->updateVideoPostsByPlaylistItems($videos);
		}
	}

	public function getVideosByChannelId($channel_id, $pageToken = false){

		$params = array(
			'q'=>'',
			'channelId'=>$channel_id,
			'maxResults'=>$this->options['maxResults'],
			'part'=>'snippet, id',
			'type'=>'video'
		);

		if( $pageToken != false ){
			$params['pageToken'] = $pageToken;
		}

		$response = $this->youtube->searchAdvanced($params, true);

		if(isset($response['info']) && isset($response['info']['nextPageToken']) && !empty($response['info']['nextPageToken'])){
			update_option( 'pageToken', $response['info']['nextPageToken'] );
		} else {
			delete_option( 'pageToken' );
		}

		return $response['results'];
	}

	public function getVideosByPlaylistId($playlistId, $pageToken = false){

		$params = array();

		if( $pageToken != false ){
			$params['pageToken'] = $pageToken;
		}

		$response = $this->youtube->getPlaylistItemsByPlaylistId($playlistId, $params, $this->options['maxResults'], true);
		if(isset($response['info']) && isset($response['info']['nextPageToken']) && !empty($response['info']['nextPageToken'])){
			update_option( 'pageToken', $response['info']['nextPageToken'] );
		} else {
			delete_option( 'pageToken' );
		}

		return $response['results'];
	}

	public function updateVideoPostsByVideos($videos){

		foreach($videos as $item){

			if( $item->id->kind == 'youtube#channel' ) continue;
			
			$result = $this->insertYoutubeVideo($item);

			if ( is_wp_error( $result ) ) {
			   $error_message = $result->get_error_message();
			   error_log(print_r(date('Y-m-d H:i:s', time()) . ' - ' . $error_message,1) . "\r\n", 3, ROOT_PATH . 'logs/video_error.log');
			   return;
			}

			$post_id = $result;

			$duration = $this->getVideoDuration( $item->id->videoId );
			update_post_meta( $post_id, 'video_duration', $duration);
			
			if(isset($item->id->kind) && ($item->id->kind == 'youtube#video')){
			
				update_post_meta( $post_id, 'video-id', $item->id->videoId );
			}

			if(isset($item->snippet->resourceId->kind) && ($item->snippet->resourceId->kind == 'youtube#video')){

				update_post_meta( $post_id, 'video-id', $item->snippet->resourceId->videoId );
			}

			$this->setVideoThumbnailAsFeaturedImage($item, $post_id);
		}
	}

	public function updateVideoPostsByPlaylistItems($videos){

		for($x=0; $x<count($videos); $x++){
									
			$post_id = $this->insertYoutubePlayListItem($videos[$x]);

			if ( is_wp_error( $post_id ) ) {
				echo '<pre>'.print_r($post_id->get_error_message(), 1).'</pre>';
				continue;
			}

			$duration = $this->getVideoDuration( $videos[$x]->contentDetails->videoId );
			update_post_meta( $post_id, 'video_duration', $duration);
			update_post_meta( $post_id, 'video-id', $videos[$x]->contentDetails->videoId );
			$this->setVideoThumbnailAsFeaturedImage($videos[$x], $post_id);
		}
	}

	// Helper functions
	
	public function insertYoutubeVideo($item){

		if( $item->snippet->title == 'Private Video' )
			return;

		$user_id = get_current_user_id();

		global $wpdb;

		$title = sanitize_title( $item->snippet->title );

		$result = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_name` = '{$title}' AND `post_type` = 'videos'");

		if(is_null($result)){

			if(isset($item->id->kind) && ($item->id->kind == 'youtube#video')){

				$video = $item->id->videoId;
			}

			if(isset($item->snippet->resourceId->kind) && ($item->snippet->resourceId->kind == 'youtube#video')){

				$video = $item->snippet->resourceId->videoId;
			}

			kses_remove_filters();
			$result = wp_insert_post( array(			
	        'post_author' => $user_id,
	        'post_content' => '<iframe src="https://www.youtube.com/embed/'.$video.'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
	        'post_content_filtered' => '',
	        'post_title' => $item->snippet->title,
	        'post_name' => $title,
	        'post_excerpt' => '',
	        'post_status' => 'publish',
	        'post_type' => 'videos',
	        'post_date'=>isset($item->contentDetails->videoPublishedAt) ? date('Y-m-d H:i:s', strtotime($item->contentDetails->videoPublishedAt)): date('Y-m-d', strtotime('-1 day', strtotime(date("r"))))
			), true );
			kses_init_filters();

			if(!is_wp_error($result)){
			  error_log(print_r(date('Y-m-d H:i:s', time()) . ' - ' . $result,1) . "\r\n", 3, ROOT_PATH . 'logs/posted_videos.log');
			}


		} else {$result = $result->ID;}

		return $result;
	}

	public function insertYoutubePlayListItem($item){

		if( $item->snippet->title == 'Private Video' )
			return;

		$user_id = get_current_user_id();

		global $wpdb;

		$title = sanitize_title( $item->snippet->title );

		$result = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_name` = '{$title}' AND `post_type` = 'videos'");

		if(is_null($result)){

			kses_remove_filters();
			$insert_array = array(			
	        'post_author' => $user_id,
	        'post_content' => '<iframe src="https://www.youtube.com/embed/'.$item->contentDetauls->videoId.'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
	        'post_content_filtered' => '',
	        'post_title' => $item->snippet->title,
	        'post_name' => $title,
	        'post_excerpt' => '',
	        'post_status' => 'publish',
	        'post_type' => 'videos',
	        'post_date'=>isset($item->contentDetails->videoPublishedAt) ? date('Y-m-d H:i:s', strtotime($item->contentDetails->videoPublishedAt)): date('Y-m-d', strtotime('-1 day', strtotime(date("r"))))
			);
			$result = wp_insert_post($insert_array, true);

			kses_init_filters();


			if(!is_wp_error($result)){
			  error_log(print_r(date('Y-m-d H:i:s', time()) . ' - ' . $result,1) . "\r\n", 3, ROOT_PATH . 'logs/posted_videos.log');
			}


		} else {$result = $result->ID;}

		return $result;
	}

	public function setVideoThumbnailAsFeaturedImage($item, $post_id){

		$thumbnail = get_post_meta( $post_id, 'backup-thumbnail', true);

		if( has_post_thumbnail( $post_id ) && $thumbnail ){
			return;
		}
		
		$thumbnail = $this->getVideoThumbnail($item);

		$destination = $this->getTempFolder();

		$filename = '/'.sanitize_title( $item->snippet->title ).'-'.(time()*rand(1,100)).'.jpg';

		if( isset($thumbnail->url) ){

			$file = file_put_contents ( $destination.$filename, file_get_contents($thumbnail->url));	

			if( $file ){

				$uploads_dir 	 = wp_upload_dir();

				$uploaded_file = $filename;

				// Store the parts of the file name into an array
				$pi = pathinfo($uploaded_file);

				$goal_image_file = wp_upload_bits( $pi['filename'].'.'.$pi['extension'] , null, file_get_contents( $destination.$filename ) );
				

				// Set post meta about this image. Need the comment ID and need the path.
				if( empty($goal_image_file['error']) ) {

					// Prepare an array of post data for the attachment.
					$attachment = array(
						'guid'           => $goal_image_file['url'], 
						'post_mime_type' => $goal_image_file['type'],
						'post_title'     => $item->snippet->title,
						'post_content'   => '',
						'post_status'    => 'inherit'
					);

					update_post_meta( $post_id, 'backup-thumbnail', $goal_image_file['url']);

					// Insert the attachment.
					$attach_id = wp_insert_attachment( $attachment, $goal_image_file['file'], $post_id );


					if (!is_wp_error($attach_id)) {

						require_once( ABSPATH . 'wp-admin/includes/image.php' );
						
						// Generate the metadata for the attachment, and update the database record.
						$attach_data = wp_generate_attachment_metadata( $attach_id, $goal_image_file['file'] );

						$wuam = wp_update_attachment_metadata( $attach_id, $attach_data );

						$spt = set_post_thumbnail( $post_id, $attach_id );

			    }


				} else {

					echo '<pre>'.print_r('image not created', 1).'</pre>';
				}

			} 

		}
	}

	public function getVideoThumbnail($item){

		if( isset($item->snippet->thumbnails->maxres) ){ return $item->snippet->thumbnails->maxres; }
		elseif( isset($item->snippet->thumbnails->standard) ){ return $item->snippet->thumbnails->standard; }
		elseif( isset($item->snippet->thumbnails->high) ){ return $item->snippet->thumbnails->high; }
		elseif( isset($item->snippet->thumbnails->medium) ){ return $item->snippet->thumbnails->medium; }
		elseif( isset($item->snippet->thumbnails->default) ){ return $item->snippet->thumbnails->default; }
	}

	private function getTempFolder(){

		$uploads_dir = wp_upload_dir();

		$uploads_dir = $uploads_dir['basedir'];

		$user_folder = 'temp';

		if( ! file_exists($uploads_dir . '/' .  $user_folder) ){
			mkdir($uploads_dir . '/' . $user_folder);
		}

		//$user_folder = $user_folder . '/' . $_POST['action'];
		
		if( ! file_exists($uploads_dir . '/' . $user_folder) ){
			mkdir($uploads_dir . '/' . $user_folder);
		}

		$destination =  $uploads_dir . '/' . $user_folder;

		return $destination;
	}

	private function getVideoDuration($videoId){
		$video_info = $this->youtube->getVideosInfo($videoId);
		preg_match_all('/(\d+)/',$video_info[0]->contentDetails->duration,$parts);
		$hours = floor($parts[0][0]/60);
		$minutes = intval($parts[0][0]%60);
		$seconds = intval($parts[0][1]);

		$hours 	 = $hours < 1 ? '' : $hours . ':';
		$minutes = strlen($minutes) < 2 ? str_pad($minutes, 2, '0', STR_PAD_LEFT) : $minutes;
		$seconds = strlen($seconds) < 2 ? str_pad($seconds, 2, '0', STR_PAD_LEFT) : $seconds;

		$duration = $hours .  $minutes . ':' .$seconds;
		return $duration;
	}
}