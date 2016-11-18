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
 * Fetches and displays tweets.
 */
class FetchTweets_Output_Tweet extends FetchTweets_Output_Base {
    
    /**
     * Stores the direct set arguments.
     */
    private $___aDirectArguments = array();
    
    /**
     * Retrieves the arguments.
     * 
     * @return      array       An argument array.
     */    
    protected function _getArguments( $aArguments ) {
        $_oDirectArgument = new FetchTweets_Output_Tweet___Argument_Direct( $aArguments );
        $this->___aDirectArguments = $_oDirectArgument->get();
        $_oArgument       = new FetchTweets_Output_Tweet___Argument( $this->___aDirectArguments );
        return $_oArgument->get();
    }
        
    
    /**
     * Returns the output of tweets by the given arguments.
     * 
     * @remark      called from the shortcode callback.
     */
    public function get() {   

        $_asTweets   = $this->getTweets();  
        if ( is_string( $_asTweets ) ) {
            return $_asTweets;  // error
        }

        // Output the tweets by applying the template 
        $_aTweets = $_asTweets;
        $_oTemplate = new FetchTweets_Output_Tweet___Template( $_aTweets, $this->_aArguments );
        return $_oTemplate->get();  
        
    }
  
    /**
     * Fetches tweets based on the argument.
     * 
     * When tags are set, multiple requests are performed. So be careful about the handling of each arguments per request.
     * 
     * @remark      The `$_aArguments` property will be updated.
     * @since       2.5.0
     * @return      array
     */
    public function getTweets() {    
        
        $_aTweets             = array();
        $_aArgumentsByRequest = $this->___getArgumentSetsByRequests( $this->___aDirectArguments );

        // Update the overall arguments with the first parsing argument set.
        foreach ( $_aArgumentsByRequest as $_aArguments ) {
            $this->_aArguments = $this->___aDirectArguments + $_aArguments + $this->_aArguments;
            break;
        }        
        
        // Retrieve tweets.
        foreach( $_aArgumentsByRequest as $_aArguments ) {
            $_aArguments   = $_aArguments + $this->_aArguments;
            $_sClassName   = 'FetchTweets_TwitterAPI_' . $_aArguments[ 'tweet_type' ];
            $_oRequest     = new $_sClassName( $_aArguments );
            $_aTweets      = array_merge( $_oRequest->get(), $_aTweets );
        }

        $_sError    = $this->___getErrorMessage( $_aTweets );
        if ( $_sError ) {
            return $_sError;
        }
        
        // Format tweets
        $_oFormatter   = new FetchTweets_Output_Tweet___Format( 
            $_aTweets, 
            $this->_aArguments  // overall arguments
        );
        return $_oFormatter->get();
        
    }
    
        /**
         * Generates error message from the tweets array.
         * 
         * @since       2.4.7
         * @since       2.4.8       Changed the scope to public to let some extension plugins access this method.
         * @return      string      the error message. An empty string on no error.
         */
        private function ___getErrorMessage( array $_aTweets ) {
                    
            if ( empty( $_aTweets ) ) {
                return $this->getElement( $this->_aArguments, 'show_error_on_no_result' )
                    ? __( 'No result could be fetched.', 'fetch-tweets' )
                    : '';
            }
            
            $_aError = $this->getElement( $_aTweets, array( 'errors', 0, ) ); 
            if ( isset( $_aError[ 'message' ], $_aError[ 'code' ] ) ) {
                return '<strong>' . FetchTweets_Commons::NAME . '</strong>: ' 
                    . $_aError[ 'message' ] . ' ' 
                    . __( 'Code', 'fetch-tweets' ) . ': ' . $_aError[ 'code' ];
            }
            
            $_sError = $this->getElement( $_aTweets, 'error', '' );
            if ( $_sError && is_string( $_aTweets[ 'error' ] ) ) {
                return '<strong>' . FetchTweets_Commons::NAME . '</strong>: ' 
                    . $_aTweets[ 'error' ];    
            }       
            
            return '';
            
        }
            
        /**
         * Populates arguments by each request.
         * 
         * Usually, only one set of argument is returned 
         * but if multiple unit IDs are passed, the multiple sets of arguments will be returned.
         * 
         * @remark      Each argument set is not formatted so there could be missing arguments.
         * @return      array
         */
        private function ___getArgumentSetsByRequests( array $aDirectArguments ) {
                        
            // If the user sets direct arguments and the request type is determined, format the arguments with the direct arguments.
            $_sType           = $this->___getRequestTypeFromDirectArguments( $aDirectArguments );
            if ( '' !== $_sType ) {
                $aDirectArguments[ 'tweet_type' ] = $_sType;
                return array( $aDirectArguments );
            }
            
            // Otherwise, the rule ID is specified so construct arguments with the rule arguments.
            return $this->___getArgumentsFromRuleIDs( $aDirectArguments[ 'id' ], $aDirectArguments );
                        
        }
            /**
             * @return      string
             */
            private function ___getRequestTypeFromDirectArguments( $aDirectArguments ) {
                            
                // Custom query URIs.
                if ( isset( $aDirectArguments[ 'custom_query' ] ) ) {
                    return 'custom_query';
                }
                
                // Search keywords.
                if ( isset( $aDirectArguments[ 'q' ] ) ) {   
                    return 'search';
                }
                
                // Screen names.
                if ( isset( $aDirectArguments[ 'screen_name' ] ) ) {
                    return 'screen_name';
                }
                
                // Lists
                if ( isset( $aDirectArguments[ 'list_id' ] ) ) {
                    return 'list';
                }
                
                // Tweet ID
                if ( isset( $aDirectArguments[ 'tweet_id' ] ) ) {
                    return 'tweet_id';
                }            
                
                // Time line by registered account. Be careful that private lists also use `account_id`.
                if ( isset( $aDirectArguments[ 'account_id' ] ) ) {
                    return 'home_timeline';
                }
                
                // Otherwise, unknown.
                return '';
                
            } 
            /**
             * @remark      Each unit has its own request type (tweet type).
             * @return      array       arguments by request type
             */
            private function ___getArgumentsFromRuleIDs( array $aRuleIDs, $aDirectArguments ) {

                $_aArgumentSets = array();
                foreach( $aRuleIDs as $_iPostID ) {
                    
                    // Rule arguments here refers to the arguments set in the rule.
                    $_aRuleArguments = array();
                    $_aRuleArguments[ 'tweet_type' ]  = get_post_meta( $_iPostID, 'tweet_type', true );
                    $_aRuleArguments[ 'count' ]       = get_post_meta( $_iPostID, 'item_count', true );                  
                    $_aRuleArguments[ 'include_rts' ] = get_post_meta( $_iPostID, 'include_rts', true );
                    $_aRuleArguments[ 'cache' ]       = get_post_meta( $_iPostID, 'cache', true );
                    $_sMethodName                 = "___getArguments_{$_aRuleArguments[ 'tweet_type' ]}";
                    $_aRuleArguments = $this->uniteArrays( 
                        $aDirectArguments,
                        $_aRuleArguments + $this->$_sMethodName( $_iPostID )
                    );
                    $_aArgumentSets[ $_iPostID ] = $_aRuleArguments;
                    
                }             
                return $_aArgumentSets;
                
            }
                /**
                 * @return      array
                 */
                private function ___getArguments_search( $iPostID ) {
                    
                    $_aArguments = array();
                    $_aArguments[ 'q' ]                     = get_post_meta( $iPostID, 'search_keyword', true );    
                    $_aArguments[ 'result_type' ]           = get_post_meta( $iPostID, 'result_type', true );
                    $_aArguments[ 'lang' ]                  = get_post_meta( $iPostID, 'language', true );
                    $_aArguments[ 'until' ]                 = get_post_meta( $iPostID, 'until', true );
                    $_aArguments[ 'geocentric_coordinate' ] = get_post_meta( $iPostID, 'geocentric_coordinate', true );
                    $_aArguments[ 'geocentric_radius' ]     = get_post_meta( $iPostID, 'geocentric_radius', true );
                    
                    if ( 
                        is_array( $_aArguments[ 'geocentric_coordinate' ] ) && is_array( $_aArguments[ 'geocentric_radius' ] )
                        && isset( $_aArguments[ 'geocentric_coordinate' ][ 'latitude' ], $_aArguments[ 'geocentric_radius' ][ 'size' ] ) 
                        && $_aArguments[ 'geocentric_coordinate'][ 'latitude' ] !== '' && $_aArguments[ 'geocentric_coordinate' ][ 'longitude' ] !== ''    // the coordinate can be 0
                        && $_aArguments[ 'geocentric_radius' ][ 'size' ] !== '' 
                    ) {
                        // "latitude,longitude,radius",
                        $_aArguments[ 'geocode' ] = trim( $_aArguments[ 'geocentric_coordinate' ][ 'latitude' ] ) . "," . trim( $_aArguments[ 'geocentric_coordinate' ][ 'longitude' ] ) 
                            . "," . trim( $_aArguments[ 'geocentric_radius' ][ 'size' ] ) . $_aArguments[ 'geocentric_radius' ][ 'unit' ] ;
                    }            
                    return $_aArguments;
                 
                }
                /**
                 * @return      array
                 */
                private function ___getArguments_list( $iPostID ) {
                    $_aArguments = array();
                    $_aArguments[ 'account_id' ] = get_post_meta( $iPostID, 'account_id', true );
                    $_aArguments[ 'mode' ]       = get_post_meta( $iPostID, 'mode', true );
                    $_aArguments[ 'list_id' ]    = get_post_meta( $iPostID, 'list_id', true );
                    return $_aArguments;
                }       
                /**
                 * @return      array
                 */
                private function ___getArguments_home_timeline( $iPostID ) {
                    $_aArguments = array();
                    $_aArguments[ 'account_id' ]      = get_post_meta( $iPostID, 'account_id', true );
                    $_aArguments[ 'exclude_replies' ] = get_post_meta( $iPostID, 'exclude_replies', true );
                    return $_aArguments;                    
                }
                /**
                 * @return      array
                 */
                private function ___getArguments_feed( $iPostID ) {
                    $_aArguments = array();
                    $_aArguments[ 'json_url' ] = get_post_meta( $iPostID, 'json_url', true );
                    return $_aArguments;
                }
                /**
                 * @return      array
                 */
                private function ___getArguments_custom_query( $iPostID ) {
                    $_aArguments = array();
                    $_aArguments[ 'custom_query' ] = get_post_meta( $iPostID, 'custom_query', true );
                    $_aArguments[ 'response_key' ] = get_post_meta( $iPostID, 'response_key', true );
                    return $_aArguments;
                }
                /**
                 * @return      array
                 */
                private function ___getArguments_tweet_id( $iPostID ) {
                    $_aArguments = array();
                    $_aArguments[ 'tweet_id' ] = get_post_meta( $iPostID, 'tweet_id', true );
                    return $_aArguments;
                }
                /**
                 * @return      array
                 */
                private function ___getArguments_screen_name( $iPostID ) {                    
                    $_aArguments = array();
                    $_aArguments[ 'screen_name' ]     = get_post_meta( $iPostID, 'screen_name', true );
                    $_aArguments[ 'exclude_replies' ] = get_post_meta( $iPostID, 'exclude_replies', true );
                    return $_aArguments;
                }   
 
}
