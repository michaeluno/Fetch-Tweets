<?php
/**
 *    Provides utility methods which use WordPress functions.
 *
 * @package     Fetch Tweets
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        1.3.3.2
 * 
 */

class FetchTweets_WPUtilities extends FetchTweets_Utilities {
    
    /**
     * Removes transients that have the given prefix.
     * 
     * @since   1.3.5
     * @since   2.3.7   Moved from the FetchTweets_Transient class.
     */
    static public function clearTransients( $aPrefixes=array( 'FTWS', 'FTWSFeedMs' ) ) {    // for the deactivation hook.

        // This method also serves for the deactivation callback and in that case, an empty value is passed to the first parameter.
        $aPrefixes = empty( $aPrefixes ) ? array( 'FTWS', 'FTWSFeedMs' ) : $aPrefixes;        // 'FTWSAds'
            
        foreach( $aPrefixes as $sPrefix ) {
            $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_%{$sPrefix}%' )" );
            $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_timeout_%{$sPrefix}%' )" );
        }
    
    }
    
    /**
     * Stores whether the page is loaded in the network admin or not.
     * @since 2.3.7
     */
    static private $_bIsNetworkAdmin;
    
    /**
     * Deletes the given transient.
     *
     * @since 2.3.7
     */
    static public function deleteTransient( $sTransientKey ) {

        // temporarily disable $_wp_using_ext_object_cache
        global $_wp_using_ext_object_cache;  
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache; 
        $_wp_using_ext_object_cache = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin ) ? self::$_bIsNetworkAdmin : is_network_admin();

        $_vTransient = ( self::$_bIsNetworkAdmin ) ? delete_site_transient( $sTransientKey ) : delete_transient( $sTransientKey );

        // reset prior value of $_wp_using_ext_object_cache
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp; 

        return $_vTransient;

    }
    /**
     * Retrieves the given transient.
     * 
     * @since 2.3.7
     */    
    static public function getTransient( $sTransientKey, $vDefault=null ) {

        // temporarily disable $_wp_using_ext_object_cache
        global $_wp_using_ext_object_cache;  
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache; 
        $_wp_using_ext_object_cache = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin ) ? self::$_bIsNetworkAdmin : is_network_admin();

        $_vTransient = ( self::$_bIsNetworkAdmin ) ? get_site_transient( $sTransientKey ) : get_transient( $sTransientKey );    

        // reset prior value of $_wp_using_ext_object_cache
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp; 

        return null === $vDefault 
            ? $_vTransient
            : ( false === $_vTransient 
                ? $vDefault
                : $_vTransient
            );
        
    }
    /**
     * Sets the given transient.
     * @since 2.3.7
     */
    static public function setTransient( $sTransientKey, $vValue, $iExpiration=0 ) {

        // temporarily disable $_wp_using_ext_object_cache
        global $_wp_using_ext_object_cache;  
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache; 
        $_wp_using_ext_object_cache = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin ) ? self::$_bIsNetworkAdmin : is_network_admin();
        
        // Do not allow 0 because the table row will be autoloaded and it consumes the server memory.
        $iExpiration = 0 == $iExpiration ? 99999 : $iExpiration;
        
        $_vTransient = ( self::$_bIsNetworkAdmin ) 
            ? set_site_transient( $sTransientKey, $vValue, $iExpiration ) 
            : set_transient( $sTransientKey, $vValue, $iExpiration );

        // reset prior value of $_wp_using_ext_object_cache
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp; 

        return $_vTransient;     
    }
    
    

    /**
     * Calculates the URL from the given path.
     * 
     * @since            1.3.3.2
     * @since            1.3.3.8            FIxed an issue that /./ gets inserted.
     * @static
     * @access            public
     * @return            string            The source url
     */
    static public function getSRCFromPath( $sFilePath ) {
                
        $oWPStyles = new WP_Styles();    // It doesn't matter whether the file is a style or not. Just use the built-in WordPress class to calculate the SRC URL.
        $sRelativePath = FetchTweets_Utilities::getRelativePath( ABSPATH, $sFilePath );        
        $sRelativePath = preg_replace( "/^\.[\/\\\]/", '', $sRelativePath, 1 );    // removes the heading ./ or .\ 
        $sHref = trailingslashit( $oWPStyles->base_url ) . $sRelativePath;
        unset( $oWPStyles );    // for PHP 5.2.x or below
        return esc_url( $sHref );
        
    }
    
    /**
     * Returns the file path by checking if the given path is a file.
     * 
     * If fails, it attempts to check with the relative path to ABSPATH.
     * 
     * This is necessary when some users build the WordPress site locally and immigrate to the production site.
     * In that case, the stored absolute path won't work so it needs to be converted to the one that works in the new environment.
     * 
     * @since            2.3.5
     */
    public static function getReadableFilePath( $sFilePath, $sRelativePathToABSPATH='' ) {
        
        if ( @is_file( $sFilePath ) ) {
            return $sFilePath;
        }
        
        if ( ! $sRelativePathToABSPATH ) {
            return false;
        }
        
        // try with the relative path.
        $_sAbsolutePath = realpath( trailingslashit( ABSPATH ) . $sRelativePathToABSPATH );
        if ( ! $_sAbsolutePath ) {
            return false;
        }
        
        if ( @is_file( $_sAbsolutePath ) ) {
            return $_sAbsolutePath;
        }
        
        return false;        
        
    }    
    

}