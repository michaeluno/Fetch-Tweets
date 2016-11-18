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
         
        $this->___scheduleBackgroundCacheRemoval();
         
    }
        /**
         * 
         * @return      boolean     True if scheduled. Otherwise, false.
         */
        private function ___scheduleBackgroundCacheRemoval() {
                
            $_aArguments = array();
            if ( wp_next_scheduled( $this->_sActionName, $_aArguments ) ) {
                return false; 
            }
            
            $_oOption   = FetchTweets_Option::getInstance();
            $_aInterval = $_oOption->get( 
                array( 'cache_settings', 'clearing_inteval' ),
                array(
                    'size'     => 7,
                    'unit'     => 86400,            
                )
            );            
            $_iInterval = ( integer ) $this->getElement( $_aInterval, 'size' )
                * ( integer ) $this->getElement( $_aInterval, 'unit' );
            $_bCancelled = wp_schedule_single_event( 
                time() + $_iInterval, 
                $this->_sActionName, // the FetchTweets_Event class will check this action hook and executes it with WP Cron.
                $_aArguments // must be enclosed in an array.
            );          
            return false !== $_bCancelled;
            
        }

    
    /**
     * Deletes expired caches.
     */
    protected function _doAction( /* */ ) {
        
        $_oHTTPRequestTable = new FetchTweets_DatabaseTable_ft_http_requests;
        $_oHTTPRequestTable->deleteExpired();

    }

}
