<?php
/**
 * Fetch Tweets
 * 
 * Fetches and displays tweets from twitter.com.
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 */

/**
 * Defines a form section.
 * 
 * @since       2.5.0   
 */
class FetchTweets__FormSection__APIAuthentication extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'twitter_connect',   
            'title'         => __( 'Authenticate', 'fetch-tweets' ),
        );
    }

    protected function _construct( $oFactory ) {
        
        add_filter( "validation_{$this->_sPageSlug}", array( $this, 'replyToValidatePage' ), 10, 4 );
        
    }
    
    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        
        $_oOption       = FetchTweets_Option::getInstance();
        $_bIsConnected  = $_oOption->isConnected();
        
        return array(   
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
                    admin_url( $GLOBALS[ 'pagenow' ] )
                ),
                'type'          => 'submit',
                'if'            => ! $_bIsConnected,
                'hidden'        => $_bIsConnected,
            ),    
            array(    
                'field_id'      => 'disconnect_from_twitter',
                'title'         => __( 'Connect to Twitter', 'fetch-tweets' ),
                'label'         => __( 'Disconnect', 'fetch-tweets' ),
                'type'          => 'submit',
                'if'            => $_bIsConnected,
                'hidden'        => ! $_bIsConnected,
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
    }
        
    /**
     * @callback        filter      validation_{page slug}
     */
    public function replyToValidatePage( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        
        if ( 'disconnect_from_twitter' !== $aSubmitInfo[ 'field_id' ] ) {
            return $aInputs;
        }

        // At this point, the user has pressed the Disconnect button.
        
        $_oOption   = FetchTweets_Option::getInstance();
        
        // If the Disconnect button is pressed.    
        $aInputs    = $this->getAsArray( $aInputs );
        
        // the transient needs to be removed 
        $this->deleteTransient( 
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
        $this->deleteTransient(
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
        
        $aInputs[ 'authentication_keys' ]  = array();
        $aInputs[ 'twitter_connect' ]      = array();
        do_action( 'fetch_tweets_action_updated_credentials', array() );
        return $aInputs;

    }

}
