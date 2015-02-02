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
class FetchTweets_AdminPage_Setting_MISC_ContentSecurity extends FetchTweets_AdminPage_Section_Base {

    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            array(
                'field_id'          => 'disable_warnings',
                'title'             => __( 'Disable Warnings', 'fetch-tweets' ),
                'description'       => __( 'The warnings may appear when you enable the Follow button in the template.', 'fetch-tweets' )
                    . ' ' . __( 'This option is to disable the warnings.', 'fetch-tweets' ),
                'type'              => 'checkbox',
                'label'             => __( 'Disable content security policy warnings in the browser console.', 'fetch-tweets' ),
            )       
        );     
        
    }    
    
}