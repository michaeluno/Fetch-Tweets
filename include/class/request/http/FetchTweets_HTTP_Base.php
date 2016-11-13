<?php
/**
 * Fetch Tweets
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Performs HTTP request.
 * 
 * 
 * @since       2.5.0       
 * @filter      apply       fetch_tweets_filter_http_response_cache
 * @version     1.1.0
 */
abstract class FetchTweets_HTTP_Base extends FetchTweets_PluginUtility {
    
    /* Protected properties - which should be overridden in an extended class. */

    /**
     * A cache table class name.
     */
    protected $_sCacheTableClass = 'FetchTweets_DatabaseTable_ft_http_requests';
    
    /**
     * A request type, marked in the cache table.
     */
    protected $_sRequestType = 'http_remote_request';
                         
    /**
     * Stores the default HTTP arguments.
     * @remark      The values will be modified with the formatting method.
     */
    protected $_aArguments =  array(
        'timeout'     => 5,
        'redirection' => 5,
        'httpversion' => '1.0',
        'user-agent'  => 'Fetch Tweets',    // will be reassigned in a formatting method.
        'blocking'    => true,
        'headers'     => array(),
        'cookies'     => array(),
        'body'        => null,
        'compress'    => false,
        'decompress'  => true,
        'sslverify'   => true,
        'stream'      => false,
        'filename'    => null
    ); 

    /* Private properties - extended classes do not have to care about these. */
    
    /**
     * Stores subject urls to fetch HTTP responses.
     */ 
    private $___aURLs = array();
    
    /**
     * Stores the cache duration in seconds.
     */
    private $___iCacheDuration = 86400;
    
    /**
     * Stores the character-set for a last performed HTTP request.
     */
    private $___sLastCharSet = '';
    
    /**
     * Stores the cache table object.
     */
    private $___oCacheTable;
    
    private $___iStatusCode;
    
    /**
     * Stores the site character set.
     * @remark      The value will be automatically set in the constructor.
     */
    static private $___sSiteCharSet;    
    
    /**
     * Sets up properties.
     */
    public function __construct( $asURLs, $iCacheDuration=86400, $aArguments=null ) {
                        
        $this->_aArguments       = $this->_getArgumentsFormatted(
            $this->___getArguments( $aArguments )
        );
        
        // Private properties.
        $this->___aURLs          = $this->___getURLsFormatted( $asURLs );
        self::$___sSiteCharSet   = isset( self::$___sSiteCharSet )
            ? self::$___sSiteCharSet
            : get_bloginfo( 'charset' );
        $this->___iCacheDuration = $iCacheDuration;
        $this->___oCacheTable    = new $this->_sCacheTableClass;
        
    }      
        /**
         * @since       1.1.0
         * @return      array       The formatted array.
         */
        private function ___getURLsFormatted( $asURLs ) {
            $_aFormatted = array();
            $_aURLs      = array_filter( $this->getAsArray( $asURLs ) );
            foreach( $_aURLs as $_sURL ) {
                $_sURL = trim( $_sURL );
                // Set the key to the cache name
                $_aFormatted[ $this->___getCacheName( $_sURL ) ] = $_sURL;
            }
            return $_aFormatted;            
        }
        /**
         * @return      array
         * @since       1.1.0
         */
        private function ___getArguments( $aArguments ) {
            
            $aArguments     = $this->getAsArray( $aArguments ) + $this->_aArguments;                
            $aArguments[ 'user-agent' ] =  FetchTweets_Commons::NAME . '/' . FetchTweets_Commons::VERSION . '; ' . get_bloginfo( 'url' );
            
            // WP 3.7 or above, it should be true.
            $aArguments[ 'sslverify' ] = version_compare( $GLOBALS[ 'wp_version' ], '3.7', '>=' );
            
            return $aArguments;
            
        }
    
    /**
     * Returns the HTTP status code of the last request.
     * @return      string      
     */
    public function getStatusCode() {
        return $this->___iStatusCode;
    }
    /**
     * Returns HTTP body(s).
     * 
     * @remark      Handles character encoding conversion.
     * @return      string|array
     */
    public function get() {
        
        $_aHTTPBodies = array();
        $_aResponses  = $this->___getHTTPResponseWithCache( $this->___aURLs, $this->_aArguments, $this->___iCacheDuration );
        foreach( $_aResponses as $_sURL => $_aoResponse ) {
            $this->___iStatusCode   = wp_remote_retrieve_response_code( $_aoResponse );
            $_aHTTPBodies[ $_sURL ] = $this->___getHTTPBody( $_aoResponse );
            $_sLastIndex = $_sURL;
        }
        return 1 === count( $_aHTTPBodies ) // is single ?
            ? $_aHTTPBodies[ $_sLastIndex ]     // (string)
            : $_aHTTPBodies;              // (array)
        
    }
        /**
         * @since       1.1.0
         * @return      string
         */
        private function ___getHTTPBody( $aoResponse ) {
            
            $_sHTTPBody         = is_wp_error( $aoResponse )
                ? $aoResponse->get_error_message()
                : wp_remote_retrieve_body( $aoResponse );         
            
            $_sCharSetFrom      = $this->___getCharacterSet( $aoResponse );
            $_sCharSetTo        = self::$___sSiteCharSet;

            // Encode the document from the source character-set to the site character-set.
            return ( strtoupper( $_sCharSetTo ) === strtoupper( $_sCharSetFrom ) )
                ?  $_sHTTPBody
                : $this->___getCharacterEncodingConverted(
                    $_sHTTPBody,
                    $_sCharSetTo,   // to
                    $_sCharSetFrom, // from
                    false           // no html-entities conversion
                );

        }
            /**
             * Converts a given string into a specified character set.
             * @since       1.1.0
             * @return      string      The converted string.
             * @see         http://php.net/manual/en/mbstring.supported-encodings.php
             * @param       string          $sText                      The subject text string.
             * @param       string          $sCharSetTo                 The character set to convert to.
             * @param       string|boolean  $bsCharSetFrom              The character set to convert from. If a character set is not specified, it will be auto-detected.
             * @param       boolean         $bConvertToHTMLEntities     Whether or not the string should be converted to HTML entities.
             */
            private function ___getCharacterEncodingConverted( $sText, $sCharSetTo='', $bsCharSetFrom=true, $bConvertToHTMLEntities=false ) {
                
                if ( ! function_exists( 'mb_detect_encoding' ) ) {
                    return $sText;
                }
                
                $sCharSetTo = $sCharSetTo
                    ? $sCharSetTo
                    : self::$___sSiteCharSet;
                
                $_bsDetectedEncoding = $bsCharSetFrom && is_string( $bsCharSetFrom )
                    ? $bsCharSetFrom
                    : $this->___getDetectedCharacterSet(
                        $sText,
                        $bsCharSetFrom              
                    );
                $sText = false !== $_bsDetectedEncoding
                    ? mb_convert_encoding( 
                        $sText, 
                        $sCharSetTo, // encode to
                        $_bsDetectedEncoding // from
                    )
                    : mb_convert_encoding( 
                        $sText, 
                        $sCharSetTo // encode to      
                        // auto-detect
                    );
                
                if ( $bConvertToHTMLEntities ) {            
                    $sText  = mb_convert_encoding( 
                        $sText, 
                        'HTML-ENTITIES', // to
                        $sCharSetTo  // from
                    );
                }
                
                return $sText;
                
            }
                /**
                 * 
                 * @return      boolean|string      False when not found. Otherwise, the found encoding character set.
                 * @since       1.1.0
                 */
                private function ___getDetectedCharacterSet( $sText, $sCandidateCharSet='' ) {
                    
                    $_aEncodingDetectOrder = array( self::$___sSiteCharSet, 'auto', );
                    if ( is_string( $sCandidateCharSet ) && $sCandidateCharSet ) {
                        array_unshift( $_aEncodingDetectOrder, $sCandidateCharSet );
                    }        

                    // Returns false or the found encoding character set
                    return mb_detect_encoding( 
                        $sText, // subject string
                        $_aEncodingDetectOrder, // candidates
                        true // strict detection - true/false
                    );
                    
                }    
                
        /**
         * Returns raw HTTP responses.
         * @return      array        Response array.
         */
        private function ___getHTTPResponseWithCache( array $aURLs, array $aArguments=array(), $iCacheDuration=86400 ) {
            
            // Retrieve available caches.
            $_aValidCaches   = $this->___getCaches( $aURLs, $aArguments, $iCacheDuration );
            
            // Check if caches exist one by one and if not, get the response and set a cache.
            $_aHTTPResponses = array();
            foreach( $aURLs as $_sURL ) {
                
                // If a cache is avilable, use it.
                if ( isset( $_aValidCaches[ $_sURL ] ) ) {
                    $_aHTTPResponses[ $_sURL ] = $_aValidCaches[ $_sURL ];
                    continue;
                }
                                
                // Otherwise, perform an HTTP request and cache the result.
                $_aHTTPResponses[ $_sURL ] = $this->_getHTTPResponse( $_sURL, $aArguments );
                $this->___setCache( $_sURL, $_aHTTPResponses[ $_sURL ], $iCacheDuration );
                
            }
            return $_aHTTPResponses;
            
        }    
            /**
             * @return      array
             */
            private function ___getCaches( $aURLs, $aArguments, $iCacheDuration ) {
                                
                $_aValidCaches   = array();
                foreach( $this->___getCachesFromDatabase( $aURLs, $iCacheDuration ) as $_aCache ) {
                    
                    $_aCache = $this->__getCacheFormatted( $_aCache );
                    if ( ! isset( $_aCache[ 'data' ] ) ) {
                        continue;
                    }
                    
                    /**
                     * Allow external components to modify the remained time, 
                     * which can be used to trick the below check and return the stored data anyway.
                     * So the cache renewal event can be scheduled in the background.
                     */
                    $_aCache = apply_filters(
                        'fetch_tweets_filter_http_response_cache',
                        $_aCache,
                        $iCacheDuration,
                        $aArguments,
                        $this->_sRequestType
                    );
                    
                    // Set a valid item.
                    if ( $_aCache[ 'remained_time' ] && $_aCache[ 'data' ] ) {
                        $this->___sLastCharSet = $_aCache[ 'charset' ];
                        $_aValidCaches[ $_aCache[ 'request_uri' ] ] = $_aCache[ 'data' ];
                    }
                    
                }
                return $_aValidCaches;
                
            }
                /**
                 * @return      array
                 */
                private function ___getCachesFromDatabase( array $aURLs, $iCacheDuration ) {
                    return 0 === $iCacheDuration
                        ? array()
                        : $this->___oCacheTable->getCache(  
                            array_keys( $aURLs ), // multiple names - the url array is indexed with cache names
                            $iCacheDuration
                        );                      
                }                    
                /**
                 * @return      array
                 * @since       1.1.0
                 */
                private function __getCacheFormatted( $aCache ) {
                    return $aCache + array( 
                        'remained_time' => 0,
                        'charset'       => null,
                        'data'          => null,
                        'request_uri'   => null,
                        'name'          => null,
                    );
                }
            
            /**
             * Sets a cache by url.
             * It internally sets a cache name.
             * @return      boolean     
             * @todo        Examine the return value as it is not tested.
             * @param       string              $sURL
             * @param       WP_ERROR|array      $aoResponse
             * @param       integer             $iCacheDuration     The cache duration in seconds. Pass `0` to renew the cache.
             */
            private function ___setCache( $sURL, $aoResponse, $iCacheDuration=86400 ) {
                
                $_sCharSet    = $this->___getCharacterSet( $aoResponse );            

                $_bResult     = $this->___oCacheTable->setCache( 
                    $this->___getCacheName( $sURL ), // name
                    $aoResponse,
                    $iCacheDuration // when 0 is passed, use a default value of 86400 (one day). So pass 0 to renew the cache.
                        ? ( integer ) $iCacheDuration
                        : 86400, // cache life span
                    array( // extra column items
                        'request_uri' => $sURL,
                        'type'        => $this->_sRequestType,
                        'charset'     => $_sCharSet,
                    )
                );        
                $this->___sLastCharSet = $_sCharSet;
                return $_bResult;
                
            }
                                                  

    /**
     * Returns the response's character set by the url.
     * 
     * @remark      This should be used after performing _getResponses().
     * @since       1.0.0
     * @return      string
     * @param       string      $sURLOrCacheName    If specified, it checks the character set from the cache.
     */
    public function getCharacterSet( $sURLOrCacheName='' ) {
        if ( ! $sURLOrCacheName ) {
            return $this->___sLastCharSet;
        }
        return $this->___getCharacterSetFromCache( 
            $this->___getCacheName( $sURLOrCacheName ) 
        );
    }
        /**
         * 
         * @return      string
         */
        private function ___getCharacterSetFromCache( $sCacheName ) {
            return $this->getElement( 
                $this->___oCacheTable->getCache( $sCacheName ),   // row - single item returns a single row
                array( 'charset' ), 
                ''  // default
            );
        }           
            
    /**
     * Deletes the cache of the provided URL.
     * 
     * @access      public      Accessed publicly.
     */
    public function deleteCache() {
        
        // @deprecated
        // foreach( $this->___aURLs as $_sCacheName => $_sURL ) {            
            // $this->___oCacheTable->deleteCache( $_sCacheName );    
        // }
        
        $this->___oCacheTable->deleteCache( array_keys( $this->___aURLs ) );
        
    }
    
    /* Protected Methods - should be overridden in each extended class. */
    
    /**
     * @return      array
     * @since       1.1.0
     */
    protected function _getArgumentsFormatted( $aArguments ) {
        return apply_filters(
            'fetch_tweets_filter_http_request_arguments',
            $aArguments
        );
    }
    
    /**
     * Performs HTTP request.
     * @remark      Override this method in each extended class.
     * @remark      this does not set cache.
     * @since       1.0.0
     * @return      WP_Error|array
     */
    protected function _getHTTPResponse( $sURL, array $aArguments ) {
        return function_exists( 'wp_safe_remote_get' )
            ? wp_safe_remote_get( $sURL, $aArguments )
            : wp_remote_get( $sURL, $aArguments );
    }
    
    
    /* Utility Methods - called from multiple methods */
    
    /**
     * Generates a cache name from the given url.
     * @return      string
     * @since       1.1.0
     */
    private function ___getCacheName( $sString ) {
        $_sCacheName = filter_var( $sString, FILTER_VALIDATE_URL )
            ? 'url_type_md5_' . md5( $this->_sRequestType . '_' . $sString )
            : $sString;
        return apply_filters(
            'fetch_tweets_filter_http_response_cache_name',
            $_sCacheName,
            $sString,
            $this->_sRequestType
        );
    }
    
    /**
     * 
     * @return      string
     */
    private function ___getCharacterSet( $aoHTTPResponse ) {
        if ( is_wp_error( $aoHTTPResponse ) ) {
            return self::$___sSiteCharSet;
        }
        return $this->___getCharacterSetFromResponseHeader( wp_remote_retrieve_headers( $aoHTTPResponse ) );
    }
        /**
         * 
         * @return      string      The found character set.
         * e.g. ISO-8859-1, utf-8, Shift_JIS
         * 
         * @remark  The value set to the header charset should be case-insensitive.
         * @see     http://www.iana.org/assignments/character-sets/character-sets.xhtml
         * @since   1.1.0
         */
        private function ___getCharacterSetFromResponseHeader( $asHeaderResponse ) {
            
            $_sContentType = '';
            if ( is_string( $asHeaderResponse ) ) {
                $_sContentType = $asHeaderResponse;
            } 
            // It should be an array then.
            else if ( isset( $asHeaderResponse[ 'content-type' ] ) ) {
                $_sContentType = $asHeaderResponse[ 'content-type' ];
            } 
            else {
                foreach( $asHeaderResponse as $_iIndex => $_sHeaderElement ) {
                    if ( false !== stripos( $_sHeaderElement, 'charset=' ) ) {
                        $_sContentType = $asHeaderResponse[ $_iIndex ];
                    }
                }
            }

            $_bFound = preg_match(
                '/charset=(.+?)($|[;\s])/i',  // needle
                $_sContentType, // haystack
                $_aMatches
            );
            return isset( $_aMatches[ 1 ] )
                ? $_aMatches[ 1 ]
                : '';
                
        }
    
}
