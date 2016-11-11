<?php
/**
 * Fetch Tweets
 * @copyright   Copyright (c) 2013-2016, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Methods used for debugging
 * @since       1.0.0
 * @since       2.5.0       Extends `FetchTweets_AdminPageFramework_Debug`.
 */
final class FetchTweets_Debug extends FetchTweets_AdminPageFramework_Debug {
    
    /**
     * @todo        deprecate this method.
     */
    static public function echoMemoryUsage() {
        
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }
                   
        echo self::getMemoryUsage() . "<br/>";
        
    }         

    /**
     * @todo        deprecate this method.
     */
    static public function getMemoryUsage( $intType=1 ) {
       
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }
       
        $intMemoryUsage = $intType == 1 ? memory_get_usage( true ) : memory_get_peak_usage( true );
       
        if ( $intMemoryUsage < 1024 ) return $intMemoryUsage . " bytes";
        
        if ( $intMemoryUsage < 1048576 ) return round( $intMemoryUsage/1024,2 ) . " kilobytes";
        
        return round( $intMemoryUsage / 1048576,2 ) . " megabytes";
           
    }  
    
    /**
     * @todo        deprecate this method.
     */    
    static public function getOption( $strKey ) {

        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) return;
        
        $oOption = & $GLOBALS['oFetchTweets_Option'];        
        if ( ! isset( $oOption->aOptions[ $strKey ] ) ) return;
        
        self::dump( $oOption->aOptions[ $strKey ] );
        
    }

}