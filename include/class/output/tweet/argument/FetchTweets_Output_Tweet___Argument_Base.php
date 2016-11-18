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
     *
     * @return      array
     *     id - The post id. default: null. e.g. 125  or 124, 235
     *     tag - default: null. e.g. php or php, WordPress. In this method this tag is only used to pass the argument to the template filter.
     *  sort - default: descending. Either ascending, descending, or random can be used.
     *     count - default: 20
     *     operator - default: AND. Either AND or IN or NOT IN is used.
     *  q - default: null e.g. WordPress
     *  screen_name - default: null e.g. miunosoft
     *  include_rts - default: 0. Either 1 or 0.
     *  exclude_replies - default: 0. Either 1 or 0.
     *  cache - default: 1200
     *    lang - default: null.  
     *    result_type - default: mixed
     *    list_id - default: null. e.g. 8044403
     *    twitter_media - ( boolean ) determines whether the Twitter media should be displayed or not. Currently only photos are supported by the Twitter API.
     *    external_media - ( boolean ) determines whether the plugin attempts to replace external media links to embedded elements.
     * show_error_on_no_result     - 2.4.7+ default: true
     * apply_template_on_no_result - 2.4.8+ default: true
     * Template options
     *    template - the template slug.
     *    width - 
     *    width_unit - 
     *    height    - 
     *    height_unit - 
     *    avatar_size - default: 48 
     * 
     */
    public function get() {
        return $this->_aArguments;
    }
       
}
