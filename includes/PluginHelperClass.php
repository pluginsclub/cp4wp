<?php

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

// Add rating stars to the plugin row in the Plugins page.

add_filter( 'plugin_row_meta', 'pluginsclub_custom_plugin_links_cp4wp_links', 10, 2 );

function pluginsclub_custom_plugin_links_cp4wp_links( $links, $file ) {
  if ( strpos( $file, 'cp4wp' ) !== false ) {
    $stars = esc_html__( 'Rate this plugin:', 'cp4wp' );

		$stars .= "<span class='pluginsclub-rating-stars'>";
		for ( $i = 1; $i <= 5; $i++) {
			$stars .= "<a href='https://wordpress.org/support/plugin/cp4wp/reviews/?filter=5#new-post' target='_blank'><span class='dashicons dashicons-star-filled'></span></a>";
		}
		$stars .= "<span>";

		$links[] = $stars;
  }
  
  return $links;
}

// Enqueue CSS and JS files
add_action( 'admin_enqueue_scripts', 'pluginclub_enqueue_scripts_for_plugin_cp4wp' );

function pluginclub_enqueue_scripts_for_plugin_cp4wp() {
  wp_enqueue_style( 'cp4wp-plugins-page-style', plugin_dir_url( __FILE__ ) . '/css/plugins.min.css' );
  wp_enqueue_script( 'cp4wp-plugins-page-script', plugin_dir_url( __FILE__ ) . '/js/plugins.min.js', array( 'jquery' ), '1.0', true );
}

