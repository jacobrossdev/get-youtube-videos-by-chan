<?php
	function show_sermon_feedback(){
		$string =  $_SESSION['sermons']['message'];
		unset($_SESSION['sermons']);
			return $string;
	}
?>

<style>
		.group:before,
		.group:after {
		    content: "";
		    display: table;
		} 
		.group:after {
		    clear: both;
		}
		.group {
		    zoom: 1; /* For IE 6/7 (trigger hasLayout) */
		}

		.options {
			margin-bottom: 30px;
		}
		.form-box {
			background-color: #fff;
			border: 1px solid #ddd;
			width: 70%
		}

		.form-header {

			border-bottom: 1px solid #ddd;
			padding: 0 10px;
		}

		.form-footer {

			border-top: 1px solid #ddd;
			padding: 6px 10px;
		}

		.footer-message { float: left; line-height: 34px; color: #555 }

		.form-row {
			position: relative;
			padding: 10px;
			border-bottom: 1px solid #ddd;
		}
		.form-row:before {
			content: '';
			width: 20%;
			background-color: #f9f9f9;
			border-right: 1px solid #ddd;
			display: block;
			position: absolute;
			top: 0;
			left: 0;
			bottom: 0;
		}
		
		.form-input {
			padding-left: 20%;
		}

		.form-input .inner { padding: 0 8px; }

		.form-input .inner input { width: 100%; }

		label {
			position: absolute;
			top: 10px;
			left: 10px;
		}
		
		
		input[type="submit"]:active {

			background-color: #53b8e8;
		}
		input[type="submit"] {
			padding: 8px 12px;
			background-color: #0073AA;
			border: none;
			color: #fff;
			border-radius: 6px;
			float: right;
		}

</style>
<div class="wrap">
	
	<div class="options">
			
		<div class="form-box">

			<form action="<?php echo get_admin_url();?>admin.php?page=youtube_admin" method="POST">
				
				<div class="form-header">
						
					<h3>YouTube Interface</h3>

				</div>
				
				<div class="form-row">
					
					<label for="progress_bar_value">Google API Key</label>
					
					<div class="form-input"><div class="inner"><input type="index" value="<?php echo isset($options) && isset($options['google_api_key']) && !empty($options['google_api_key']) ? $options['google_api_key'] : ''; ?>" name="google_api_key" /></div></div>

				</div>

				<div class="form-row">
					
					<label for="progress_bar_value">Channel ID</label>
					
					<div class="form-input"><div class="inner"><input type="index" value="<?php echo isset($options) && isset($options['yt_channel_id']) && !empty($options['yt_channel_id']) ? $options['yt_channel_id'] : '';  ?>" name="yt_channel_id" /></div></div>

				</div>
				<div class="form-row">
					
					<label for="progress_bar_value">Playlist</label>
					
					<div class="form-input"><div class="inner"><input type="index" value="<?php echo isset($options) && isset($options['playlist']) && !empty($options['playlist']) ? $options['playlist'] : '';  ?>" name="playlist" /></div></div>

				</div>
				<div class="form-row">
					
					<label for="progress_bar_value">Max Videos Queried</label>
					
					<div class="form-input"><div class="inner"><input type="index" value="<?php echo isset($options) && isset($options['maxResults']) && !empty($options['maxResults']) ? $options['maxResults'] : '';  ?>" name="maxResults" /></div></div>

				</div>

				<div class="form-footer group">

					<div class="footer-message"><?=isset($_POST['publish']) ? '<span style="color: #009900">Options saved.</span>' : ''?></div>
					
					<input type="submit" name="publish">
					
				</div>

			</form>

		</div>

		<div class="form-box">
			
			<form action="<?php echo site_url()?>/gybc/cron/updateYoutubePosts" method="POST">
				<input type="hidden" name="redirect" value="<?php echo get_admin_url();?>admin.php?page=youtube_admin">
				<div class="form-footer group">
					<input type="submit" name="getYouTubeVideos" value="Get YouTube Videos">
				</div>
			</form>
		</div>

	</div>
</div>