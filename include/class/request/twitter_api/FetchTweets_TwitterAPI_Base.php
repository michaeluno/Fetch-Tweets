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
 * Handles Twitter API requests.
 * 
 * @since             2.5.0
 */
class FetchTweets_TwitterAPI_Base extends FetchTweets_PluginUtility {
    
    /**
     * The arguments for the API request.
     */
    protected $_aArguments = array();
    
    /**
     * The dimensional path (keys) which locates the rate limit element in the response array.
     */
    protected $_aRateLimitPath = array();
    
    /**
     * The dimensional path (keys) which locates the tweets element in the response array.
     */
    protected $_aTargetElementPath = array();
    
    protected $_oOption;
    
    protected $_aCredentials;
    
    private $___oTwitterOAuth;
    private $___oCache;
    
    /**
     * Sets up properties.
     * @since       2.5.0
     */
    public function __construct( $aArguments ) {
        
        $this->_oOption          = FetchTweets_Option::getInstance();
        $this->_aArguments       = $this->_getArguments( $aArguments );
        $this->_aCredentials     = $this->___getApplicationKeys( $aArguments );
        $this->___oCache         = new FetchTweets_TwitterAPI___CacheHandler;
        $this->___oTwitterOAuth  = new FetchTweets_TwitterOAuth( 
            $this->_aCredentials[ 'consumer_key' ],
            $this->_aCredentials[ 'consumer_secret' ], 
            $this->_aCredentials[ 'access_token' ], 
            $this->_aCredentials[ 'access_secret' ]
        );        
                
    }
    
        /**
         * Returns the arguments.
         * 
         * Handle formatting here.
         * @return      array
         * @since       2.5.0
         */
        protected function _getArguments( $aArguments ) {
            return $aArguments + $this->_aArguments;
        }
    
        /**
         * Returns the application keys for the Twitter API credentials.
         * 
         * @since           2.5.0
         * @return          array
         */
        private function ___getApplicationKeys( $_aArguments ) {
                        
            // If the user sets keys explicitly, use them.
            $_aKeys =  array(
                'consumer_key'      => $this->getElement( $_aArguments, 'consumer_key' ),
                'consumer_secret'   => $this->getElement( $_aArguments, 'consumer_secret' ),
                'access_token'      => $this->getElement( $_aArguments, 'access_token' ),
                'access_secret'     => $this->getElement( $_aArguments, 'access_secret' ),
            );
            if ( 4 === count( array_filter( $_aKeys ) ) ) {
                return $_aKeys;
            }
            
            // If the `account_id` is set, it is a private one. Private lists and timelines use this argument.
            $_iAccountID = $this->getElement( $_aArguments, array( 'account_id' ), 0 );            
            
            /**
             * Let extensions set custom credentials.
             */
            return apply_filters( 
                'fetch_tweets_filter_random_credentials',  
                $this->_oOption->getCredentialsByID( $_iAccountID )
            );
            
        }   
            
    /**
     * @return      array
     * @since       2.5.0
     */
    public function get() {
        
        $_aResponse = $this->_getAPIResponse( $this->_getRequestURI() );
        return $this->_getTweetsExtracted( $_aResponse );
       
    }
        /**
         * @return      array
         */
        protected function _getTweetsExtracted( $aResponse ) {
            return empty( $this->_aTargetElementPath )
                ? $this->getAsArray( $aResponse )
                : $this->getElementAsArray( $aResponse, $this->_aTargetElementPath );            
            
        }
        
        /**
         * @return      string
         * @remark      Override this method in an extended class.
         */
        protected function _getRequestURI() {
            return 'https://api.twitter.com/1.1/'; // ... API query parameters continue.
        }
    
        /**
         * Performs the Twitter API request by the given URI.
         * 
         * This checks the existent caches and if it's not expired it uses the cache.
         * 
         * @since       1.2.0
         * @since       2.2.0       Changed the scope to public to let extension plugins to use this method.
         * @since       2.3.5       Added the $aRateLimitKey parameter.
         * @param       string      $sRequestURI        The GET request URI with the query.
         * @return      array
         */ 
        protected function _getAPIResponse( $sRequestURI ) {
            
            $_sRequestURI    = $this->___getRequestURISanitized( $sRequestURI );            
            
            // Check if it has been already requested. If so return an error.
            if ( $this->hasBeenCalled( $_sRequestURI ) ) {
                return array( 
                    'error' => __( 'Excessive requests have been made. Please reload the page.', 'fetch-tweets' ) 
                );
            }
            
            // Check the rate limit.
            if ( $this->___hasExceededRateLimit() ) {
// @todo Returns the cached data anyway if possible.  
                return array( 
                    'error' => __( 'The number of API requests exceeded the rate limit. Please try it later.', 'fetch-tweets' ) 
                );
            }
            
            $_iCacheDuration = ( integer ) $this->getElement( $this->_aArguments, array( 'cache' ), 1200 );
            
            // Check a cache and use it if available.      
            if ( ! $this->getElement( $this->_aArguments, array( 'force_caching' ), false ) ) {
                $_aCache = $this->___oCache->get( $sRequestURI, $_iCacheDuration );
                if ( ! empty( $_aCache ) ) {
                    
var_dump( 'using cache: ' . $sRequestURI );
FetchTweets_Debug::log( 'using cache: ' . $sRequestURI );
        
                    return $_aCache;
                }
            }
                    
            // Perform the API request.
            $_aResponse = $this->___oTwitterOAuth->get( $_sRequestURI );   
            $_aResponse = $this->getAsArray( $_aResponse );

            // Set cache
            $this->___oCache->set( $sRequestURI, $_aResponse, $_iCacheDuration );
var_dump( 'setting cache: ' . $sRequestURI );
FetchTweets_Debug::log( 'setting cache: ' . $sRequestURI );
            return $_aResponse;

        }    
        
            private function ___getRequestURISanitized( $sRequestURI ) {
                return trim( $sRequestURI );    
            }    
           
            /**
             * @return      boolean
             */
            private function ___hasExceededRateLimit() {
                
                // There are some request types do not have rate limit such as feed (plugin json feed).
                if ( empty( $this->_aRateLimitPath ) ) {
                    return false;
                }
                
                $_aDimensionalPath = $this->_aRateLimitPath;
                array_unshift( $_aDimensionalPath , 'resources' ); // prepend an item.
                $_aDimensionalPath[] = 'remaining';  // append an item.
                
                // Extract the value.
                $_iRemaining = $this->getElement( 
                    $this->___getRateLimitStatus(), // subject array
                    $_aDimensionalPath,  // dimensional path
                    -1  // default - will yields true in the below boolean casting
                );
                return ( boolean ) ! $_iRemaining;
                    
            }
                /**
                 * Returns the number of remaining requests from the given key.
                 * 
                 * @since       2.3.5
                 * @return      array
                 */
                private function ___getRateLimitStatus() {
                    
                    $_aStatusKeys   = apply_filters( 'fetch_tweets_filter_request_rate_limit_status_keys', array( 'statuses', 'search', 'lists' ) );    // keys can be added such as 'help', 'users' etc
                    $this->___oTwitterOAuth->setCacheDuration( 15 * 60 );
                    $_iOriginalCachingMode = $this->___oTwitterOAuth->iCachingMode;
                    $this->___oTwitterOAuth->setCachingMode( 0 );   // use cache if available
                    $_aResult = $this->___oTwitterOAuth->get( 'https://api.twitter.com/1.1/application/rate_limit_status.json?resources=' . implode( ',', $_aStatusKeys ) );
                    $this->___oTwitterOAuth->setCachingMode( $_iOriginalCachingMode );                  
                    return $_aResult;
              
                }
 

 
}
