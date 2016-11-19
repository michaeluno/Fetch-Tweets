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
        add_filter( 'oembed_result', array( __CLASS__, 'replyToSanitizeOEmbedResult' ), 10, 3 );
        
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
        
        if ( isset( self::$___aCachedURLs[ $sURL ] ) ) {            
            return self::$___aCachedURLs[ $sURL ];
        }
            
        $sHTML = parent::get_html( $sURL, $aArguments );
        
        // Store the cache.
        self::$___aCachedURLs[ $sURL ] = $sHTML;
        
        return $sHTML;
        
    }
        static private $___aCachedURLs = array();

	/**
	 * Attempts to discover link tags at the given URL for an oEmbed provider.
     * 
	 * @param string $url The URL that should be inspected for discovery `<link>` tags.
	 * @return false|string False on failure, otherwise the oEmbed provider URL.
     * @remark      Overriding this method to use a custom HTTP client.
	 */
	public function discover( $url ) {
		$providers = array();
		$args = array(
			'limit_response_size' => 153600, // 150 KB
		);

		/**
		 * Filters oEmbed remote get arguments.
		 *
		 * @since 4.0.0
		 *
		 * @see WP_Http::request()
		 *
		 * @param array  $args oEmbed remote get arguments.
		 * @param string $url  URL to be inspected.
		 */
		$args = apply_filters( 'oembed_remote_get_args', $args, $url );

		// Fetch URL content
        // Use a custom HTTP client.
        $_oHTTP     = new FetchTweets_HTTP_Get( 
            $url,
            $this->___getCacheDuration(),
            array(
                'timeout'     => 5,
            ) + $args
        );    
        $_oHTTP->setType( 'oembed_get' ); // mark the request type
		$request = $_oHTTP->get(
            $this->___shouldSetCache() ? 0 : 3
        );
        
		if ( $html = wp_remote_retrieve_body( $request ) ) {

			/**
			 * Filters the link types that contain oEmbed provider URLs.
			 *
			 * @since 2.9.0
			 *
			 * @param array $format Array of oEmbed link types. Accepts 'application/json+oembed',
			 *                      'text/xml+oembed', and 'application/xml+oembed' (incorrect,
			 *                      used by at least Vimeo).
			 */
			$linktypes = apply_filters( 'oembed_linktypes', array(
				'application/json+oembed' => 'json',
				'text/xml+oembed' => 'xml',
				'application/xml+oembed' => 'xml',
			) );

			// Strip <body>
			if ( $html_head_end = stripos( $html, '</head>' ) ) {
				$html = substr( $html, 0, $html_head_end );
			}

			// Do a quick check
			$tagfound = false;
			foreach ( $linktypes as $linktype => $format ) {
				if ( stripos($html, $linktype) ) {
					$tagfound = true;
					break;
				}
			}

			if ( $tagfound && preg_match_all( '#<link([^<>]+)/?>#iU', $html, $links ) ) {
				foreach ( $links[1] as $link ) {
					$atts = shortcode_parse_atts( $link );

					if ( !empty($atts['type']) && !empty($linktypes[$atts['type']]) && !empty($atts['href']) ) {
						$providers[$linktypes[$atts['type']]] = htmlspecialchars_decode( $atts['href'] );

						// Stop here if it's JSON (that's all we need)
						if ( 'json' == $linktypes[$atts['type']] )
							break;
					}
				}
			}
		}

		// JSON is preferred to XML
		if ( !empty($providers['json']) )
			return $providers['json'];
		elseif ( !empty($providers['xml']) )
			return $providers['xml'];
		else
			return false;
	}  
        /**
         * @return      integer
         */
        private function ___getCacheDuration() {
            return FetchTweets_Option::get( array( 'oembed', 'cache_duration', 'size' ), 1 )
                * FetchTweets_Option::get( array( 'oembed', 'cache_duration', 'unit' ), 86400 );
        }
        /**
         * @return      boolean
         */
        private function ___shouldSetCache() {
            return FetchTweets_Option::get( array( 'oembed', 'cache_discover' ), true );
        }
        
    
    /**
     * Sanitizes oEmebed results.
     * 
     * @remark          Currently only Instagram's images will be checked.
     * @since           1.3.3.1
     * @since           2.5.0       Changed the scope to static.
     */
    static public function replyToSanitizeOEmbedResult( $sHTML, $sURL, $aArgs ) {
        
        // A fix for Instagram - src="http://distilleryimage7.ak.instagram.com/0a0e19404b4a11e38add1240bd2c384e_7.jpg  ---> <img src="http://distilleryimage7.ak.instagram.com/0a0e19404b4a11e38add1240bd2c384e_8.jpg
        if ( preg_match( "/:\/\/instagr/", $sURL ) ) {            
            $sHTML = preg_replace_callback( 
                '/(\ssrc=(["\']))(.+_)(\d)(\..+?)(\2)/i',   // '
                array( __CLASS__, '___getInstagramImageURLReplaceed' ), 
                $sHTML 
            );
        }  
        return $sHTML;
        
    }
        /**
         * A helper function for the above replyToSanitizeOEmbedResult() method.
         * 
         * @since       1.3.3.2
         * @return      string
         */
        static private function ___getInstagramImageURLReplaceed( $aMatches ) {
            
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