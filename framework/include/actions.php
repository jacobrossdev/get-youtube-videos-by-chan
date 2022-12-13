<?php

if( ! function_exists('print_feedback_function')){

	function print_feedback_function(){

		$string = '<ul>';

		if( isset( $_SESSION['feedback'] ) )
		{
			foreach( $_SESSION['feedback'] as $key=>$feedback )
			{

				if( $key == 'validation' )
				{
					
					foreach($feedback as $fb)
					{
						foreach($fb as $f)
						{
							$string .= '<li><i class="fa fa-close"></i> &nbsp;'.$f.'</li>';
						}
					}
				
				}

				if( $key == 'message' )
				{

					$string .= '<li>'.$feedback.'</li>';
				
				}
			}
		}

		$string .= '</ul>';
		
		echo $string;

		session_destroy();
	}
}
 
add_action('print_feedback', 'print_feedback_function');

/**
 * Registers a new post type
 * @uses $wp_post_types Inserts new post type object into the list
 *
 * @param string  Post type key, must not exceed 20 characters
 * @param array|string  See optional args description above.
 * @return object|WP_Error the registered post type object, or an error object
 */

		
function youtubevideos_scripts_queue(){
	wp_enqueue_script( 'sermons-fitvids',  plugin_dir_url( dirname(__DIR__) ) . '/framework/public/js/fitvids.js', array('jquery') );
	wp_enqueue_script( 'sermons-scripts',  plugin_dir_url( dirname(__DIR__) ) . '/framework/public/js/script.js', array('jquery') );
}

add_action('wp_enqueue_scripts', 'youtubevideos_scripts_queue');


/**
 * Hook it into Wordpress
 */
add_action('admin_menu', 'youtube_menu_pages'); 

/**
 * Place all the add_menu_page functions in here
 */
function youtube_menu_pages(){

	$admin_page_name = 'YouTube Interface';
	add_menu_page( $admin_page_name, $admin_page_name, 'manage_options', 'youtube_admin', 'youtube_admin_page' );

}

/**
 * Admin page function
 */
function youtube_admin_page(){

	$message = NULL;

	$options = array();

	if ( !current_user_can( 'manage_options' ) )  {
	
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );	
	}

	if( isset( $_POST['publish'] ) ){
		// This is code to capture $_POST data if there's a form on  your admin page.
		delete_option('pageToken');
		update_option( 'youtube_options', $_POST );
	}


	$options = get_option('youtube_options');
	
	ob_start(); include dirname(__DIR__) . '/partial/admin.php'; $template = ob_get_clean();

	echo $template;
}


function legacy_videos_post_type_function() {

	$labels = array(
		'name'               => __( 'Videos', 'legacy-child' ),
		'singular_name'      => __( 'Video', 'legacy-child' ),
		'add_new'            => _x( 'Add New Video', 'legacy-child', 'legacy-child' ),
		'add_new_item'       => __( 'Add New Video', 'legacy-child' ),
		'edit_item'          => __( 'Edit Video', 'legacy-child' ),
		'new_item'           => __( 'New Video', 'legacy-child' ),
		'view_item'          => __( 'View Video', 'legacy-child' ),
		'search_items'       => __( 'Search Videos', 'legacy-child' ),
		'not_found'          => __( 'No Videos found', 'legacy-child' ),
		'not_found_in_trash' => __( 'No Videos found in Trash', 'legacy-child' ),
		'parent_item_colon'  => __( 'Parent Video:', 'legacy-child' ),
		'menu_name'          => __( 'Videos', 'legacy-child' ),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => false,
		'description'         => 'description',
		'taxonomies'          => array(),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => null,
		'menu_icon'           => null,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'custom-fields',
			'trackbacks',
			'comments',
			'revisions',
			'page-attributes',
			'post-formats',
		),
	);

	register_post_type( 'videos', $args );
}

add_action( 'init', 'legacy_videos_post_type_function' );
