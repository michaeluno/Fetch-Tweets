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
        $_oHTTPRequestTable = new FetchTweets_DatabaseTable_ft_http_requests;
        return array(         
            array(
                'field_id'          => 'http_requests',
                'title'             => __( 'Size', 'fetch-tweets' ),
                'content'           => "<p>"
                        . $_oHTTPRequestTable->getTableSize()
                    . "</p>",
            ),
            array(    
                'field_id'          => 'clear_all',
                'title'             => __( 'Operation', 'fetch-tweets' ),
                'type'              => 'submit',
                'href'              => add_query_arg(
                    $_GET,
                    admin_url( $this->getElement( $GLOBALS, 'pagenow', 'edit.php' ) )
                ),
                'label'             => __( 'Clear All', 'fetch-tweets' ),
                'attributes'        => array(
                    'class' => 'button button-secondary',  
                ),
                'save'              => false,
            ),   
            array(    
                'field_id'          => 'clear_expired',
                'type'              => 'submit',
                'href'              => add_query_arg(
                    $_GET,
                    admin_url( $this->getElement( $GLOBALS, 'pagenow', 'edit.php' ) )
                ),
                'label'             => __( 'Clear Expired', 'fetch-tweets' ),
                'attributes'        => array(
                    'class' => 'button button-secondary',  
                ),
                'save'              => false,
            ),               
        );
    }
        
    protected function _construct( $oFactory ) {
        add_action( 
            "submit_{$oFactory->oProp->sClassName}_{$this->_sSectionID}_clear_all", 
            array( $this, 'replyToClearAllCaches' ),   // callback
            10, // priority
            5   // number of parameters
        );
        add_action( 
            "submit_{$oFactory->oProp->sClassName}_{$this->_sSectionID}_clear_expired", 
            array( $this, 'replyToClearExpiredCaches' ),   // callback
            10, // priority
            5   // number of parameters
        );        
    }
        /**
         * @callback        action      submit_{class name}_{section id}_{field id}
         */
        public function replyToClearAllCaches() {
            $_aParams = func_get_args();
            
            // Clear transients
            $this->clearTransients();

            // Delete all rows
            $_oHTTPRequestTable = new FetchTweets_DatabaseTable_ft_http_requests;
            $_oHTTPRequestTable->deleteAll();
            
            $_oFactory = $_aParams[ 2 ];
            $_oFactory->setSettingNotice( __( 'All the caches have been cleared.', 'fetch-tweets' ), 'updated' );
        }
        
        public function replyToClearExpiredCaches() {
            $_aParams = func_get_args();
            $_oFactory = $_aParams[ 2 ];
            $_oHTTPRequestTable = new FetchTweets_DatabaseTable_ft_http_requests;
            $_oHTTPRequestTable->deleteExpired();
            $_oFactory->setSettingNotice( __( 'Expired caches have been cleared.', 'fetch-tweets' ), 'updated' );
        }

}
