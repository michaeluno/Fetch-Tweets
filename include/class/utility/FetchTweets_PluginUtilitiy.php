<?php
/**
 *	Provides utility plugin specific methods.
 *
 * @package     Fetch Tweets
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl	http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since		2.3.6
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
        if ( ! isset( $_GET['post_type'] ) ) {
            return false;
        }
        $_bIsPluginAdminPage = ( FetchTweets_Commons::PostTypeSlug === $_GET['post_type'] );
        return $_bIsPluginAdminPage;
            
    }

}