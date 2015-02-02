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
class FetchTweets_AdminPage_Setting_General extends FetchTweets_AdminPage_Tab_Base {

    /**
     * Called when the tab loads.
     * 
     */
    public function replyToLoadTab( $oFactory ) {
        
        // Add form sections
        new FetchTweets_AdminPage_Setting_General_Default(
            $oFactory,
            $this->sPageSlug,        
            array(
                'section_id'    => 'default_values',
                'tab_slug'      => 'general',
                'title'         => __( 'Default Values', 'fetch-tweets' ),
                'help'          => __( 'Set the default option values which will be applied when the argument values are not set.', 'fetch-tweets' )
                    . __( 'These values will be overridden by the argument set directly to the widget options or shortcode.', 'fetch-tweets' ),
            )        
        );
        new FetchTweets_AdminPage_Setting_General_Cache(
            $oFactory,
            $this->sPageSlug,        
            array(
                'section_id'    => 'cache_settings',
                'tab_slug'      => 'general',
                'title'         => __( 'Cache Settings', 'fetch-tweets' ),
            )     
        );
        new FetchTweets_AdminPage_Setting_General_Search(
            $oFactory,
            $this->sPageSlug,        
            array(
                'section_id'    => 'search',
                'tab_slug'      => 'general',
                'title'         => __( 'Search', 'fetch-tweets' ),
            ) 
        );
        
    }
    
}