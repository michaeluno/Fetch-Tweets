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
class FetchTweets_AdminPage_Setting_TwitterConnect_Authentication extends FetchTweets_AdminPage_Section_Base {

    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    public function addFields( $oFactory, $sSectionID ) {
    
        $_oOption       = $GLOBALS['oFetchTweets_Option'];
        $_bIsConnected  = $_oOption->isConnected();
    
        $oFactory->addSettingFields(
            array(    
                'field_id'      => 'connect_to_twitter',
                'title'         => __( 'Connect to Twitter', 'fetch-tweets' ),
                'label'         => __( 'Connect', 'fetch-tweets' ),
                'href'          => add_query_arg( 
                    array( 
                        'post_type' => 'fetch_tweets', 
                        'page'      => 'fetch_tweets_settings', 
                        'tab'       => 'twitter_redirect' 
                    ), 
                    admin_url( $GLOBALS['pagenow'] )
                ),
                'type'          => 'submit',
                'if'            => ! $_bIsConnected,
            ),    
            array(    
                'field_id'      => 'disconnect_from_twitter',
                'title'         => __( 'Connect to Twitter', 'fetch-tweets' ),
                'label'         => __( 'Disconnect', 'fetch-tweets' ),
                'type'          => 'submit',
                'if'            => $_bIsConnected,
            ),                
            array(    
                'field_id'      => 'manual_authentication',
                'title'         => __( 'Manual', 'fetch-tweets' ),
                'label'         => __( 'Set Keys Manually', 'fetch-tweets' ),
                'href'          => add_query_arg( 
                    array( 
                        'post_type' => 'fetch_tweets',
                        'page'      => 'fetch_tweets_settings', 
                        'tab'       => 'authentication',
                        'settings-updated' => false 
                    ) 
                ),
                'type'          => 'submit',
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
            )
        );             
        
        add_filter( "validation_{$this->sPageSlug}", array( $this, 'replyToValidatePage' ), 10, 4 );
        
    }    
    
    /**
     * Checks if the Disconnect button is pressed and if so deletes the credentials from the options.
     * 
     * @since   2.4.5
     */
    public function replyToValidatePage( $aInput, $aOldInput, $oFactory, $aSubmitInformation ) {
        
        if ( 'disconnect_from_twitter' !== $aSubmitInformation['field_id'] ) {
            return $aInput;
        }
        
        $_oOption       = $GLOBALS['oFetchTweets_Option'];        
        
        // If the Disconnect button is pressed.    
        $aInput = is_array( $aInput ) ? $aInput : array();    
        
        // the transient needs to be removed 
        FetchTweets_WPUtilities::deleteTransient( 
            FetchTweets_Commons::TransientPrefix . '_' . md5( 
                serialize( 
                    array(  
                        $_oOption->getConsumerKey(), 
                        $_oOption->getConsumerSecret(), 
                        $_oOption->getAccessToken(), 
                        $_oOption->getAccessTokenSecret() 
                    ) 
                ) 
            )
        );
        FetchTweets_WPUtilities::deleteTransient(
            FetchTweets_Commons::TransientPrefix . '_' . md5( 
                serialize( 
                    array( 
                        FetchTweets_Commons::ConsumerKey,
                        FetchTweets_Commons::ConsumerSecret,
                        $_oOption->getAccessTokenAuto(),
                        $_oOption->getAccessTokenSecretAuto()
                    )
                )
            )
        );
        
        $aInput['authentication_keys']  = array();
        $aInput['twitter_connect']      = array();
        do_action( 'fetch_tweets_action_updated_credentials', array() );
        return $aInput;
        
    }
        
}