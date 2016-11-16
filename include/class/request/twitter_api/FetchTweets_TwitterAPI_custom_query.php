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
class FetchTweets_TwitterAPI_custom_query extends FetchTweets_TwitterAPI_Base {
    
    /**
     * Not setting path here because the type of requests of a custom query can vary 
     * so a fixed path cannot be set.
     */
    protected $_aRateLimitPath = array();
    
    protected $_aTargetElementPath = array();
    
    /**
     * @return      array
     * @since       2.5.0
     */
    public function get() {
        
        $_sResponseKey      = $this->getElement( $this->_aArguments, array( 'response_key' ), '' );
        $this->_aTargetElementPath = $this->getStringIntoArray( $_sResponseKey, ',' );
        return parent::get();

    }
    
    /**
     * @return      string
     */
    protected function _getRequestURI() {
        return $this->getElement( $this->_aArguments, array( 'custom_query' ), '' );
    }    
    
}
