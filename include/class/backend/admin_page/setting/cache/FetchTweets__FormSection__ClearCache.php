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
class FetchTweets__FormSection__ClearCache extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return             array(
            'section_id'    => 'clear_caches',
            'title'         => __( 'Clear', 'fetch-tweets' ),
            'save'          => false,
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array(    
                'field_id'          => 'clear_caches',
                'title'             => __( 'Clear Tweet Caches', 'fetch-tweets' ),
                'type'              => 'submit',
                'href'              => add_query_arg(
                    $_GET,
                    admin_url( $this->getElement( $GLOBALS, 'pagenow', 'edit.php' ) )
                ),
                'label'             => __( 'Clear', 'fetch-tweets' ),
                'description'       => __( 'If you need to refresh the fetched tweets, clear the caches.', 'fetch-tweets' ),
                'attributes'        => array(
                    'class' => 'button button-secondary',  
                ),
            ),   
        );
    }
        
    protected function _construct( $oFactory ) {
        add_action( 
            "submit_{$oFactory->oProp->sClassName}_{$this->_sSectionID}_clear_caches", 
            array( $this, 'replyToSubmitField' ),   // callback
            10, // priority
            5   // number of parameters
        );        
    }
        /**
         * @callback        action      submit_{class name}_{section id}_{field id}
         */
        public function replyToSubmitField() {
            $_aParams = func_get_args();
            $this->clearTransients();
            
            $_oFactory = $_aParams[ 2 ];
            $_oFactory->setSettingNotice( __( 'The caches have been cleared.', 'fetch-tweets' ), 'updated' );
        }
        
    /**
     * Validates the submitted form data.
     * 
     * @since    2.5.0
     */
    protected function _validate( $aInputs, $aOldInput, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();
        return $aInputs;
        
    }
        
}