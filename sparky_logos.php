<?php
/*
Plugin Name: Sparky Logos
Description: Display your logos with their title and description in gallery or slider
Version: 1.0.0
Author: Vidya
Text Domain: sparky
License: GPLv2 or later
*/
class sparkyLogos {

	public function __construct(){

		define( 'SPARKY_PLUGIN', __FILE__ );

		define( 'SPARKY_PLUGIN_BASENAME', plugin_basename( SPARKY_PLUGIN ) );

		define( 'SPARKY_PLUGIN_NAME', trim( dirname( SPARKY_PLUGIN_BASENAME ), '/' ) );

		define( 'SPARKY_PLUGIN_DIR', untrailingslashit( dirname( SPARKY_PLUGIN ) ) );

		define( 'SPARKY_PLUGIN_URL', untrailingslashit( plugins_url( '', SPARKY_PLUGIN ) ) );

		$prefix = $wpdb->prefix;

		$this->includeSparkyFiles();	
		$this->enqueSparkyFiles();
	}

	public function includeSparkyFiles(){

		require_once(SPARKY_PLUGIN_DIR.'/admin/functions.php');
	}

	public function enqueSparkyFiles(){		

        wp_register_style( 'custom_sl_admin_style', SPARKY_PLUGIN_URL . '/admin/sl-admin-style.css', false, '1.0.0' );
        wp_enqueue_style( 'custom_sl_admin_style' );

        wp_enqueue_script( 'jquery' );

	}

}


new sparkyLogos();