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
class FetchTweets_Output extends FetchTweets_Output_Base {
    
    /**
     * Prints tweets based on the given arguments.
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
     */    
    public function render( $aArguments ) {
        echo $this->get( $aArguments );        
    }    
    
    /**
     * Returns the output of tweets by the given arguments.
     * 
     * @remark      called from the shortcode callback.
     */
    public function get( $aArguments ) {   

        $_aTweets   = $this->getTweets( 
            $aArguments // Passed by reference. Gets formatted and updated in the method.
        );
        $_sError    = $this->___getErrorMessage( $_aTweets, $aArguments );
        if ( $_sError ) {
            return $_sError;
        }
    
        // Output the tweets by applying the template 
        return $this->getOutputBuffer( 
            array( $this, 'applyTemplates' ),
            array( $_aTweets, $aArguments )
        );
        // $this->applyTemplate( $_aTweets, $aArguments );    
        
    }
        /**
         * Generates error message from the tweets array.
         * 
         * @since       2.4.7
         * @since       2.4.8       Changed the scope to public to let some extension plugins access this method.
         * @return      string      the error message. An empty string on no error.
         */
        private function ___getErrorMessage( array $_aTweets, array $aArguments ) {
                    
            if ( empty( $_aTweets ) ) {
                return isset( $aArguments[ 'show_error_on_no_result' ] ) && $aArguments[ 'show_error_on_no_result' ]
                    ? __( 'No result could be fetched.', 'fetch-tweets' )
                    : '';
            }
            
            if ( isset( $_aTweets['errors'][ 0 ]['message'], $_aTweets['errors'][ 0 ]['code'] ) ) {
                return '<strong>Fetch Tweets</strong>: ' . $_aTweets['errors'][ 0 ]['message'] . ' ' . __( 'Code', 'fetch-tweets' ) . ':' . $_aTweets['errors'][ 0 ]['code'];    
            }
            if ( isset( $_aTweets['error'] ) && $_aTweets['error'] && is_string( $_aTweets['error'] ) ) {
                return '<strong>Fetch Tweets</strong>: ' . $_aTweets['error'];    
            }        
            return '';
            
        }


     
    /**
     * Fetches tweets based on the argument.
     * 
     * @remark      The scope is public as the feed extension uses it.
     * @since       2.5.0
     * @param       array       $aArguments      The argument array.
     * @return      array
     */
    public function getTweets( $aArguments ) {    
        
        $aArguments = $this->___getArgumentsFormatted( $aArguments );
        
        $_aClassNames = array(
        
        );
        switch( $this->___getRequestType( $aArguments ) ) {
            case 'search':
                $_aTweets = $this->getTweetsBySearch(
                    $aArguments['q'],
                    $aArguments['count'],
                    $aArguments['lang'],
                    $aArguments['result_type'],
                    $aArguments['until'],
                    $aArguments['geocode'],
                    $aArguments['cache']
                );
                break;
            
            case 'screen_name':
                $_aTweets = $this->getTweetsByScreenNames(
                    $aArguments['screen_name'],
                    $aArguments['count'],
                    $aArguments['include_rts'],
                    $aArguments['exclude_replies'],
                    $aArguments['cache'] 
                );
                break;
            
            case 'list':
                $_aTweets = $this->_getTweetsByListID(
                    $aArguments['list_id'],
                    $aArguments['include_rts'],
                    $aArguments['cache']
                );
                break;                
                
            case 'timeline':
                $_aTweets = $this->_getTweetsByHomeTimeline(
                    $aArguments['account_id'],
                    $aArguments['exclude_replies'],
                    $aArguments['include_rts']
                );
                break;                                

            case 'tweet_id':
                $_aTweets = $this->_getResponseByTweetID( 
                    $aArguments['tweet_id'], 
                    $aArguments['cache']
                );
                break;                                
                
            // normal
            default:
                $_aTweets = $this->_getTweetsAsArrayByPostIDs( 
                    $aArguments['id'], 
                    $aArguments, 
                    $_aRawArgs 
                );
                break;
                
        }
                        
        // Format the array and return it.
        $this->_formatTweetArrays( 
            $_aTweets,      // passed by reference
            $aArguments 
        ); 
        return $_aTweets;
        
    }

        /**
         * Formats the argument array.
         * @return      array
         * @since       2.5.0
         */
        private function ___getArgumentsFormatted( $aArguments ) {
            
            $_oOption    = FetchTweets_Option::getInstance();
            $_aArguments = $this->getAsArray( $aArguments );
           
            $_aArguments = $this->uniteArrays( 
                $_aArguments,         // passed arguments for the API request
                $_oOption->aOptions['default_values']  // user saved options
                + $_oOption->aStructure_DefaultParams  // class0defined option structure
                + $_oOption->aStructure_DefaultTemplateOptions // class-defined template option structure
            );
            $_aArguments[ 'id' ]    = $this->getElement( 
                $_aArguments, 
                array( 'ids' ), 
                $this->getElement( $_aArguments, array( 'id' ), '' )    // backward compatibility
            );
            $_aArguments[ 'id' ]    = is_array( $_aArguments[ 'id' ] ) 
                ? $_aArguments[ 'id' ] 
                : preg_split( 
                    "/[,]\s*/", 
                    trim( ( string ) $_aArguments[ 'id' ] ), 
                    0, 
                    PREG_SPLIT_NO_EMPTY 
                );
            return $_aArguments;        
            
        }
    
        /**
         * Determines the request type.
         */
        private function ___getRequestType( array $aArguments ) {
            
            // custom call by search keyword
            if ( isset( $aArguments['q'] ) ) {   
                return 'search';
            }
            
            // custom call by screen name
            if ( isset( $aArguments['screen_name'] ) ) {
                return 'screen_name';
            }
            
            // only public list can be fetched with this method
            if ( isset( $aArguments['list_id'] ) ) {
                return 'list';
            }
            
            // Time line by registered account.
            if ( isset( $aArguments['account_id'] ) ) {
                return 'timeline';
            }
            
            // Tweet ID
            if ( isset( $aArguments['tweet_id'] ) ) {
                return 'tweet_id';
            }
            
        }
    
        /**
         * 
         * @param            array|integer            $vPostIDs            The target post ID of the Fetch Tweet rule post type.
         * @param            array                    $aArguments                The argument array. It is passed by reference to let assign post meta options.
         */
        protected function _getTweetsAsArrayByPostIDs( $vPostIDs, & $aArguments, $aRawArgs ) {    
        
            $_aTweets = array();
            foreach( ( array ) $vPostIDs as $_iPostID ) {
                
                $aArguments['tweet_type']    = get_post_meta( $_iPostID, 'tweet_type', true );
                $aArguments['count']         = get_post_meta( $_iPostID, 'item_count', true );
                $aArguments['include_rts']   = get_post_meta( $_iPostID, 'include_rts', true );
                $aArguments['cache']         = get_post_meta( $_iPostID, 'cache', true );
                
                $_aRetrievedTweets      = array();
                switch ( $aArguments['tweet_type'] ) {
                    case 'search':
                        $aArguments['q']                     = get_post_meta( $_iPostID, 'search_keyword', true );    
                        $aArguments['result_type']           = get_post_meta( $_iPostID, 'result_type', true );
                        $aArguments['lang']                  = get_post_meta( $_iPostID, 'language', true );
                        $aArguments['until']                 = get_post_meta( $_iPostID, 'until', true );
                        $aArguments['geocentric_coordinate'] = get_post_meta( $_iPostID, 'geocentric_coordinate', true );
                        $aArguments['geocentric_radius']     = get_post_meta( $_iPostID, 'geocentric_radius', true );
                        $_sGeoCode                      = '';
                        if ( 
                            is_array( $aArguments['geocentric_coordinate'] ) && is_array( $aArguments['geocentric_radius'] )
                            && isset( $aArguments['geocentric_coordinate']['latitude'], $aArguments['geocentric_radius']['size'] ) 
                            && $aArguments['geocentric_coordinate']['latitude'] !== '' && $aArguments['geocentric_coordinate']['longitude'] !== ''    // the coordinate can be 0
                            && $aArguments['geocentric_radius']['size'] !== '' 
                        ) {
                            // "latitude,longitude,radius",
                            $_sGeoCode              = trim( $aArguments['geocentric_coordinate']['latitude'] ) . "," . trim( $aArguments['geocentric_coordinate']['longitude'] ) 
                                . "," . trim( $aArguments['geocentric_radius']['size'] ) . $aArguments['geocentric_radius']['unit'] ;
                        }                        
                        $aArguments                      = FetchTweets_Utilities::uniteArrays( $aRawArgs, $aArguments ); // The direct input takes its precedence.
                        $_aRetrievedTweets          = $this->getTweetsBySearch( $aArguments['q'], $aArguments['count'], $aArguments['lang'], $aArguments['result_type'], $aArguments['until'], $_sGeoCode, $aArguments['cache'] );
                        break;
                    case 'list':
                        $aArguments['account_id']        = get_post_meta( $_iPostID, 'account_id', true );
                        $aArguments['mode']              = get_post_meta( $_iPostID, 'mode', true );
                        $aArguments['list_id']           = get_post_meta( $_iPostID, 'list_id', true );
                        $aArguments                      = FetchTweets_Utilities::uniteArrays( $aRawArgs, $aArguments ); // The direct input takes its precedence.
                        $_aRetrievedTweets          = $this->_getTweetsByListID( $aArguments['list_id'], $aArguments['include_rts'], $aArguments['cache'], $aArguments['account_id'], $aArguments['mode'] );
                        break;
                    case 'home_timeline':
                        $aArguments['account_id']        = get_post_meta( $_iPostID, 'account_id', true );
                        $aArguments['exclude_replies']   = get_post_meta( $_iPostID, 'exclude_replies', true );
                        $aArguments                      = FetchTweets_Utilities::uniteArrays( $aRawArgs, $aArguments ); // The direct input takes its precedence.
                        $_aRetrievedTweets          = $this->_getTweetsByHomeTimeline( $aArguments['account_id'], $aArguments['exclude_replies'], $aArguments['include_rts'], $aArguments['cache'] );
                        break;
                    case 'feed':
                        $aArguments['json_url']          = get_post_meta( $_iPostID, 'json_url', true );
                        $aArguments                      = FetchTweets_Utilities::uniteArrays( $aRawArgs, $aArguments ); // The direct input takes its precedence.
                        $_aRetrievedTweets          = $this->_getTweetsByJSONFeed( $aArguments['json_url'], $aArguments['cache'] );
                        break;
                    case 'custom_query':
                        $aArguments['custom_query']      = get_post_meta( $_iPostID, 'custom_query', true );
                        $aArguments['response_key']      = get_post_meta( $_iPostID, 'response_key', true );
                        $aArguments                      = FetchTweets_Utilities::uniteArrays( $aRawArgs, $aArguments ); // The direct input takes its precedence.
                        $_aRetrievedTweets          = $this->_getResponseWithCustomRequest( $aArguments['custom_query'], $aArguments['response_key'], $aArguments['cache'] );
                        break;
                    case 'tweet_id':
                        $aArguments['tweet_id']          = get_post_meta( $_iPostID, 'tweet_id', true );
                        $aArguments                      = FetchTweets_Utilities::uniteArrays( $aRawArgs, $aArguments ); // The direct input takes its precedence.
                        $_aRetrievedTweets          = $this->_getResponseByTweetID( $aArguments['tweet_id'], $aArguments['cache'] );
                        break;
                    case 'screen_name':
                    default:    
                        $aArguments['screen_name']       = get_post_meta( $_iPostID, 'screen_name', true );
                        $aArguments['exclude_replies']   = get_post_meta( $_iPostID, 'exclude_replies', true );
                        $aArguments                      = FetchTweets_Utilities::uniteArrays( $aRawArgs, $aArguments ); // The direct input takes its precedence.
                        $_aRetrievedTweets          = $this->getTweetsByScreenNames( $aArguments['screen_name'], $aArguments['count'], $aArguments['include_rts'], $aArguments['exclude_replies'], $aArguments['cache'] );
                        break;                
                }    

                $_aTweets = array_merge( $_aRetrievedTweets, $_aTweets );
                    
            }

            return $_aTweets;
            
        }
    
}
