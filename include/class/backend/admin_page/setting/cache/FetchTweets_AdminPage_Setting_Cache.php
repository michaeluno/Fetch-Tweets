<?php
/**
 * Fetch Tweets
 * 
 * Fetches and displays tweets from twitter.com.
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Defines an in-page tab.
 * 
 * @since       2.4.5
 */
class FetchTweets_AdminPage_Setting_Cache extends FetchTweets_AdminPage_Tab_Base {
    
    /**
     * Called when the tab loads.
     * 
     */
    public function replyToLoadTab( $oFactory ) {
            
        // Form sections    
        new FetchTweets_AdminPage_Setting_Cache_Clear(
            $oFactory,
            $this->sPageSlug,
            array(
                'section_id'    => 'clear_caches',
                'tab_slug'      => 'cache',
                'title'         => __( 'Clear', 'fetch-tweets' ),
            )            
        );        
        new FetchTweets_AdminPage_Setting_Cache_Cache(
            $oFactory,
            $this->sPageSlug,        
            array(
                'section_id'    => 'cache_settings',
                'tab_slug'      => 'cache',
                'title'         => __( 'Cache Settings', 'fetch-tweets' ),
            )     
        );        
                
    }
    
}