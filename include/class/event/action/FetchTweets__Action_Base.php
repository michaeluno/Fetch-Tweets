<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Renews transients.
 * 
 * @since       2.5.0
 * @action      add             
 * @action      schedule|add    
 */
class FetchTweets__Action_Base extends FetchTweets_PluginUtility {
    
    /* Protected properties - should be overridden in each extended class. */
    
    protected $_sActionName = '';
    
    protected $_iPriority   = 10;
    
    protected $_iArguments  = 1;
    
    /**
     * Sets up hooks.
     * @since       2.5.0
     */
    public function __construct() {

        if ( ! $this->_sActionName ) {
            return;
        }
    
        add_action(
            $this->_sActionName, // action hook name
            array( $this, 'repyToDoAction' ),
            $this->_iPriority,
            $this->_iArguments
        );

        $this->_construct();
        
    }
        /**
         * Performs the cache renewal.
         * @callback        filter      
         */
        public function repyToDoAction() {
            $_aParameters = func_get_args();
            call_user_func_array(
                array( $this, '_doAction' ),
                $_aParameters
            );
        }
    
    protected function _construct() {}
        

    
    protected function _doAction( /* RESERVED. Use `func_get_args()` to retrieve parameters. */ ) {
    }

 
}
