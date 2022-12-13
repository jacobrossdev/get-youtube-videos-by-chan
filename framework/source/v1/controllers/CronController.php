<?php
namespace GYBC;

class CronController extends Controller{

	protected $private;

	public function __construct(){
		
		parent::__construct();
	}

	// Controller Views
	// 
	public function testGET(){
		die('fire');
	}
	
	public function updateYoutubePostsByCronGET(){

		$pageToken = get_option( 'pageToken', $default = false );
		$YoutubePosts = new YoutubePosts;
		$YoutubePosts->updateYoutubePostsByCron();
		die();
	}

	public function updateYoutubePostsPOST(){
		$redirect = $_POST['redirect'];
		$pageToken = get_option( 'pageToken', $default = false );
		$YoutubePosts = new YoutubePosts;
		$YoutubePosts->updateYoutubePostsByCron();
		wp_redirect($redirect);exit;
	}
/*
	public function updateYoutubePostsPOST(){
		$pageToken = false;
		if(isset($_POST['pageToken'])){
			$pageToken = $_POST['pageToken'];
		}
		$YoutubePosts = new YoutubePosts;
		$YoutubePosts->updateVideos($pageToken);
		
	}*/
}