<?php

 /**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://jacobrossdev.com
 * @since             4.9.8
 * @package           get-videos-by-youtube-chan
 *
 * @wordpress-plugin
 * Plugin Name:       Get Youtube Videos
 * Plugin URI:        http://jacobrossdev.com
 * Description:       A plugin that lists the your videos in the Wordpress videos post type.
 * Version:           1.1.2
 * Author:            Jacob Ross Web & App Development
 * Author URI:        http://jacobrossdev.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       get-videos-by-youtube-chan
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

include 'config.php';

global $plugin_root;

$plugin_root = __DIR__;

require 'framework/wpmvc.php';