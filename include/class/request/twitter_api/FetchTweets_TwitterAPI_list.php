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
class FetchTweets_TwitterAPI_list extends FetchTweets_TwitterAPI_Base {
    
    protected $_aRateLimitPath = array( 'lists', '/lists/statuses' );
    
    protected $_aTargetElementPath = array();
    
    /**
     * @return      string
     * @see         https://dev.twitter.com/docs/api/1.1/get/lists/statuses
     */
    protected function _getRequestURI() {
        
        $_aQuery = array(
            'list_id'       => $this->getElement( $this->_aArguments, array( 'list_id' ), '' ),
            'count'         => $this->getElement( $this->_aArguments, array( 'count' ), 100 ),    // 200 is the max
            'include_rts'   => ( integer ) ( boolean ) $this->getElement( $this->_aArguments, array( 'include_rts' ), false ),
            
            // (optional)
            'slug'              => $this->getElement( $this->_aArguments, array( 'slug' ) ),
            'owner_screen_name' => $this->getElement( $this->_aArguments, array( 'owner_screen_name' ) ),
            'owner_id'          => $this->getElement( $this->_aArguments, array( 'owner_id' ) ),
            'since_id'          => $this->getElement( $this->_aArguments, array( 'since_id' ) ),
            'max_id'            => $this->getElement( $this->_aArguments, array( 'max_id' ) ),
        );
        return add_query_arg(            
            array_filter( $_aQuery ),   // drop non-true values.
            "https://api.twitter.com/1.1/lists/statuses.json"             
        );
        
    }     
    
    
}
