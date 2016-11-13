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
 * Handles plugin options.
 */
class FetchTweets_Option extends FetchTweets_Option_Format {
    
    /**
     * Stores the self-instance.
     * 
     * @since       2.3.5
     */
    static public $oInstance = null;    
    
   
    public $aOptions = array();    // stores the option values.
         
    protected $sOptionKey = '';    // stores the option key for this plugin. 
         
    /**
     * Returns the instance of the class.
     * 
     * This is to ensure only one instance exists.
     * 
     * @since       2.3.5
     */
    static public function getInstance() {
        
        if ( isset( $GLOBALS['oFetchTweets_Option'] ) && is_object( $GLOBALS['oFetchTweets_Option'] ) ) {
            return $GLOBALS['oFetchTweets_Option'];
        }
        
        $GLOBALS['oFetchTweets_Option'] = new FetchTweets_Option( FetchTweets_Commons::$sAdminKey );
        return $GLOBALS['oFetchTweets_Option'];
        
    }         
         
    public function __construct( $sOptionKey ) {
        
        $this->sOptionKey   = $sOptionKey;
        $this->aOptions     = $this->setOption( $sOptionKey );

    }    
    

    /*
     * Front end methods
     * */
       
    public function getAccessTokenAuto() {
        return $this->aOptions['twitter_connect']['access_token'];
    }    
    public function getAccessTokenSecretAuto() {
        return $this->aOptions['twitter_connect']['access_secret'];
    }         
        
    /**
     * Saves Twitter Account credentials.
     * 
     * @since            2
     */
    public function saveCredentials( array $aCredentials ) {

        $this->aOptions['twitter_connect'] = $aCredentials;
        do_action( 'fetch_tweets_action_updated_credentials', $aCredentials );        
        $this->saveOptions();    
        
    }
    
    /**
     * Returns the credentials array by the given account ID.
     * 
     * The account ID does not refer to the Twitter user ID. It is just a post ID stored in the WordPress database.
     * 
     * @since            2
     */
    public function getCredentialsByID( $iAccountID ) {

        if ( $iAccountID <= 0 ) {
            return $this->getCredentials();    // will returns the main one.
        }        
        return apply_filters( 
            'fetch_tweets_filter_credentials', 
            array(
                'consumer_key'      => '',
                'consumer_secret'   => '',
                'access_token'      => '',
                'access_secret'     => '',
                'screen_name'       => '',
            ), 
            $iAccountID 
        );    
        
    }
    
    /**
     * Returns the credentials array.
     * 
     * @since            2
     */
    public function getCredentials() {
        
        if ( $this->isAuthKeysManuallySet() ) {
            return $this->aOptions['authentication_keys'];
        }
        
        $_aCredentials = $this->aOptions['twitter_connect'] + self::$aStructure_Options['twitter_connect'];
        $_aCredentials['consumer_key']      = FetchTweets_Commons::ConsumerKey;
        $_aCredentials['consumer_secret']   = FetchTweets_Commons::ConsumerSecret;

        // Check mandatory keys have a value
        if ( $_aCredentials['access_token'] && $_aCredentials['access_secret'] && $_aCredentials['screen_name'] ) {
            return $_aCredentials;
        }
        
        // If the user has not connected to twitter, the access token and secret key is not set. In that case, return in the incomplete array.
        if ( ! ( $_aCredentials['access_token'] && $_aCredentials['access_secret'] ) ) {
            return $_aCredentials;
        }
        
        // Otherwise, fetch the status to fill the user id and the screen name elements.
        $_oConnect = new FetchTweets_TwitterAPI_Verification( 
            FetchTweets_Commons::ConsumerKey, 
            FetchTweets_Commons::ConsumerSecret, 
            $this->aOptions['twitter_connect']['access_token'], 
            $this->aOptions['twitter_connect']['access_secret']
        );
        $_aStatus = $_oConnect->getStatus();
        $_aCredentials['screen_name'] = $_aStatus['screen_name'];
        $_aCredentials['user_id'] = $_aStatus['id'];
        $_aCredentials['is_connected'] = true;
        $_aCredentials['connect_method'] = 'oauth';
        $this->saveCredentials( $_aCredentials );    // update the options
        return $_aCredentials;
        
    }
    
    /**
     * Checks if the plugin is connected to Twitter.
     * 
     * @since            2
     */
    public function isConnected() {
        
        // The keys are manually set
        if ( $this->isAuthKeysManuallySet() ) {
            if ( isset( $this->aOptions['authentication_keys']['is_connected'] ) ) {
                return $this->aOptions['authentication_keys']['is_connected'];
            }
            $_oConnect = new FetchTweets_TwitterAPI_Verification( 
                $this->aOptions['authentication_keys']['consumer_key'], 
                $this->aOptions['authentication_keys']['consumer_secret'],
                $this->aOptions['authentication_keys']['access_token'], 
                $this->aOptions['authentication_keys']['access_secret']
            );
            $_aStatus = $_oConnect->getStatus();                
            return isset( $_aStatus['screen_name'] );
        }
        
        // The keys are automatically retrieved
        if ( isset( $this->aOptions['twitter_connect']['is_connected'] ) ) {
            return $this->aOptions['twitter_connect']['is_connected'];
        }
        $_oConnect = new FetchTweets_TwitterAPI_Verification( 
            FetchTweets_Commons::ConsumerKey,
            FetchTweets_Commons::ConsumerSecret,
            $this->aOptions['twitter_connect']['access_token'], 
            $this->aOptions['twitter_connect']['access_secret']
        );
        $_aStatus = $_oConnect->getStatus();                
        return isset( $_aStatus['screen_name'] );        
        
    }
    
    /**
     * Returns whether the plugin has set the API authentication keys automatically.
     * 
     * since            1.3.0
     */
    public function isAuthKeysAutomaticallySet() {
        return $this->getAccessTokenAuto() && $this->getAccessTokenSecretAuto()
            ? true
            : false;
    }
    /**
     * Returns whether the user has set the API authentication keys manually.
     * 
     * As of v1.3.0, automatic authentication is supported. If the user already sets the keys by themselves already, no need to re-authorize. 
     * Also if the consumer key and consumer secret are provided by miunosoft, if they become invalid for some reasons, the user can set them by themselves.
     * 
     * since            1.3.0
     * return            boolean
     */
    public function isAuthKeysManuallySet() {
        return $this->getConsumerKey() && $this->getConsumerSecret() && $this->getAccessToken() && $this->getAccessTokenSecret()
            ? true 
            : false;
    }
    
    public function getConsumerKey() {
        return $this->aOptions['authentication_keys']['consumer_key'];
    }
    public function getConsumerSecret() {
        return $this->aOptions['authentication_keys']['consumer_secret'];
    }
    public function getAccessToken() {
        return $this->aOptions['authentication_keys']['access_token'];
    }    
    public function getAccessTokenSecret() {
        return $this->aOptions['authentication_keys']['access_secret'];
    }    
    
    public function saveOptions( $aOptions=null ) {
        update_option( 
            $this->sOptionKey, 
            $aOptions ? 
                $aOptions 
                : $this->aOptions 
        );
    }
    
     /**
     * Returns the specified option value.
     * 
     * @since       2.3.5
     */
    static public function get( $asKey=null, $vDefault=null ) {
                
        $_oOption = self::getInstance();
        
        // If the key is not set or false, return the entire option array.
        if ( ! $asKey ) {
            return empty( $_oOption->aOptions )
                ? $vDefault
                : $_oOption->aOptions;
        }

        // Now either the section ID or field ID is given. 
        return FetchTweets_AdminPageFramework_WPUtility::getArrayValueByArrayKeys( 
            $_oOption->aOptions, 
            array_values( FetchTweets_AdminPageFramework_WPUtility::getAsArray( $asKey ) ), 
            $vDefault 
        );
        
    }
    
    /**
     * Resets the cached options.
     * 
     * It will re-retrieve the options.
     */
    static public function refresh() {
        
        self::$oInstanc = null;
        return self::getInstance();
        
    }
    
    /**
     * Deletes the option.
     * 
     * @remark      Accessed from `uninstall.php`.
     */
    public function delete() {
        delete_option( $this->sOptionKey );
    }
    
}