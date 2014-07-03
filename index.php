<?php
/*
Plugin Name: External Links for Osclass
Plugin URI: http://www.dopethemes.com/plugins/external-links-for-osclass/
Description: This plugin convert text URLs to hyperlinks and adds rel=&quot;nofollow&quot; and target=&quot;_blank&quot;, for all the external links of your website item post.
Version: 1.0.0
Author: DopeThemes
Author URI: http://www.dopethemes.com/
Plugin update URI: http://www.dopethemes.com/files/osclass/plugins/external_links/update.php
Short Name: external_links
Support URI: http://www.dopethemes.com/contact-us/
*/

// == FUNCTION == //
function external_links_make_url_clickable_cb( $matches ) {
  $ret = '';
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

function external_links_make_web_ftp_clickable_cb( $matches ) {
  $ret = '';
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

function external_links_make_email_clickable_cb( $matches ) {
  $email = $matches[2] . '@' . $matches[3];
  return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}

function external_links_make_clickable( $ret ) {
  $ret = ' ' . $ret;
  $ret = preg_replace_callback( '#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'external_links_make_url_clickable_cb', $ret );
  $ret = preg_replace_callback( '#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'external_links_make_web_ftp_clickable_cb', $ret );
  $ret = preg_replace_callback( '#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'external_links_make_email_clickable_cb', $ret );
  $ret = preg_replace( "#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret );
  $ret = trim( $ret );
  return $ret;
}
