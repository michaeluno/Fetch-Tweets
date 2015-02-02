<?php
/**
 * Handles Twitter API verification
 * 
 * @package          Fetch Tweets
 * @subpackage        
 * @copyright        Michael Uno
 * @since            2.4.5
 * 
 */
class FetchTweets_TwitterAPI_VerificationStatus {

    /**
     * Sets up properties.
     */
    public function __construct( $oOption ) {
        
        $this->oOption = $oOption;
        
    }
    
    /**
     * Returns the verification status array.
     * 
     * @since       2.4.5
     */
    public function get() {
        
        return $this->_getVerificationStatus();
        
    }
    
    /**
     * Prints the verification status table.
     */
    public function render() {
        
        FetchTweets_TwitterAPI_Verification::renderStatus( $this->get() );
        
    }

    /**
     * Retrieves the verification status with the saved access keys.
     * 
     * This method first checks with the manually set authentication keys and if it fails, it checks with the automatically set authentication keys.
     * 
     * @since       1.3.0
     * @return      array       The array which contains the verification status.
     * @since       2.4.5       Moved from the admin page class.
     */
    private function _getVerificationStatus() {

        // If it is disconnected, return an empty array.
        if ( ! $this->oOption->isConnected() ) {            
            return array();
        }
        
        // If the access token and access secret keys have been manually set,
        $_aStatus = $this->oOption->isAuthKeysManuallySet()
            ? $this->_getAuthenticationStatus( 
                $this->oOption->getConsumerKey(), 
                $this->oOption->getConsumerSecret(), 
                $this->oOption->getAccessToken(), 
                $this->oOption->getAccessTokenSecret() 
            )
            : array();
            
        if ( ! empty( $_aStatus ) ) { 
            return $_aStatus;
        }
            
        // If the access token and secret keys have been automatically set,
        if ( $this->oOption->isAuthKeysAutomaticallySet() ) {
            $_aStatus = $this->_getAuthenticationStatus( 
                FetchTweets_Commons::ConsumerKey, 
                FetchTweets_Commons::ConsumerSecret, 
                $this->oOption->getAccessTokenAuto(), 
                $this->oOption->getAccessTokenSecretAuto()
            );
        }
    
        return $_aStatus;
    
    }
        /**
         * Checks the API credential is valid or not.
         *      
         * @since           1.3.0
         * @since           2.4.5       Moved from the admin page class.
         * @return          array       the retrieved data.
         * @remark          The returned data is a merged result of 'account/verify_credientials' and 'rate_limit_status'.
         */
        private function _getAuthenticationStatus( $sConsumerKey, $sConsumerSecret, $sAccessToken, $sAccessSecret ) {
            
            $_oTwitterOAuth_Verification = new FetchTweets_TwitterAPI_Verification( 
                $sConsumerKey, 
                $sConsumerSecret, 
                $sAccessToken, 
                $sAccessSecret 
            );
            return $_oTwitterOAuth_Verification->getStatus();
            
        }   

}