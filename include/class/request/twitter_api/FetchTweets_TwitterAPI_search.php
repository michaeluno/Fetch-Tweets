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
 * Handles Twitter API requests.
 * 
 * @since             2.5.0
 */
class FetchTweets_TwitterAPI_search extends FetchTweets_TwitterAPI_Base {
    
    protected $_aRateLimitPath = array( 'search', '/search/tweets', );
    
    protected $_aTargetElementPath = array( 'statuses' );
    
    /**
     * @return      string
     * @since       2.5.0
     */
    protected function _getRequestURI() {
        
        $_sLanguage         = $this->getElement( $this->_aArguments, array( 'lang' ), '' );
        $_sUntil            = $this->getElement( $this->_aArguments, array( 'until' ), '' );
        $_sGeoCode          = $this->getElement( $this->_aArguments, array( 'geocode' ), '' );
        $_iCount            = $this->getElement( $this->_aArguments, array( 'count' ), 80 );  // The maximum number is 100 but it likely exceeds 1mb and causes MySQL error
                    
        return add_query_arg(
            array_filter( 
                array(
                    'q'                 => ( string ) urlencode_deep( trim( ( string ) $this->getElement( $this->_aArguments, 'q' ) ) ),
                    'result_type'       => $this->getElement( $this->_aArguments, 'result_type' ),    // 'mixed', 'recent', or 'popular'
                    'count'             => $_iCount,
                    'lang'              => 'none'  === $_sLanguage  ? '' : $_sLanguage,
                    'until'             => $_sUntil,
                    'geocode'           => $_sGeoCode,
                    'include_entities'  => '1',
                )
            ),
            'https://api.twitter.com/1.1/search/tweets.json'
        );
        
    }
    
}
