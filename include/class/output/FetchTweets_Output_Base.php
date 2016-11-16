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
 * Handles rendering outputs.
 * 
 * @since             2.5.0
 */
abstract class FetchTweets_Output_Base extends FetchTweets_PluginUtility {
   
    protected $_oOptioun;
   
    protected $_aArguments = array();
    
    /**
     * Sets up properties.
     * @since       2.5.0
     */
    public function __construct( $aArguments ) {
        $this->_oOption    = FetchTweets_Option::getInstance();
        $this->_aArguments = $this->_getArguments( $aArguments );
    }
    
    /**
     * Returns the arguments.
     * 
     * Handle formatting here.
     * @return      array
     */
    protected function _getArguments( $aArguments ) {
        return $aArguments + $this->_aArguments;
    }
    
    /**
     * @return      void
     */
    public function render() {
        echo $this->get();
    }
        
    /**
     * @return      string
     */
    public function get() {
        return '';
    }

}
