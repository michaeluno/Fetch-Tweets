<?php
/**
 * Fetch Tweets
 * 
 * Fetches and displays tweets from twitter.com.
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 */

/**
 * Provides methods to get responses with tweet IDs.
 * 
 * @since            2.3
 */
abstract class FetchTweets_Fetch_ByTweetID extends FetchTweets_Fetch_ByCustomRequest {
    
    /**
     * Retrieves tweets by the given tweet IDs.
     * 
     * @see              https://dev.twitter.com/docs/api/1.1/get/statuses/show/%3Aid
     * @since            2.3
     */
    protected function _getResponseByTweetID( $sTweetIDs, $iCacheDuration ) {
        
        $_aTweetIDs = preg_split( "/[,]\s*/", trim( ( string ) $sTweetIDs ), 0, PREG_SPLIT_NO_EMPTY );        
        $_aResponse = array();
        foreach( $_aTweetIDs as $__sTweetID ) {
        
            $_aResponse[] = $this->doAPIRequest_Get( 
                add_query_arg( 
                    array(
                        'id' => $__sTweetID,
                    ), 
                    "https://api.twitter.com/1.1/statuses/show.json" 
                ),  // request URI
                null, 
                $iCacheDuration, 
                array( 
                    'statuses',
                    '/statuses/show/:id'
                )
            );
            
        }
        return $_aResponse;
        
    } 
    
}
