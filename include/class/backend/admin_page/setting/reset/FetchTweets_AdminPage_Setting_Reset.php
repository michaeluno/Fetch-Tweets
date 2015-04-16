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
class FetchTweets_AdminPage_Setting_Reset extends FetchTweets_AdminPage_Tab_Base {

    /**
     * Called when the tab loads.
     * 
     */
    public function replyToLoadTab( $oFactory ) {
        
        // Form sections 
        new FetchTweets_AdminPage_Setting_Reset_Option(
            $oFactory,
            $this->sPageSlug,
            array(
                'section_id'    => 'reset_settings',
                'capability'    => 'manage_options',
                'tab_slug'      => 'reset',
                'title'         => __( 'Reset Settings', 'fetch-tweets' ),
            )
        );
        
    }
    
}