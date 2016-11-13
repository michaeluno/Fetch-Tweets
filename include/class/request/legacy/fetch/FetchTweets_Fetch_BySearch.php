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
 * Provides methods to fetch tweets by search.
 * 
 * @since            2
 */
abstract class FetchTweets_Fetch_BySearch extends FetchTweets_Fetch_ByTag {
    
    /**
     * Performs a search Twitter API request.
     * 
     * @see         https://dev.twitter.com/docs/api/1.1/get/search/tweets
     * @ramark      This request type does not support the 'include_rts' option. The Twitter API does not support it.
     * @param       string      $sKeyword
     * @param       integer     $_deprecated        Deprecated. As of v1.3.4, request will be performed with the maximum count so that the caches will be reused for ones with lesser counts.
     * @param       string      $sLang              the language slug.  Default `en`
     * @param       string      $sUntil     
     * @param       string      $sGeoCode
     * @param       integer     $iCacheDuration     Default: `600`
     */ 
    protected function getTweetsBySearch( $sKeyword, $_deprecated=null, $sLang='en', $sResultType='mixed', $sUntil='', $sGeoCode='', $iCacheDuration=600 ) {

        $_sRequestURI = add_query_arg(
            array_filter( 
                array(
                    'q'                 => ( string ) urlencode_deep( trim( $sKeyword ) ),
                    'result_type'       => $sResultType,    // 'mixed', 'recent', or 'popular'                    
                    'count'             => 100, // set the maximum
                    'lang'              => 'none'  === $sLang   ? '' : $sLang,
                    'until'             => empty( $sUntil )     ? '' : $sUntil,
                    'geocode'           => empty( $sGeoCode )   ? '' : $sGeoCode,
                    'include_entities'  => '1',
                )
            ),
            'https://api.twitter.com/1.1/search/tweets.json'
        );
        return $this->doAPIRequest_Get( 
            $_sRequestURI, 
            'statuses', 
            $iCacheDuration,
            array(
                'search', 
                '/search/tweets',
            )
        );
                    
    }
    
}
