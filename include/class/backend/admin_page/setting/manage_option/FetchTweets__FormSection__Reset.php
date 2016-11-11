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
class FetchTweets__FormSection__Reset extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return             array(
            'section_id'    => 'reset',
            'title'         => __( 'Reset', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array( 
                'field_id'          => 'reset_confirmation_check',
                'title'             => __( 'Reset Options', 'fetch-tweets' ),
                'type'              => 'checkbox',
                'label'             => __( 'I understand the options will be erased by pressing the reset button.', 'fetch-tweets' ),
                'save'              => false,
                'value'             => false,
            ),            
            array(
                'field_id'          => 'reset',
                'type'              => 'submit',
                'reset'             => true,
                'skip_confirmation' => true,                   
                // 'show_title_column' => false,
                'value'             => __( 'Reset', 'fetch-tweets' ),
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
        
        // If the pressed button is not the one with the check box, do not set a field error.
        if ( 'reset' !== $aSubmitInfo[ 'field_id' ] ) {
            return $aInputs;
        }       

        if ( ! $aInputs[ 'reset_confirmation_check' ] ) {
            
            $_bVerified = false;
            $_aErrors[ $this->_sSectionID ][ 'reset_confirmation_check' ] = __( 'Please check the check box to confirm you want to reset the settings.', 'fetch-tweets' );
                
        }        
        
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'fetch-tweets' ) );
            return $aOldInput;
        }
                
        return $aInputs;
        
    }
        
}