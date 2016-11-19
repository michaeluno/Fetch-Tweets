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
class FetchTweets__FormSection__UninstallTables extends FetchTweets__FormSection__InstallTables {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return             array(
            'section_id'    => 'uninstall_tables',
            'title'         => __( 'Uninstall Tables', 'fetch-tweets' ),
            'save'          => false,
        );
    }
        
        /**
         * @since       2.5.0
         * @return      string
         */    
        protected function _getTheHandlingMessage( $bSuccedd ) {
            return $bSuccedd
                ? __( 'Uninstalled the table of %1$s.', 'fetch-tweets' )
                : __( 'Failed to uninstalled the table of %1$s.', 'fetch-tweets' );
        }
        
        protected function _handleTable_http_requests() {
            $_oTable  = new FetchTweets_DatabaseTable_ft_http_requests;
            $_oTable->uninstall();        
            return true;
        }
        protected function _handleTable_tweets() {
            // $_oTable  = new FetchTweets_DatabaseTable_ft_tweets;
            // $_oTable->uninstall();            
            return true;
        }        
       
}