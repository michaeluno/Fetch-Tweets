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
 * @action      add             fetch_tweets_filter_twitter_api_response_cache
 * @action      schedule|add    fetch_tweets_action_twitter_api_response_cache_renewal
 */
class FetchTweets__Action_TwitterAPIResponseCacheRenewal extends FetchTweets__Action_Base {
    
    protected $_sActionName = 'fetch_tweets_action_twitter_api_response_cache_renewal';
    
    protected $_iArguments  = 5;

    protected function _construct() {
        
        add_filter( 
            'fetch_tweets_filter_twitter_api_response_cache',  // filter hook name
            array( $this, 'replyToModifyCacheRemainedTime' ), // callback
            10, // priority
            $this->_iArguments // number of parameters
        );            
    }
    
    /**
     * 
     */
    protected function _doAction( /* $_asURL, $_iCacheDuration, $_aArguments, $_sType, $_naTargetElementPath */ ) {
        
        $_aParams               = func_get_args() + array( '', 0, array(), '' );
        $_asURL                 = $_aParams[ 0 ];
        $_iCacheDuration        = $_aParams[ 1 ];
        $_aArguments            = $_aParams[ 2 ];
        $_sType                 = $_aParams[ 3 ];
        $_naTargetElementPath   = $_aParams[ 4 ];

        foreach( $this->getAsArray( $_asURL ) as $_sURL ) {
            
            $_aArguments = array(
                'custom_query'          => $_sURL,
                'cache'                 => $_iCacheDuration,
                'force_caching'         => true,
                'response_key'          => $_naTargetElementPath,    // 2.5.2+   The oEmbed cache modification routine needs this.
            );
            fetchTweets( $_aArguments, false ); // output does not matter 

        }
        
    }
    
    
    /**
     * 
     * @callback        filter      fetch_tweets_filter_twitter_api_response_cache
     */
    public function replyToModifyCacheRemainedTime( $aCache, $iCacheDuration, $aArguments, $sType='wp_remote_get', $naTargetElementPath ) {

        if ( ! isset( $aCache[ 'request_uri' ] ) ) {
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
            $_bScheduled = $this->scheduleWPCronActionOnce( 
                $this->_sActionName, 
                array(
                    $aCache[ 'request_uri' ], 
                    $iCacheDuration,
                    $aArguments,
                    $sType,
                    $naTargetElementPath
                )
            );                  
            if ( $_bScheduled ) {
                new FetchTweets_Event__BackgroundPageload;
            }
 
            // Tell the plugin it is not expired. 
            $aCache[ 'remained_time' ] = time();
            
        } 
        
        return $aCache;
                
    }
        
}
