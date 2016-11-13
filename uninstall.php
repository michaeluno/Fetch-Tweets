<?php
/**
 * Cleans up the plugin options.
 *    
 * @package      Fetch Tweets
 * @copyright    Copyright (c) 2013-2015, <Michael Uno>
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        2.4.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/* 
 * Plugin specific constant. 
 * We are going to load the main file to get the registry class. And in the main file, 
 * if this constant is set, it will return after declaring the registry class.
 **/
if ( ! defined( 'DOING_UNINSTALL' ) ) {
    define( 'DOING_UNINSTALL', true  );
}

/**
 * Set the main plugin file name here.
 */
$_sMaingPluginFileName  = 'fetch-tweets.php';
if ( file_exists( dirname( __FILE__ ). '/' . $_sMaingPluginFileName ) ) {
   include( $_sMaingPluginFileName );
}

if ( ! class_exists( 'FetchTweets_Commons' ) ) {
    return;
}

// Delete the plugin option
$_oOption  = FetchTweets_Option::getInstance();
if ( $_oOption->get( 'delete', 'delete_upon_uninstall' ) ) {
    $_oOption->delete();
    $_oTable  = new FetchTweets_DatabaseTable_ft_http_requests;
    $_oTable->uninstall();        
    $_oTable  = new FetchTweets_DatabaseTable_ft_tweets;
    $_oTable->uninstall();            
}

// Delete transients
$_aPrefixes = array(
    FetchTweets_Commons::TransientPrefix, // the plugin transients
    'apf_',      // the admin page framework transients
);
foreach( $_aPrefixes as $_sPrefix ) {
    if ( ! $_sPrefix ) { continue; }
    $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_%{$_sPrefix}%' )" );
    $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_timeout_%{$_sPrefix}%' )" );    
}
    