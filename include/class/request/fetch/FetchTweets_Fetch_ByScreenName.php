<?php
/**
 * Provides methods to fetch tweets by screen name.
 * 
 * @package            Fetch Tweets
 * @subpackage        
 * @copyright        Michael Uno
 * @since            2
 */
abstract class FetchTweets_Fetch_ByScreenName extends FetchTweets_Fetch_ByList {
    
    /**
     * Fetches tweets by screen names.
     * 
     * The plural form of the _getTweetsByScreenName() method. Multiple screen names can be passed separated by commas.
     * 
     * @since            1.3.3
     */
    protected function getTweetsByScreenNames( $sUsers, $iCount, $bIncludeRetweets=false, $bExcludeReplies=false, $iCacheDuration=1200 ) {

        $_aTweets      = array();
        foreach( FetchTweets_Utilities::convertStringToArray( $sUsers, ',' ) as $_sScreenName ) {
            $_aTweets  = array_merge( 
                $this->_getTweetsByScreenName( 
                    $_sScreenName, 
                    $iCount, 
                    $bIncludeRetweets, 
                    $bExcludeReplies, 
                    $iCacheDuration 
                ), 
                $_aTweets 
            );
        }        
        return $_aTweets;
        
    }
    
        /**
         * Fetches tweets by screen name.
         * 
         * @see     https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
         * @since   1.0.0
         * @param       string      $sUser
         * @param       integer     $_deprecated        As of 1.3.4 the maximum number of tweets are fetched so that the data can be reused for different count requests.
         * @param       boolean     $bIncludeRetweets
         * @param       boolean     $bExcludeReplies
         * @param       integer     $iCacheDuration     Default: `1200`
         */ 
        protected function _getTweetsByScreenName( $sUser, $_deprecated, $bIncludeRetweets=false, $bExcludeReplies=false, $iCacheDuration=1200 ) {

            $_sRequestURI = add_query_arg(
                array(
                    'screen_name'       => $sUser,
                    'count'             => 200,     // set maximum
                    'include_rts'       => $bIncludeRetweets ? 1 : 0,
                    'exclude_replies'   => $bExcludeReplies ? 1 : 0,
                ),
                'https://api.twitter.com/1.1/statuses/user_timeline.json'
            );
            return $this->doAPIRequest_Get( 
                $_sRequestURI, 
                null, 
                $iCacheDuration, 
                array( 
                    'statuses', 
                    '/statuses/user_timeline' 
                )
            );
                    
        }
    
    
}