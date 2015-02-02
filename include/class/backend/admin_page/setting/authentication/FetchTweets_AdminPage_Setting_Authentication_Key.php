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
 * @action      do      fetch_tweets_action_updated_credentials     Triggered when the Twitter API access credentials are stored.
 */
class FetchTweets_AdminPage_Setting_Authentication_Key extends FetchTweets_AdminPage_Section_Base {

    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            array(    
                'field_id'      => 'consumer_key',
                'title'         => __( 'Consumer Key', 'fetch-tweets' ),
                'type'          => 'text',
                'attributes'    => array(
                    'size'    => 60,
                ),                
            ),
            array(    
                'field_id'      => 'consumer_secret',
                'title'         => __( 'Consumer Secret', 'fetch-tweets' ),
                'type'          => 'text',
                'attributes'    => array(
                    'size'  => 60,
                ),                
            ),
            array(    
                'field_id'      => 'access_token',
                'title'         => __( 'Access Token', 'fetch-tweets' ),
                'type'          => 'text',
                'attributes'    => array(
                    'size'    =>    60,
                ),
            ),
            array(    
                'field_id'      => 'access_secret',
                'title'         => __( 'Access Secret', 'fetch-tweets' ),
                'type'          => 'text',
                'attributes'    => array(
                    'size'    => 60,
                ),
                'description'   => '<p class="description">' 
                        . sprintf( 
                            __( 'You can obtain those keys by logging in to <a href="%1$s" target="_blank">Twitter Developers</a>', 'fetch-tweets' ),
                            'https://dev.twitter.com/apps' 
                        )
                    . '</p>',
            ),
            array(
                'field_id'      => 'connect_method',
                'type'          => 'hidden',
                'value'         => 'manual',
                'is_hidden'     => true,
                'attributes'    => array(
                    'fieldrow'    => array(
                        'style'    => 'display:none',
                    ),
                ),
            ),
            array(  // single button
                'field_id'      => 'submit_authentication_keys',
                'type'          => 'submit',
                'before_field'  => "<div class='right-button'>",
                'after_field'   => "</div>",
                'label'         => __( 'Authenticate', 'fetch-tweets' ),
                'attributes'    => array(
                    'class' => 'button button-primary',
                ),
            )     
        );             
        
        add_filter( "validation_{$oFactory->oProp->sClassName}_{$sSectionID}", array( $this, 'replyToValidate' ), 10, 4 );
        
    }    
   
    /**
     * Triggered when the manual keys are set and submitted.
     * 
     * @since       2
     * @since       2.4.5       Moved from the admin page class.
     * @remark      valiudation_{class name}_{section id}
     */
    public function replyToValidate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {

        // Sanitize
        $aInput['consumer_key']     = trim( $aInput['consumer_key'] );
        $aInput['consumer_secret']  = trim( $aInput['consumer_secret'] );
        $aInput['access_token']     = trim( $aInput['access_token'] );
        $aInput['access_secret']    = trim( $aInput['access_secret'] );
    
        // Check the connection
        $_oConnect = new FetchTweets_TwitterAPI_Verification( 
            $aInput['consumer_key'],
            $aInput['consumer_secret'],
            $aInput['access_token'],
            $aInput['access_secret']
        );
        $_aStatus = $_oConnect->getStatus();    
        
        // If it's connected, add the connection status
        if ( isset( $_aStatus['id_str'] ) ) {
            
            $aInput['user_id']          = $_aStatus['id_str'];
            $aInput['screen_name']      = $_aStatus['screen_name'];
            $aInput['is_connected']     = true;
            $aInput['connect_method']   = 'manual';
            
        } else {
            $aInput['is_connected']     = false;
            $aInput['connect_method']   = 'manual';
        }
        
        do_action( 'fetch_tweets_action_updated_credentials', $aInput );
        return $aInput;
        
    }
   
}