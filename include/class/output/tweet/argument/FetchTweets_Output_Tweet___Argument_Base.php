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
 * A base class to for classes which provide methods to retrieve tweet output arguments.
 * 
 * @since       2.5.0
 */
class FetchTweets_Output_Tweet___Argument_Base extends FetchTweets_PluginUtility {
    
    protected $_oOption;
    
    protected $_aArguments = array();
        
    /**
     * Sets up properties.
     */
    public function __construct( $aArguments ) {
        $this->_oOption    = FetchTweets_Option::getInstance();
        $this->_aArguments = $this->getAsArray( $aArguments );
    }

    /**
     * @return      array
     */
    public function getDefaults() {
        return $this->_oOption->aOptions[ 'default_values' ]  // user saved options
            + $this->_oOption->aStructure_DefaultParams  // class-defined option structure
            + $this->_oOption->aStructure_DefaultTemplateOptions; // class-defined template option structure
    }
    
    /**
     * Returns formatted arguments.
     * @return      array
     */
    public function get() {
        return $this->_aArguments;
    }
       
}
