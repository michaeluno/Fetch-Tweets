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
class FetchTweets_AdminPage_Setting_MISC extends FetchTweets_AdminPage_Tab_Base {

    
    /**
     * Called when the tab loads.
     * 
     */
    public function replyToLoadTab( $oFactory ) {
            
        // Form sections  
        new FetchTweets_AdminPage_Setting_MISC_Capability(
            $oFactory,
            $this->sPageSlug,
            array(
                'section_id'    => 'capabilities',
                'capability'    => 'manage_options',
                'tab_slug'      => 'misc',
                'title'         => __( 'Access Rights', 'fetch-tweets' ),
                'description'   => __( 'Set the access levels to the plugin setting pages.', 'fetch-tweets' ),
            )
        );
        
    }
    
}