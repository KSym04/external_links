<?php
/*
Plugin Name: External Links for Osclass
Plugin URI: https://www.dopethemes.com/downloads/external-links-osclass/?utm_source=oc-plugins&utm_campaign=plugin-uri&utm_medium=oc-dash
Description: This plugin automatically convert text urls and all valid external links to anchor tag with lots of an optional features.
Version: 1.0.4
Author: DopeThemes
Author URI: https://www.dopethemes.com/?utm_source=oc-plugins&utm_campaign=plugin-uri&utm_medium=oc-dash
Plugin update URI: external-links-for-osclass
Short Name: external_links
Support URI: https://www.dopethemes.com/contact-us/?utm_source=oc-plugins&utm_campaign=plugin-uri&utm_medium=oc-dash
*/

if( ! defined( 'ABS_PATH' ) ) {
	exit;
}

/**
 * external_links_call_after_install
 *
 * Set plugin defaults data after installation
 *
 * @since 	1.0.0
 * @return	void
 */
function external_links_call_after_install() {
	osc_set_preference( 'new_window', '1', 'plugin-external_links', 'BOOLEAN' );
	osc_set_preference( 'add_no_follow', '1', 'plugin-external_links', 'BOOLEAN' );
	osc_set_preference( 'add_no_opener', '1', 'plugin-external_links', 'BOOLEAN' );
	osc_set_preference( 'auto_convert_emails', '0', 'plugin-external_links', 'BOOLEAN' );
}
osc_register_plugin( osc_plugin_path( __FILE__ ), 'external_links_call_after_install' );

/**
 * external_links_call_after_uninstall
 *
 * Remove all plugin data upon uninstallation
 *
 * @since	1.0.0
 * @return 	void
 */
function external_links_call_after_uninstall() {
	osc_delete_preference( 'new_window', 'plugin-external_links' );
	osc_delete_preference( 'add_no_follow', 'plugin-external_links' );
	osc_delete_preference( 'add_no_opener', 'plugin-external_links' );
	osc_delete_preference( 'auto_convert_emails', 'plugin-external_links' );
}
osc_add_hook( osc_plugin_path( __FILE__ ) . "_uninstall", 'external_links_call_after_uninstall' );

/**
 * external_links_actions
 *
 * Function that validates and process the saving of data
 *
 * @since	1.0.0
 * @return void
 */
function external_links_actions() {
	if ( Params::getParam( 'file' ) !== 'external_links/admin/settings.php' ) {
		return;
	}

	// Save settings
	if ( Params::getParam( 'option' ) === 'settings_saved' ) {
		// set data
    	osc_set_preference( 'new_window', Params::getParam( "new_window", false, false ), 'plugin-external_links', 'BOOLEAN' );
		osc_set_preference( 'add_no_follow', Params::getParam( "add_no_follow", false, false ), 'plugin-external_links', 'BOOLEAN' );
		osc_set_preference( 'add_no_opener', Params::getParam( "add_no_opener", false, false ), 'plugin-external_links', 'BOOLEAN' );
    	osc_set_preference( 'auto_convert_emails', Params::getParam( "auto_convert_emails", false, false ), 'plugin-external_links', 'BOOLEAN' );

    	// return message
    	osc_add_flash_ok_message( __( 'Settings saved successfully', 'external_links' ), 'admin' );
    	osc_redirect_to( osc_admin_render_plugin_url( 'external_links/admin/settings.php' ) );
	}
}
osc_add_hook( 'init_admin', 'external_links_actions' );

/**
 * external_links_admin
 *
 * Create admin pages of our plugin
 *
 * @since	1.0.0
 * @return  void
 */
function external_links_admin() {
	osc_admin_render_plugin( 'external_links/admin/settings.php' );
}
osc_add_hook( osc_plugin_path( __FILE__ ) . "_configure", 'external_links_admin' );

/**
 * external_links_admin_menu
 *
 * Add menu inside Osclass admin panel
 *
 * @since	1.0.0
 * @return  void
 */
function external_links_admin_menu() {
	osc_admin_menu_plugins( 'External Links', osc_admin_render_plugin_url( 'external_links/admin/settings.php' ), 'external_links_submenu' );
}
osc_add_hook( 'admin_menu_init', 'external_links_admin_menu' );

/**
 * external_links_admin_style
 *
 * Load styling inside Osclass admin backend
 *
 * @since	1.0.0
 * @return  void
 */
function external_links_admin_style() {
	osc_enqueue_style( 'external_links-style', osc_plugin_url( __FILE__ ) . 'assets/css/style.css' );
}
osc_add_hook( 'init_admin', 'external_links_admin_style' );

/**
 * external_links_make_url_clickable_cb
 *
 * Convert url string to make url clickable
 *
 * @since 1.0.0
 * @access public
 * @return string
 */
function external_links_make_url_clickable_cb( $matches ) {
	$ret = NULL;
	$url = $matches[2];

  	if ( empty( $url ) ) {
    	return $matches[0];
  	}

  	if ( in_array( substr( $url, -1 ), array( '.', ',', ';', ':' ) ) === true ) {
    	$ret = substr( $url, -1 );
		$url = substr( $url, 0, strlen( $url )-1 );
	}

  	// Check if the url is external
  	$url_data = parse_url( $url );
  	if( $url_data['host'] !== $_SERVER['HTTP_HOST'] ) {
		$data_attr = array();

		// target attributes
    	if( osc_get_preference( 'new_window', 'plugin-external_links' ) === true ) {
    		$data_attr[] = 'target="_blank"';
		}

		// rel attributes
    	if( osc_get_preference( 'add_no_follow', 'plugin-external_links' ) === true ) {
      		$rel_value[] = 'nofollow';
		}

    	if( osc_get_preference( 'add_no_opener', 'plugin-external_links' ) === true ) {
			$rel_value[] = 'noopener';
		}

		if( $rel_value ) {
			$data_attr[] = sprintf( 'rel="%s"', implode( ' ', $rel_value ) );
		}

		// format attributes
		$finalize_attr = implode( ' ', $data_attr );
	}

	return sprintf( '%1$s <a href="%2$s" %3$s>%4$s</a> %5$s', $matches[1], $url, $finalize_attr, $url, $ret );
}

/**
 * external_links_make_web_ftp_clickable_cb
 *
 * Convert ftp url string clickable
 *
 * @since 1.0.0
 * @access public
 * @return string
 */
function external_links_make_web_ftp_clickable_cb( $matches ) {
	$ret = NULL;
	$dest = $matches[2];
	$dest = 'http://' . $dest;

	if ( empty( $dest ) ) {
		return $matches[0];
	}

	if ( in_array( substr( $dest, -1 ), array( '.', ',', ';', ':' ) ) === true ) {
		$ret = substr( $dest, -1 );
		$dest = substr( $dest, 0, strlen( $dest )-1 );
	}

	if( osc_get_preference( 'new_window', 'plugin-external_links' ) === true ) {
		$target_value = 'target="_blank"';
	}

	return sprintf( '%1s<a href="%2s" %3s>%4s</a>%5s', $matches[1], $dest, $target_value, $matches[2], $ret );
}

/**
 * external_links_make_email_clickable_cb
 *
 * Convert email string to mailto:
 *
 * @since 1.0.0
 * @access public
 * @return string
 */
function external_links_make_email_clickable_cb( $matches ) {
	$email = $matches[2] . '@' . $matches[3];
	return sprintf( '%1s<a href="mailto:%2s">%3s</a>', $matches[1], $email, $email );
}

/**
 * external_links_make_clickable
 *
 * Process links or any valid url clickable
 *
 * @since 1.0.0
 * @access public
 * @return string
 */
function external_links_make_clickable( $ret ) {
	// convert txt urls
	$ret = preg_replace_callback( '#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'external_links_make_url_clickable_cb', $ret );

	// convert ftp
	$ret = preg_replace_callback( '#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'external_links_make_web_ftp_clickable_cb', $ret );

	// convert emails
	if( osc_get_preference( 'auto_convert_emails', 'plugin-external_links' ) === true ) {
		$ret = preg_replace_callback( '#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'external_links_make_email_clickable_cb', $ret );
	}

  	$ret = preg_replace( "#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret );
  	$ret = trim( $ret );

	return $ret;
}
