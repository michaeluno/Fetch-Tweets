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
class FetchTweets_AdminPage_Setting_General_Cache extends FetchTweets_AdminPage_Section_Base {

    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            array(
                'field_id'       => 'cache_for_errors',
                'title'          => __( 'Cache for Errors', 'fetch-tweets' ),
                'type'           => 'checkbox',
                'label'          => __( 'Cache fetched results even for errors.', 'fetch-tweets' ),
                'description'    => __( 'This reduces the chances to reach the Twitter API rate limit.', 'fetch-tweets' ),
            ),
            array(
                'field_id'       => 'caching_mode',
                'title'          => __( 'Caching Mode', 'fetch-tweets' ),
                'type'           => 'radio',
                'label'          => array(
                    'normal'    => __( 'Normal', 'fetch-tweets' ) . ' - ' . __( 'uses WP Cron.', 'fetch-tweets' ),
                    'intense'   => __( 'Intense', 'fetch-tweets' ) . ' - ' . __( 'uses the plugin caching method.', 'fetch-tweets' ),
                ),
                'after_label'    => '<br />',
                'default'        => 'normal',
            )     
        );     
        
    }    
    
}