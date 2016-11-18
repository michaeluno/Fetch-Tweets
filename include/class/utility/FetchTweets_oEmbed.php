<?php
require_once( ABSPATH . WPINC . '/class-oembed.php' );
/**
 * Fixes issues of the WordPress built-in oEmbed class.
 * 
 * 
 * @since            1.2.0
 * @since            1.3.3.1
 */ 
class FetchTweets_oEmbed extends WP_oEmbed {

    public function __construct() {
        
        parent::__construct();
        
        // Apply a fix for recent Instagram's image url format change.
        add_filter( 'oembed_result', array( $this, 'sanitizeOEmbedResult' ), 10, 3 );
        
    }
    
    /**
     * The do-it-all function that takes a URL and attempts to return the HTML.
     *
     * @see WP_oEmbed::discover()
     * @see WP_oEmbed::fetch()
     * @see WP_oEmbed::data2html()
     *
     * @param string $url The URL to the content that should be attempted to be embedded.
     * @param array $args Optional arguments. Usually passed from a shortcode.
     * @return bool|string False on failure, otherwise the UNSANITIZED (and potentially unsafe) HTML that should be used to embed.
     */
    public function get_html( $sURL, $aArguments=array() ) {
        
        if ( isset( self::$___aCachedURLs[ $sURL ] ) )
            return self::$___aCachedURLs[ $sURL ];
            
        $sHTML = parent::get_html( $sURL, $aArguments );
        
        // Store the cache.
        self::$___aCachedURLs[ $sURL ] = $sHTML;
        
        return $sHTML;
        
    }
        static private $___aCachedURLs = array();

    
    /**
     * Sanitizes oEmebed results.
     * 
     * @remark            Currently only Instagram's images will be checked.
     * @since            1.3.3.1
     */
    public function sanitizeOEmbedResult( $sHTML, $sURL, $aArgs ) {
        
        // A fix for Instagram - src="http://distilleryimage7.ak.instagram.com/0a0e19404b4a11e38add1240bd2c384e_7.jpg  ---> <img src="http://distilleryimage7.ak.instagram.com/0a0e19404b4a11e38add1240bd2c384e_8.jpg
        if ( preg_match( "/:\/\/instagr/", $sURL ) ) {            
            $sHTML = preg_replace_callback( 
                '/(\ssrc=(["\']))(.+_)(\d)(\..+?)(\2)/i',   // '
                array( $this, '___getInstagramImageURLReplaceed' ), 
                $sHTML 
            );
        }  
        return $sHTML;
        
    }
        /**
         * A helper function for the above sanitizeOEmbedResult() method.
         * 
         * @since       1.3.3.2
         * @return      string
         */
        private function ___getInstagramImageURLReplaceed( $aMatches ) {
            
            if ( count( $aMatches ) != 7 ) {
                return $aMatches[ 0 ];
            }
            
            /** 
             * Array (
             *      [0] =>  src="http://distilleryimage0.ak.instagram.com/16d3f5fac72411e2822f22000a9f09ca_7.jpg"
             *      [1] =>  src="
             *      [2] => "
             *      [3] => http://distilleryimage0.ak.instagram.com/16d3f5fac72411e2822f22000a9f09ca_
             *      [4] => 7
             *      [5] => .jpg
             *      [6] => "
             *  )                
             */
            
            // If the image exists, 
            if ( @getimagesize( $aMatches[ 3 ] . $aMatches[ 4 ] . $aMatches[ 5 ] ) ) {                
                return $aMatches[ 0 ];    // do not change
            }
            
            // Increment the suffixed number. e.g. _7 -> _8.
            return $aMatches[ 1 ] 
                . $aMatches[ 3 ] 
                . ( $aMatches[ 4 ] + 1 ) 
                . $aMatches[ 5 ] 
                . $aMatches[ 6 ] ;    
            
        }

    /**
     * Strip any new lines from the HTML.
     *
     * @access private
     * @param string $html Existing HTML.
     * @param object $data Data object from WP_oEmbed::data2html()
     * @param string $url The original URL passed to oEmbed.
     * @return string Possibly modified $html
     * 
     * @since       2.3.9       Added  because PHP threw an error saying: PHP Warning:  call_user_func_array() [<a href='function.call-user-func-array'>function.call-user-func-array</a>]: First argument is expected to be a valid callback, 'FetchTweets_oEmbed::_strip_newlines' was given in Y:\wamp\www\wp40x\wp-includes\plugin.php on line 214
     */
    /* public function _strip_newlines( $html, $data, $url ) {
        if ( false !== strpos( $html, "\n" ) )
            $html = str_replace( array( "\r\n", "\n" ), '', $html );

        return $html;
    } */
        
}