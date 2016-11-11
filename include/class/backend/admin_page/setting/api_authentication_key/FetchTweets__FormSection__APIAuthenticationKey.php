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
class FetchTweets__FormSection__APIAuthenticationKey extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'authentication_keys',
            'title'         => __( 'Authentication Keys', 'fetch-tweets' ),
            'description'   => __( 'These keys are required to process oAuth requests of the twitter API.', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(   
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
    }
        
    protected function _validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        
        // Sanitize
        $aInputs['consumer_key']     = trim( $aInputs['consumer_key'] );
        $aInputs['consumer_secret']  = trim( $aInputs['consumer_secret'] );
        $aInputs['access_token']     = trim( $aInputs['access_token'] );
        $aInputs['access_secret']    = trim( $aInputs['access_secret'] );
    
        // Check the connection
        $_oConnect = new FetchTweets_TwitterAPI_Verification( 
            $aInputs['consumer_key'],
            $aInputs['consumer_secret'],
            $aInputs['access_token'],
            $aInputs['access_secret']
        );
        $_aStatus = $_oConnect->getStatus();    
        
        // If it's connected, add the connection status
        if ( isset( $_aStatus['id_str'] ) ) {
            
            $aInputs['user_id']          = $_aStatus['id_str'];
            $aInputs['screen_name']      = $_aStatus['screen_name'];
            $aInputs['is_connected']     = true;
            $aInputs['connect_method']   = 'manual';
            
        } else {
            $aInputs['is_connected']     = false;
            $aInputs['connect_method']   = 'manual';
        }
        
        do_action( 'fetch_tweets_action_updated_credentials', $aInputs );
        return $aInputs;        
        
    }
    
}
