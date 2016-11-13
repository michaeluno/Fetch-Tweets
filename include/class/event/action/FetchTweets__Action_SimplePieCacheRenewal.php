<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Renews transients.
 * 
 * @since       2.5.0
 * @action      add             
 * @action      schedule|add    
 */
class FetchTweets__Action_SimplePieCacheRenewal extends FetchTweets__Action_Base {
    
    protected $_sActionName = 'fetch_tweets_action_simplepie_renew_cache';

    /**
     * Performs the cache renewal.
     */
    protected function doAction( /* $_asURLs */ ) {
        
        $_aParams  = func_get_args();
        $_asURLs   = $_aParams[ 0 ];
        
        // Setup Caches
        $_oFeed = new FetchTweets_SimplePie();

        // Set urls
        $_oFeed->set_feed_url( $_asURLs );    
        
        // this should be set after defining $_asURLs
        $_oFeed->set_cache_duration( 0 );    // 0 seconds, means renew the cache right away.
    
        // Set the background flag to True so that it won't trigger the event action recursively.
        $_oFeed->setBackground( true );
        $_oFeed->init();            
        
    }

}
