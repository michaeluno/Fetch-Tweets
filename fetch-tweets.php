<?php
/* 
	Plugin Name:    Fetch Tweets
	Plugin URI:     http://en.michaeluno.jp/fetch-tweets
	Description:    Fetches and displays tweets from twitter.com with the the Twitter REST API v1.1.
	Author:         miunosoft (Michael Uno)
	Author URI:     http://michaeluno.jp
	Version:        2.3.5b06
	Requirements:   PHP 5.2.4 or above, WordPress 3.3 or above.
*/

class FetchTweets_Commons_Base {
    
	const Version        = '2.3.5b06';    // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
	const Name           = 'Fetch Tweets';
	const Description    = 'Fetches and displays tweets from twitter.com with the the Twitter REST API v1.1.';
	const URI            = 'http://en.michaeluno.jp/fetch-tweets';
	const Author         = 'miunosoft (Michael Uno)';
	const AuthorURI      = 'http://en.michaeluno.jp/';
	const Copyright      = 'Copyright (c) 2013-2014, Michael Uno';
	const License        = 'GPL v2 or later';
	const Contributors   = '';
    
}

// Do not load if accessed directly
if ( ! defined( 'ABSPATH' ) ) { return; }

include_once( dirname( __FILE__ ). '/include/class/boot/FetchTweets_Bootstrap.php' );
new FetchTweets_Bootstrap( __FILE__ );

include_once( dirname( __FILE__ ). '/include/function/functions.php' );