<?php
/**
 *    Provides utility plugin specific methods.
 *
 * @package     Fetch Tweets
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.3.6
 * 
 */

class FetchTweets_PluginUtility extends FetchTweets_WPUtilities {

   /**
     * Checks whether the page is loaded in one of the plugin admin pages.
     * 
     * @since       2.3.6
     */
    static public function isInPluginAdminPage() {
        
        static $_bIsPluginAdminPage;
        
        if ( isset( $_bIsPluginAdminPage ) ) {
            return $_bIsPluginAdminPage;
        }
        
        if ( ! is_admin() ) {
            return false;
        }
        if ( ! isset( $GLOBALS['pagenow'] ) ) {
            return false;
        }
        if ( ! in_array( $GLOBALS['pagenow'], array( 'edit.php', 'plugins.php' ) ) ) {
            return false;                
        }
        if ( 'plugins.php' === $GLOBALS['pagenow'] ) {
            return true;
        }
        if ( ! isset( $_GET['post_type'] ) ) {
            return false;
        }
        $_bIsPluginAdminPage = ( FetchTweets_Commons::PostTypeSlug === $_GET['post_type'] );
        return $_bIsPluginAdminPage;
            
    }

    /*
     * MISC methods.
     */
    /**
     * Returns an array holding the labels(names) of activated templates.
     * 
     * This is used for the widget form or the template meta box to let the user select a template.
     * 
     * @since       unknown
     * @since       2.3.9           Moved form the templates class.
     */
    static public function getTemplateArrayForSelectLabel( $aTemplates=null ) {
        
        $_oOption = FetchTweets_Option::getInstance();
        if ( ! $aTemplates ) {
            $aTemplates = $_oOption->getActiveTemplates();
        }

        $_aLabels = array();
        foreach ( $aTemplates as $_sSlug => $_aTemplate ) {
            $_oTemplate = new FetchTweets_Template( $_aTemplate['sSlug'] );
            $_sName     = $_oTemplate->get( 'sName' );            
            if ( ! $_sName ) { continue; }   // it may be broken.
            $_aLabels[ $_aTemplate['sSlug'] ] = $_sName;
        }
        return $_aLabels;
        
    }    
        
    
}