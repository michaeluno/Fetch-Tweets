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
class FetchTweets__FormSection__InstallTables extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return             array(
            'section_id'    => 'install_tables',
            'title'         => __( 'Install Tables', 'fetch-tweets' ),
            'save'          => false,
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
                
        return array(         
            array(
                'field_id'          => 'http_requests',
                'type'              => 'submit',
                'skip_confirmation' => true,                   
                // 'show_title_column' => false,
                'save'              => false,
                'value'             => __( 'HTTP Requests', 'fetch-tweets' ),
            ),
            array(
                'field_id'          => 'tweets',
                'type'              => 'submit',
                'skip_confirmation' => true,                   
                // 'show_title_column' => false,
                'save'              => false,
                'value'             => __( 'Tweets', 'fetch-tweets' ),
            )                   
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
        
        if ( 'http_requests' === $aSubmitInfo[ 'field_id' ] ) {
            $_bSuccedd = $this->_handleTable_http_requests();
            $oAdminPage->setSettingNotice( 
                sprintf(
                    $this->_getTheHandlingMessage( $_bSuccedd ),
                   __( 'HTTP requests', 'fetch-tweets' )
                ),
                $_bSuccedd ? 'updated' : 'error'
            );
            return $aInputs;
        }       

        if ( 'tweets' === $aSubmitInfo[ 'field_id' ] ) {
            $_bSuccedd = $this->_handleTable_tweets();
            $oAdminPage->setSettingNotice( 
                sprintf( 
                    $this->_getTheHandlingMessage( $_bSuccedd ),
                   __( 'Tweets', 'fetch-tweets' )
                ),
                $_bSuccedd ? 'updated' : 'error'
            );            
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
    
        /**
         * @since       2.5.0
         * @return      string
         */    
        protected function _getTheHandlingMessage( $bSuccedd ) {
            return $bSuccedd
                ? __( 'Installed the table of %1$s.', 'fetch-tweets' )
                : __( 'Failed to installed the table of %1$s.', 'fetch-tweets' );
        }
        
        /**
         * @since       2.5.0
         * @return      boolean
         */
        protected function _handleTable_http_requests() {
            $_oTable  = new FetchTweets_DatabaseTable_ft_http_requests;
            $_aResult = $_oTable->upgrade();
            return ! empty( $_aResult );
        }
        /**
         * @since       2.5.0
         * @return      boolean
         */        
        protected function _handleTable_tweets() {
            $_oTable  = new FetchTweets_DatabaseTable_ft_tweets;
            $_aResult = $_oTable->upgrade();
            return ! empty( $_aResult );
        }        
       
}