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
     * Retrieves the arguments.
     * 
     * @param            array            $aArguments 
     *     id - The post id. default: null. e.g. 125  or 124, 235
     *     tag - default: null. e.g. php or php, WordPress. In this method this tag is only used to pass the argument to the template filter.
     *  sort - default: descending. Either ascending, descending, or random can be used.
     *     count - default: 20
     *     operator - default: AND. Either AND or IN or NOT IN is used.
     *  q - default: null e.g. WordPress
     *  screen_name - default: null e.g. miunosoft
     *  include_rts - default: 0. Either 1 or 0.
     *  exclude_replies - default: 0. Either 1 or 0.
     *  cache - default: 1200
     *    lang - default: null.  
     *    result_type - default: mixed
     *    list_id - default: null. e.g. 8044403
     *    twitter_media - ( boolean ) determines whether the Twitter media should be displayed or not. Currently only photos are supported by the Twitter API.
     *    external_media - ( boolean ) determines whether the plugin attempts to replace external media links to embedded elements.
     * show_error_on_no_result     - 2.4.7+ default: true
     * apply_template_on_no_result - 2.4.8+ default: true
     * Template options
     *    template - the template slug.
     *    width - 
     *    width_unit - 
     *    height    - 
     *    height_unit - 
     *    avatar_size - default: 48 
     * 
     * @return      array       An argument array.
     */    
    protected function _getArguments( $aArguments ) {
        $aArguments = $this->getAsArray( $aArguments );
        $aArguments = $aArguments + $this->_aArguments;
        return $this->___getArgumentsFormatted( $aArguments );
    }
    
        /**
         * Formats the argument array.
         * @return      array
         * @since       2.5.0
         */
        private function ___getArgumentsFormatted( $aArguments ) {
            
            $_aArguments         = $this->uniteArrays( 
                $aArguments,         // passed arguments for the API request
                $this->_oOption->aOptions[ 'default_values' ]  // user saved options
                + $this->_oOption->aStructure_DefaultParams  // class0defined option structure
                + $this->_oOption->aStructure_DefaultTemplateOptions // class-defined template option structure
            );
            $_aArguments[ 'id' ] = $this->___getUnitIDs( $_aArguments );
            
            return $_aArguments;
            
        }    
            /**
             * @return      array
             */
            private function ___getUnitIDs( $_aArguments ) {
                
                $_aTags    = $this->___getTags( $_aArguments );
                if ( ! empty( $_aTags ) ) {
                    return $this->___getUnitIDsByTag( $_aTags, $_aArguments );
                }
                
                $_asIDs    = $this->getElement( 
                    $_aArguments, 
                    array( 'ids' ), 
                    $this->getElement( $_aArguments, array( 'id' ), '' )  // default - backward compatibility
                );
                $_aIDs     = is_array( $_asIDs ) 
                    ? $_asIDs 
                    : $this->getStringIntoArray( ( string ) $_asIDs, ',' );
                return $_aIDs;
                
            }
                /**
                 * @return      array
                 */
                private function ___getTags( $aArguments ) {
                    $_sTags    = $this->getElement( 
                        $aArguments, 
                        array( 'tags' ), 
                        $this->getElement( 
                            $aArguments, 
                            array( 'tag' ), // backward compatibility
                            '' 
                        ) 
                    );
                    return $this->getStringIntoArray( ( string ) $_sTags, ',' );
                }
                
                /**
                 * Retrieves Unit (post) IDs from specified taxonomy terms.
                 * @return      array
                 */
                private function ___getUnitIDsByTag( $aTags, $aArguments ) {                    
                    return isset( $aArguments[ 'tag_field_type' ] ) && in_array( strtolower( $aArguments[ 'tag_field_type' ] ), array( 'id', 'slug' ) )
                        ? $this->___getPostIDsByTag( 
                            $aTags, 
                            $aArguments[ 'tag_field_type' ], 
                            trim( $aArguments[ 'operator' ] ) 
                        )
                        : $this->___getPostIDsByTagName( 
                            $aTags,
                            trim( $aArguments[ 'operator' ] ) 
                        );
                }
                    private function ___getPostIDsByTagName( array $aTermNames, $sOperator='AND' ) {
                        
                        $_aTermSlugs = array();
                        foreach( $aTermNames as $_sTermName ) {
                            $_aTerm        = get_term_by( 'name', $_sTermName, FetchTweets_Commons::TagSlug, ARRAY_A );
                            $_aTermSlugs[] = $_aTerm[ 'slug' ];
                        }
                        return $this->___getPostIDsByTag( $_aTermSlugs, 'slug', $sOperator );
                                
                    }
                    /**
                     * @return      array
                     */
                    private function ___getPostIDsByTag( $aTermSlugs, $sFieldType='slug', $sOperator='AND' ) {

                        if ( empty( $aTermSlugs ) ) {
                            return array();
                        }

                        $_oResults   = new WP_Query( 
                            array(
                                'post_type'      => FetchTweets_Commons::PostTypeSlug,    // fetch_tweets
                                'posts_per_page' => -1, // ALL posts
                                'fields'         => 'ids',
                                'tax_query'      => array(
                                    array(
                                        'taxonomy'  => FetchTweets_Commons::TagSlug,    // fetch_tweets_tag
                                        'field'     => $this->___getFieldKeySanitized( $sFieldType ),    // id or slug
                                        'terms'     => $aTermSlugs,    // the array of term slugs
                                        'operator'  => $this->___getOperatorSanitized( $sOperator ),    // 'IN', 'NOT IN', 'AND. If the item is only one, use AND.
                                    )
                                )
                            )
                        );
                        return $_oResults->posts;
                        
                    }
                        /**
                         * @return      string
                         */
                        private function ___getFieldKeySanitized( $sField ) {
                            switch( strtolower( trim( $sField ) ) ) {
                                case 'id':
                                    return 'id';
                                default:
                                case 'slug':
                                    return 'slug';
                            }        
                        }
                        /**
                         * @return      string
                         */
                        private function ___getOperatorSanitized( $sOperator ) {
                            switch( strtoupper( trim( $sOperator ) ) ) {
                                case 'NOT IN':
                                    return 'NOT IN';
                                case 'IN':
                                    return 'IN';
                                default:
                                case 'AND':
                                    return 'AND';
                            }
                        }
        
    
    /**
     * Returns the output of tweets by the given arguments.
     * 
     * @remark      called from the shortcode callback.
     */
    public function get() {   

        $_aTweets   = $this->getTweets();
        $_sError    = $this->___getErrorMessage( $_aTweets, $this->_aArguments );
        if ( $_sError ) {
            return $_sError;
        }
    
        // Output the tweets by applying the template 
        $_oTemplate = new FetchTweets_Output_Tweet___Template( $_aTweets, $this->_aArguments );
        return $_oTemplate->get();  
        
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
                    . __( 'Code', 'fetch-tweets' ) . ':' . $_aError[ 'code' ];
            }
            
            $_sError = $this->getElement( $_aTweets, 'error', '' );
            if ( $_sError && is_string( $_aTweets[ 'error' ] ) ) {
                return '<strong>' . FetchTweets_Commons::NAME . '</strong>: ' 
                    . $_aTweets[ 'error' ];    
            }       
            
            return '';
            
        }
  
    /**
     * Fetches tweets based on the argument.
     * 
     * @remark      The scope is public as the feed extension uses it.
     * @since       2.5.0
     * @return      array
     */
    public function getTweets() {    
        
        $_aTweets          = array();
        $_aArgumentsByType = $this->___getArgumentsByRequestTypes( $this->_aArguments );
        foreach( $_aArgumentsByType as $_sRequestType => $_aArguments ) {            
            $_sClassName   = 'FetchTweets_TwitterAPI_' . $_sRequestType;
            $_oRequest     = new $_sClassName( $_aArguments );
            $_aTweets      = array_merge( $_oRequest->get(), $_aTweets );
        }
        $_oFormatter   = new FetchTweets_Output_Tweet___Format( 
            $_aTweets, 
            $this->_aArguments
        );
        return $_oFormatter->get();
        
    }
    
        /**
         * Determines the request types.
         * 
         * Usually, only one type is returned per argument but if multiple unit IDs are passed, the request types will be multiple.
         * 
         * @return      array
         */
        private function ___getArgumentsByRequestTypes( array $aArguments ) {
            
            $_sType = $this->___getRequestTypeFromArguments( $aArguments );
            if ( '' !== $_sType ) {
                return array( $_sType => $aArguments, );
            }
            return $this->___getArgumentsFromUnitIDs( $aArguments[ 'id' ], $aArguments );
                        
        }
            /**
             * @return      string
             */
            private function ___getRequestTypeFromArguments( $aArguments ) {
                            
                // Custom query URIs.
                if ( isset( $aArguments[ 'custom_query' ] ) ) {
                    return 'custom_query';
                }
                
                // Search keywords.
                if ( isset( $aArguments[ 'q' ] ) ) {   
                    return 'search';
                }
                
                // Screen names.
                if ( isset( $aArguments[ 'screen_name' ] ) ) {
                    return 'screen_name';
                }
                
                // Lists
                if ( isset( $aArguments[ 'list_id' ] ) ) {
                    return 'list';
                }
                
                // Tweet ID
                if ( isset( $aArguments[ 'tweet_id' ] ) ) {
                    return 'tweet_id';
                }            
                
                // Time line by registered account. Be careful that private lists also use `account_id`.
                if ( isset( $aArguments[ 'account_id' ] ) ) {
                    return 'home_timeline';
                }
                
                // Otherwise, unknown.
                return '';
                
            } 
            /**
             * @remark      Each unit has its own request type (tweet type).
             * @return      array       arguments by request type
             */
            private function ___getArgumentsFromUnitIDs( array $aUnitIDs, $aDiretArguments ) {
                
                $_aArgumentsByType = array();
                foreach( $aUnitIDs as $_iPostID ) {
                    
                    $_aArguments = array();
                    $_aArguments[ 'tweet_type' ]  = get_post_meta( $_iPostID, 'tweet_type', true );
                    $_aArguments[ 'count' ]       = get_post_meta( $_iPostID, 'item_count', true );
                    $_aArguments[ 'include_rts' ] = get_post_meta( $_iPostID, 'include_rts', true );
                    $_aArguments[ 'cache' ]       = get_post_meta( $_iPostID, 'cache', true );
                    $_aArguments = $this->uniteArrays( 
                        $aDiretArguments,
                        $_aArguments + $this->___getArguments_{$_aArguments[ 'tweet_type' ]}( $_iPostID )
                    );
                    $_aArgumentsByType[ $_aArguments[ 'tweet_type' ] ] = $_aArguments;
                    
                }
                return $_aArgumentsByType;
                
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
