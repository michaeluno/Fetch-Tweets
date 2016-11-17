<?php
/**
 * Fetch Tweets
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Loads a page in the background 
 * 
 * @since       2.5.0
 */
class FetchTweets_Event__BackgroundPageload extends FetchTweets_PluginUtility {
    
    public function __construct() {
        self::___setBackgroundPageLoad();
    }       
        
        /**
         * Schedules next page load at the end of the current page load.
         */
        static private function ___setBackgroundPageLoad() {
            
            if ( self::hasBeenCalled( __METHOD__ ) ) {
                return;
            }
            add_action( 'shutdown', array( __CLASS__, 'loadPageInTheBackground' ) );
            
        }
        static public function loadPageInTheBackground() {
            
            $_oOption = FetchTweets_Option::getInstance();
            $_sURL    = 'intense' === $_oOption->get( array( 'cache_settings', 'caching_mode' ) )
                ? self::getCurrentURL( array( 'updating-caches' => 1 ) )
                : site_url( "/wp-cron.php?updating-caches" );
            wp_remote_get( 
                $_sURL, 
                array( 
                    'timeout'     => 0.01, 
                    'sslverify'   => false, 
                ) 
            );
            
        }
        
}
