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
class FetchTweets_TwitterAPI_screen_name extends FetchTweets_TwitterAPI_Base {
    
    protected $_aRateLimitPath = array( 'statuses', '/statuses/user_timeline' );
                   
    protected $_aTargetElementPath = array();
    
    /**
     * @remark      The `screen_name` argument supports multiple screen names to be passed.
     * @since       2.5.0
     */
    public function get() {
        return $this->_getMultipleSetsByArgument( 'screen_name' );        
    }
        /**
         * Retrieves tweets multiple times by the given argument name.
         * 
         * Some arguments supports multiple items delimited by commas such as tweet IDs and screen names.
         * @since       2.5.0
         * @return      array
         */
        protected function _getMultipleSetsByArgument( $sArgumentName ) {
            $_aTweets       = array();
            $_sItems        = $this->getElement( $this->_aArguments, array( $sArgumentName ), '' );
            $_aItems        = $this->getStringIntoArray( $_sItems, ',' );
            foreach( $_aItems as $_sItem ) {
                $this->_aArguments[ $sArgumentName ] = $_sItem;
                $_aTweets = array_merge( parent::get(), $_aTweets );
            }
            return $_aTweets;            
        }    
        
    /**
     * @return      string
     * @since       2.5.0
     */
    protected function _getRequestURI() {
        
        $_sRequestURI       = add_query_arg(
            array(
                'screen_name'       => $this->getElement( $this->_aArguments, array( 'screen_name' ), '' ),
                'count'             => $this->getElement( $this->_aArguments, array( 'count' ), 100 ),  // The maximum number is 200 but it likely exceeds 1mb and causes MySQL error
                'include_rts'       => ( integer ) ( boolean ) $this->getElement( $this->_aArguments, array( 'include_rts' ), false ),
                'exclude_replies'   => ( integer ) ( boolean ) $this->getElement( $this->_aArguments, array( 'exclude_replies' ), false ),
            ),
            'https://api.twitter.com/1.1/statuses/user_timeline.json'
        );
        return $_sRequestURI;
        
    }
    

    
}
