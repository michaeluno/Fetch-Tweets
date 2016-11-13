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
class FetchTweets__FormSection__TestHTTPRequest extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return             array(
            'section_id'    => 'http_request',
            'title'         => __( 'HTTP Requests', 'fetch-tweets' ),
            'save'          => false,
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(         
            array(
                'field_id'          => 'urls',
                'type'              => 'textarea',                   
                'save'              => false,
                'title'             => __( 'URLs', 'fetch-tweets' ),
                'attributes'        => array(
                    'style' => 'min-width: 400px',
                ),
            ),        
            array(
                'field_id'          => 'wp_remote_get',
                'type'              => 'submit',
                'skip_confirmation' => true,                   
                // 'show_title_column' => false,
                'save'              => false,
                'title'             => __( 'Get Method', 'fetch-tweets' ),
                'value'             => 'wp_remote_get',
            ),
            array(
                'field_id'          => 'wp_remote_post',
                'type'              => 'submit',
                'skip_confirmation' => true,                   
                // 'show_title_column' => false,
                'save'              => false,
                'title'             => __( 'Post Method', 'fetch-tweets' ),
                'value'             => 'wp_remote_post',
            ),                  
        );
    }
        
    /**
     * Validates the submitted form data.
     * 
     * @since    2.5.0
     */
    public function _validate( $aInputs, $aOldInput, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();
        
        $_aURLs = explode( PHP_EOL, $aInputs[ 'urls' ] );
        
        if ( 'wp_remote_get' === $aSubmitInfo[ 'field_id' ] ) {
            $_bSuccedd = $this->___doTest_wp_remote_get( $_aURLs );
            return $aInputs;
        }       
        if ( 'wp_remote_post' === $aSubmitInfo[ 'field_id' ] ) {
            $_bSuccedd = $this->___doTest_wp_remote_post( $_aURLs );
            return $aInputs;
        }
   
       
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'fetch-tweets' ) );
            return $aOldInput;
        }
                
        return $aInputs;
        
    }
    
        private function ___doTest_wp_remote_get( $aURLs ) {
            $_oHTTPGET = new FetchTweets_HTTP_Get( $aURLs );
FetchTweets_Debug::log( $_oHTTPGET->get() );
            return true;
        }
    
        private function ___doTest_wp_remote_post( $aURLs ) {
            $_oHTTPGET = new FetchTweets_HTTP_Post( $aURLs );
FetchTweets_Debug::log( $_oHTTPGET->get() );
            return true;
        }    
       
}