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
class FetchTweets_TwitterAPI_feed extends FetchTweets_TwitterAPI_Base {
    
    protected $_aRateLimitPath = array();
                    
    protected $_aTargetElementPath = array();
    
    /**
     * @return      string
     */
    protected function _getRequestURI() {
        return $this->getElement( $this->_aArguments, array( 'json_url' ), '' );
    }
    
}
