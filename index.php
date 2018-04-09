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
 * _external_links_url_checker
 *
 * Check if url is not same as host
 *
 * @since 1.0.0
 * @access private
 *
 * @param array
 * @return string
 */
function _external_links_url_checker( $url ) {
	$url_data = parse_url( $url );
	if( $url_data['host'] !== $_SERVER['HTTP_HOST'] ) {
	  $data_attr = array();
	  $rel_value = array();

	  // target attributes
	  if( osc_get_preference( 'new_window', 'plugin-external_links' ) == true ) {
		$data_attr[] = 'target="_blank"';
	  }

	  // rel attributes
	  if( osc_get_preference( 'add_no_follow', 'plugin-external_links' ) == true ) {
		$rel_value[] = 'nofollow';
	  }

	  if( osc_get_preference( 'add_no_opener', 'plugin-external_links' ) == true ) {
		$rel_value[] = 'noopener';
	  }

	  if( $rel_value ) {
		$data_attr[] = sprintf( 'rel="%s"', implode( ' ', $rel_value ) );
	  }

	  // format attributes
	  return $finalize_attr = implode( ' ', $data_attr );
  }
}

/**
 * _external_link_osclass_make_url_clickable_cb
 *
 * Callback to convert URI match to HTML A element.
 *
 * @since 1.0.0
 * @access private
 *
 * @param array
 * @return string
 */
function _external_link_osclass_make_url_clickable_cb( $matches ) {
	$url = $matches[2];

	if ( ')' == $matches[3] && strpos( $url, '(' ) ) {
		// If the trailing character is a closing parethesis, and the URL has an opening parenthesis in it, add the closing parenthesis to the URL.
		// Then we can let the parenthesis balancer do its thing below.
		$url .= $matches[3];
		$suffix = '';
	} else {
		$suffix = $matches[3];
	}

	// Include parentheses in the URL only if paired
	while ( substr_count( $url, '(' ) < substr_count( $url, ')' ) ) {
		$suffix = strrchr( $url, ')' ) . $suffix;
		$url = substr( $url, 0, strrpos( $url, ')' ) );
	}

	$url = osc_sanitize_url( $url );
	if ( empty( $url ) ) {
		return $matches[0];
	}

	$finalize_attr = _external_links_url_checker( $url );
	return $matches[1] . "<a href=\"$url\" $finalize_attr>$url</a>" . $suffix;
}

/**
 * _external_links_osclass_make_web_ftp_clickable_cb
 *
 * Callback to convert URL match to HTML A element.
 *
 * @since 1.0.0
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with URL address.
 */
function _external_links_osclass_make_web_ftp_clickable_cb( $matches ) {
	$ret = '';
	$dest = $matches[2];
	$dest = 'http://' . $dest;

	// removed trailing [.,;:)] from URL
	if ( in_array( substr($dest, -1), array('.', ',', ';', ':', ')') ) === true ) {
		$ret = substr($dest, -1);
		$dest = substr($dest, 0, strlen($dest)-1);
	}

	$dest = osc_sanitize_url( $dest );
	if ( empty( $dest ) ) {
		return $matches[0];
	}

  	// Check if the url is external
  	$finalize_attr = _external_links_url_checker( $dest );
	return $matches[1] . "<a href=\"$dest\" $finalize_attr>$dest</a>$ret";
}

/**
 * _external_links_osclass_make_email_clickable_cb
 *
 * Convert email string to mailto:
 *
 * @since  1.0.0
 * @access private
 *
 * @param  array
 * @return string
 */
function _external_links_osclass_make_email_clickable_cb( $matches ) {
	$email = $matches[2] . '@' . $matches[3];
	return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
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
function external_links_make_clickable( $text ) {
	$r = '';
	$textarr = preg_split( '/(<[^<>]+>)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // split out HTML tags
	$nested_code_pre = 0; // Keep track of how many levels link is nested inside <pre> or <code>
	foreach ( $textarr as $piece ) {

		if ( preg_match( '|^<code[\s>]|i', $piece ) || preg_match( '|^<pre[\s>]|i', $piece ) || preg_match( '|^<script[\s>]|i', $piece ) || preg_match( '|^<style[\s>]|i', $piece ) )
			$nested_code_pre++;
		elseif ( $nested_code_pre && ( '</code>' === strtolower( $piece ) || '</pre>' === strtolower( $piece ) || '</script>' === strtolower( $piece ) || '</style>' === strtolower( $piece ) ) )
			$nested_code_pre--;

		if ( $nested_code_pre || empty( $piece ) || ( $piece[0] === '<' && ! preg_match( '|^<\s*[\w]{1,20}+://|', $piece ) ) ) {
			$r .= $piece;
			continue;
		}

		// Long strings might contain expensive edge cases ...
		if ( 10000 < strlen( $piece ) ) {
			// ... break it up
			foreach ( _split_str_by_whitespace( $piece, 2100 ) as $chunk ) { // 2100: Extra room for scheme and leading and trailing paretheses
				if ( 2101 < strlen( $chunk ) ) {
					$r .= $chunk; // Too big, no whitespace: bail.
				} else {
					$r .= make_clickable( $chunk );
				}
			}
		} else {
			$ret = " $piece "; // Pad with whitespace to simplify the regexes

			$url_clickable = '~
				([\\s(<.,;:!?])                                        # 1: Leading whitespace, or punctuation
				(                                                      # 2: URL
					[\\w]{1,20}+://                                # Scheme and hier-part prefix
					(?=\S{1,2000}\s)                               # Limit to URLs less than about 2000 characters long
					[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]*+         # Non-punctuation URL character
					(?:                                            # Unroll the Loop: Only allow puctuation URL character if followed by a non-punctuation URL character
						[\'.,;:!?)]                            # Punctuation URL character
						[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]++ # Non-punctuation URL character
					)*
				)
				(\)?)                                                  # 3: Trailing closing parenthesis (for parethesis balancing post processing)
			~xS'; // The regex is a non-anchored pattern and does not have a single fixed starting character.
			      // Tell PCRE to spend more time optimizing since, when used on a page load, it will probably be used several times.

			$ret = preg_replace_callback( $url_clickable, '_external_link_osclass_make_url_clickable_cb', $ret );

			$ret = preg_replace_callback( '#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', '_external_links_osclass_make_web_ftp_clickable_cb', $ret );

			if( osc_get_preference( 'auto_convert_emails', 'plugin-external_links' ) == true ) {
				$ret = preg_replace_callback( '#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_external_links_osclass_make_email_clickable_cb', $ret );
			}

			$ret = substr( $ret, 1, -1 ); // Remove our whitespace padding.
			$r .= $ret;
		}
	}

	// Cleanup of accidental links within links
	return preg_replace( '#(<a([ \r\n\t]+[^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i', "$1$3</a>", $r );
}
