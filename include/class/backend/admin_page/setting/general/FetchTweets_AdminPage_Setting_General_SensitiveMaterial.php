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
 * Defines the Content Policy section of the plugin setting page.
 * 
 * @since       2.4.8
 */
class FetchTweets_AdminPage_Setting_General_SensitiveMaterial extends FetchTweets_AdminPage_Section_Base {
    
    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            array(
                'field_id'      => 'possibly_sensitive',
                'title'         => __( 'Possibly Sensitive Materials', 'fetch-tweets' ),
                'type'          => 'radio',
                'label'         => array(
                    'do_nothing'                 => __( 'Do nothing.', 'fetch-tweets' ),
                    'remove'                     => __( 'Do not display the tweet.', 'fetch-tweets' ),
                    'replace_media_with_message' => __( 'Replace the media element with a message.', 'fetch-tweets' ),
                ),
                'label_min_width'   => '100%',
                'default'       => 'do_nothing',
                'description'   => array(
                    __( 'Decide which policy should be applied to tweets containing sensitive materials such as pornography.', 'fetch-tweets' ),
                    __( 'Please note that checking sensitive contents is not entirely reliable.', 'fetch-tweets' ),
                ),
            ),
            array()
        );     
        
        add_filter( "validation_{$oFactory->oProp->sClassName}_{$sSectionID}", array( $this, 'replyToValidate' ), 10, 4 );
        
    }  


    /**
     * Validates the submit data of the 'general' tab of the 'fetch_tweets_settings' page.
     * 
     * @remark      validation_{class name}_{section id}
     */    
    public function replyToValidate( $aInput, $aOriginal, $oFactory, $aSubmitInfo ) {
        return $aInput;        
    }    
    
}