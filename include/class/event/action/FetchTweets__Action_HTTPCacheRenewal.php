<?php
/**
 * Fetch Tweets
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Renews HTTP request caches in the background.
 * 
 * @since       2.5.0
 * @action      add             fetch_tweets_filter_http_response_cache
 * @action      schedule|add    fetch_tweets_action_http_cache_renewal
 */
class FetchTweets__Action_HTTPCacheRenewal extends FetchTweets__Action_Base {
    
    protected $_sActionName = 'fetch_tweets_action_http_cache_renewal';
    
    protected $_iArguments  = 4;

    protected function _construct() {
        
        add_filter( 
            'fetch_tweets_filter_http_response_cache',  // filter hook name
            array( $this, 'replyToModifyCacheRemainedTime' ), // callback
            10, // priority
            4 // number of parameters
        );            
    }
    
    /**
     * 
     */
    protected function _doAction( /* $_asURL, $_iCacheDuration, $_aArguments, $_sType */ ) {
        
        $_aParams        = func_get_args() + array( '', 0, array(), '' );
        $_asURL          = $_aParams[ 0 ];
        $_iCacheDuration = $_aParams[ 1 ];
        $_aArguments     = $_aParams[ 2 ];
        $_sType          = $_aParams[ 3 ];
        
        $_aClass         = array(
            'wp_remote_get'     => 'FetchTweets_HTTP_Get',
            'wp_remote_post'    => 'FetchTweets_HTTP_Post',
            'wp_remote_request' => 'FetchTweets_HTTP_Request',
            'wp_remote_delete'  => 'FetchTweets_HTTP_Delete',
        );
        $_sClassName = isset( $_aClass[ $_sType ] ) 
            ? $_aClass[ $_sType ]
            : $_aClass[ 'wp_remote_get' ];
        
        $_aURLs = $this->___getAPIRequestsDropped( $_asURL );
        if ( empty( $_aURLs ) ) {
            return;
        }
        
        $_oHTTP          = new $_sClassName(
            $_aURLs, 
            $_iCacheDuration, 
            $_aArguments
        );
        $_oHTTP->deleteCache();
        $_oHTTP->get();

    }
        /**
         * @return      array
         */
        private function ___getAPIRequestsDropped( $asURL ) {
            
            $_aNew = array();
            foreach ( $this->getAsArray( $asURL ) as $_sURL ) {
                if ( $this->___isTwitterAPIRequest( $_sURL ) ) {
                    continue;
                }
                $_aNew[] = $_sURL;
            }
            return $_aNew;
            
        }
            /**
             * Checks if the given URI is for Twitter API.
             * 
             * @since            2.1
             */
            private function ___isTwitterAPIRequest( $sURL ) {
                return 'api.twitter.com' === parse_url( $sURL,  PHP_URL_HOST );
            }
    
    /**
     * 
     * @callback        filter      fetch_tweets_filter_http_response_cache
     */
    public function replyToModifyCacheRemainedTime( $aCache, $iCacheDuration, $aArguments, $sType='wp_remote_get' ) {

        // Do nothing for API requests. It is handled in a separate routine.
        if ( $this->___isTwitterAPIRequest( $aCache[ 'request_uri' ] ) ) {
            return $aCache;
        }
      
        // If the cache duration is explicitly set to `0`, do not schedule a background renewal task.
        if ( 0 === $iCacheDuration ) {
            $aCache[ 'remained_time' ] = 0;
            return $aCache;
        }
        
        // Check if it is expired.
        if ( 0 >= $aCache[ 'remained_time' ] ) {

            // It is expired. So schedule a task that renews the cache in the background.
            $_bScheduled = $this->___scheduleBackgroundCacheRenewal( 
                $aCache[ 'request_uri' ], 
                $iCacheDuration,
                $aArguments,
                $sType
            );
            
            if ( $_bScheduled ) {
                new FetchTweets_Event__BackgroundPageload;
            }            

            // Tell the plugin it is not expired. 
            $aCache[ 'remained_time' ] = time();
            
        } 
        
        return $aCache;
                
    }
        /**
         * 
         * @return      boolean
         */
        private function ___scheduleBackgroundCacheRenewal( $sURL, $iCacheDuration, $aArguments, $sType ) {
                
            $_aArguments  = array(
                $sURL,
                $iCacheDuration,
                $aArguments,
                $sType
            );
            if ( wp_next_scheduled( $this->_sActionName, $_aArguments ) ) {
                return false; 
            }
            $_bCancelled = wp_schedule_single_event( 
                time(), // now
                $this->_sActionName, // the FetchTweets_Event class will check this action hook and executes it with WP Cron.
                $_aArguments // must be enclosed in an array.
            );          
            return false === $_bCancelled
                ? false
                : true;
            
        }

}
