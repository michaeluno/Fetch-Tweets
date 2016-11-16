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
class FetchTweets_TwitterAPI_list_name extends FetchTweets_TwitterAPI_Base {
    
    protected $_aRateLimitPath = array( 'lists', '/lists/list' );
                    
    protected $_aTargetElementPath = array( 'name' );
    
    /**
     * @return      string
     * @see         https://dev.twitter.com/docs/api/1.1/get/lists/statuses
     */
    protected function _getRequestURI() {
        
        $_aQuery = array(
            'screen_name' => $this->getElement( $this->_aArguments, array( 'screen_name' ), '' ),
        );
        return add_query_arg(            
            array_filter( $_aQuery ),   // drop non-true values.
            'https://api.twitter.com/1.1/lists/list.json'
        );
        
    }     
    
    
    
}
