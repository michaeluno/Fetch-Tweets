<?php
abstract class FetchTweets_Shortcode_ {
    
    /**
     * Registers the shortcode.
     */
    public function __construct( $sShortCode ) {
        add_shortcode( $sShortCode, array( $this, '_replyToGetOutput' ) );
    }
    
    /**
     * Returns the output by the given argument.
     */
    public function _replyToGetOutput( $aArgs ) {
        return fetchTweets( $aArgs, false );
    }    

}