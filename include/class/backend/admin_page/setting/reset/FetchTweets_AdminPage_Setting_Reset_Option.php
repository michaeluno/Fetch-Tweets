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
class FetchTweets_AdminPage_Setting_Reset_Option extends FetchTweets_AdminPage_Section_Base {

    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            array(    
                'field_id'          => 'reset_options',
                'title'             => __( 'Reset Options', 'fetch-tweets' ),
                'type'              => 'submit',
                'value'             => __( 'Reset', 'fetch-tweets' ),
                'reset'             => true,
                'description'   => __( 'If you get broken options, initialize them by performing reset.', 'fetch-tweets' ),
            )
        );        
        
    }    
    
}