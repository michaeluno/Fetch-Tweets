<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Adds oEmbed elements for transient (cache) formatting events.
 * 
 * @since       2.5.0
 * @action      add             
 * @action      schedule|add    
 */
class FetchTweets__Action_oEmbedUpdate extends FetchTweets__Action_Base {
    
    protected $_sActionName = 'fetch_tweets_action_transient_add_oembed_elements';

    private $__oBase64;
    
    protected function _construct() {
        $this->___oBase64 = new FetchTweets_Base64;
    }
    
    /**
     * Performs the cache renewal.
     */
    protected function doAction( /* $_sRequestURI */ ) {
        
        $_aParams        = func_get_args();
        $_sRequestURI    = $_aParams[ 0 ];

        $_sTransientKey  = FetchTweets_Commons::TransientPrefix . "_" . md5( $_sRequestURI );

        // Check if the transient is locked
        $_sLockTransient = FetchTweets_Commons::TransientPrefix . '_' . md5( "LockOEm_" . trim( $_sTransientKey ) );    // up to 40 characters, the prefix can be up to 8 characters
        if ( false !== $this->getTransient( $_sLockTransient ) ) {
            return;    // it means the cache is being modified.
        }    
        
        // Set a lock flag transient that indicates the transient is being renewed.
        $this->setTransient(
            $_sLockTransient, 
            true, // the value can be anything that yields true
            $this->getAllowedMaxExecutionTime()    
        );    
    
        // Perform oEmbed caching - no API request will be performed
        $_oFetch = new FetchTweets_Fetch;
        
        // structure: array( 'mod' => time(), 'data' => $this->___oBase64->encode( $vData ) ), 
        $_aTransient = $_oFetch->getTransient( $_sTransientKey );            
    
        // If the mandatory keys are not set, it's broken.
        if ( ! isset( $_aTransient['mod'], $_aTransient['data'] ) ) {
            $this->deleteTransient( $_sTransientKey );
            return;
        }
        
        $_aTweets = ( array ) $this->___oBase64->decode( $_aTransient['data'] );        
        $_oFetch->addEmbeddableMediaElements( $_aTweets );        // the array is passed as reference.
        
        // Re-save the cache.
        $_oFetch->setTransient( $_sRequestURI, $_aTweets, $_aTransient['mod'], true );    // the method handles the encoding.
    
        // Delete the lock transient
        $this->deleteTransient( $_sLockTransient );
        
    }

}
