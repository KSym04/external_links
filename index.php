<?php
/*
Plugin Name: External Links for Osclass
Plugin URI: http://www.dopethemes.com/plugins/external-links-for-osclass/
Description: This plugin convert text URLs to hyperlinks and adds rel=&quot;nofollow&quot; and target=&quot;_blank&quot;, for all the external links of your website item post.
Version: 1.0.1
Author: DopeThemes
Author URI: http://www.dopethemes.com/
Plugin update URI: external-links
Short Name: external_links
Support URI: http://www.dopethemes.com/contact-us/
*/

// == INSTALLATION == //
/**
 * Set some defaults on after installation
 */
function external_links_call_after_install() {
  osc_set_preference( 'new_tab', '', 'plugin-external_links', 'STRING' );
}

/**
 * Delete preferences value after uninstall
 */
function external_links_call_after_uninstall() {
  osc_delete_preference( 'new_tab', 'plugin-external_links' );
}

/**
 * Save settings
 */
function external_links_actions() {
  if ( Params::getParam( 'file' ) != 'external_links/admin.php' ) {
    return '';
  }

  // Save settings
  if ( Params::getParam( 'option' ) == 'settings_saved' ) {
    // set data
    osc_set_preference( 'new_tab', Params::getParam( "new_tab", false, false ), 'plugin-external_links', 'STRING' );

    // return message
    osc_add_flash_ok_message( __( 'Settings saved.', 'external_links' ), 'admin' );
    osc_redirect_to( osc_admin_render_plugin_url( 'external_links/admin.php' ) );
  }
}
osc_add_hook( 'init_admin', 'external_links_actions' );

/**
 * Admin page
 */
function external_links_admin() {
  osc_admin_render_plugin( 'external_links/admin.php' );
}

/**
 * Include on plugin submenu
 */
function external_links_admin_menu() {
  osc_admin_menu_plugins( 'External Links Settings', osc_admin_render_plugin_url( 'external_links/admin.php' ), 'external_links_submenu' );
}

/**
 * Load some style on our admin panel
 */
function external_links_admin_style() {
  osc_enqueue_style( 'external_links-style', osc_plugin_url( __FILE__ ) . 'assets/css/style.css' );
}
osc_add_hook( 'init_admin', 'external_links_admin_style' );

// == FUNCTIONS == //
/**
 * Convert url string to make url clickable
 *
 * @since 1.0.0
 * @access public
 * @return string
 */
function external_links_make_url_clickable_cb( $matches ) {

  $ret = NULL;
  $url = $matches[2];

  if ( empty( $url ) )
    return $matches[0];

  if ( in_array( substr( $url, -1 ), array( '.', ',', ';', ':' ) ) === true ) {
    $ret = substr( $url, -1 );
    $url = substr( $url, 0, strlen( $url )-1 );
  }

  // Check if the url is external
  $url_data = parse_url( $url );
  if( $url_data['host'] != $_SERVER['HTTP_HOST'] ) {
    $url_follow = 'rel="nofollow" target="_blank"';
  }

  return $matches[1] . "<a href=\"$url\" ".$url_follow .">$url</a>" . $ret;
}

/**
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

  if ( empty( $dest ) )
    return $matches[0];

  if ( in_array( substr( $dest, -1 ), array( '.', ',', ';', ':' ) ) === true ) {
    $ret = substr( $dest, -1 );
    $dest = substr( $dest, 0, strlen( $dest )-1 );
  }

  return $matches[1] . "<a href=\"$dest\" target=\"_blank\" rel=\"nofollow\">$dest</a>" . $ret;
}

/**
 * Convert email string to mailto:
 *
 * @since 1.0.0
 * @access public
 * @return string
 */
function external_links_make_email_clickable_cb( $matches ) {
  $email = $matches[2] . '@' . $matches[3];
  return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}

/**
 * Process links or any valid url clickable
 *
 * @since 1.0.0
 * @access public
 * @return string
 */
function external_links_make_clickable( $ret ) {

  $ret = preg_replace_callback( '#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'external_links_make_url_clickable_cb', $ret );
  $ret = preg_replace_callback( '#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'external_links_make_web_ftp_clickable_cb', $ret );
  $ret = preg_replace_callback( '#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'external_links_make_email_clickable_cb', $ret );
  $ret = preg_replace( "#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret );
  $ret = trim( $ret );

  return $ret;
}

// == HOOKS INSTALLATION AND PLUGIN REGISTRATION == //
osc_register_plugin( osc_plugin_path( __FILE__ ), 'external_links_call_after_install' );
osc_add_hook( osc_plugin_path( __FILE__ )."_uninstall", 'external_links_call_after_uninstall' );
osc_add_hook( osc_plugin_path( __FILE__ )."_configure", 'external_links_admin' );
osc_add_hook( 'admin_menu_init', 'external_links_admin_menu' );
