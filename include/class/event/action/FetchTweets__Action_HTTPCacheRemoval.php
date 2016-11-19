<?php
/**
 * Fetch Tweets
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Removes expired HTTP request caches.
 * 
 * @since       2.5.0
 * @action      schedule|add    fetch_tweets_action_http_cache_removal
 */
class FetchTweets__Action_HTTPCacheRemoval extends FetchTweets__Action_Base {
    
    protected $_sActionName = 'fetch_tweets_action_http_cache_removal';
    
    protected $_iArguments  = 1;

    protected function _construct() {
         
        $this->scheduleWPCronActionOnce( 
            $this->_sActionName, 
            array(), 
            FetchTweets_Option::get( array( 'cache_settings', 'clearing_interval', 'size' ), 7 )
            * FetchTweets_Option::get( array( 'cache_settings', 'clearing_interval', 'unit' ), 86400 )
        );
        
    }
    
    /**
     * Deletes expired caches.
     */
    protected function _doAction( /* */ ) {
        
        $_oHTTPRequestTable = new FetchTweets_DatabaseTable_ft_http_requests;
        $_oHTTPRequestTable->deleteExpired();

    }

}
