<?php
/* 
	Plugin Name:    Fetch Tweets
	Plugin URI:     http://en.michaeluno.jp/fetch-tweets
	Description:    Fetches and displays tweets from twitter.com with the the Twitter REST API v1.1.
	Author:         miunosoft (Michael Uno)
	Author URI:     http://michaeluno.jp
	Version:        2.4.2b02
	Requirements:   PHP 5.2.4 or above, WordPress 3.3 or above.
*/

/**
 * Provides the basic information about the plugin.
 * 
 * @since       2.3.5
 */
class FetchTweets_Commons_Base {
    
	const Version        = '2.4.2b02';    // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
	const Name           = 'Fetch Tweets';
	const Description    = 'Fetches and displays tweets from twitter.com with the the Twitter REST API v1.1.';
	const URI            = 'http://en.michaeluno.jp/fetch-tweets';
	const Author         = 'miunosoft (Michael Uno)';
	const AuthorURI      = 'http://en.michaeluno.jp/';
	const Copyright      = 'Copyright (c) 2013-2014, Michael Uno';
	const License        = 'GPL v2 or later';
	const Contributors   = '';
    
    /**
     * Returns the information of this class.
     * 
     * @since       2.4.2
     */
    static public function getInfo() {
        $_oReflection = new ReflectionClass( __CLASS__ );
        return $_oReflection->getConstants()
            + $_oReflection->getStaticProperties()
        ;
    }        
    
}

final class FetchTweets_Commons extends FetchTweets_Commons_Base {
	
	static public $sFilePath            = '';       // 2.3.5+
	static public $sDirPath             = '';       // 2.3.5+
	public static $sPluginKey           = 'fetch_tweets';          // unknown what this is used for.
	public static $sAdminKey            = 'fetch_tweets_admin';    // also the below 'AdminOptionKey' constant is being used.
	public static $sOptionKey           = 'fetch_tweets_option';   // not used at the moment.
	
	// The below properties will be assigned automatically
    public static $sPluginPath          = '';
	public static $sPluginDirPath       = '';   // will be deprecated
	public static $sPluginName          = '';   // will be deprecated
	public static $sPluginURI           = '';   // deprecated
	public static $sPluginVersion       = '';   // will be deprecated
	public static $sPluginDescription   = '';   // will be deprecated
	public static $sPluginAuthor        = '';   // will be deprecated
	public static $sPluginAuthorURI     = '';   // will be deprecated
	public static $sPluginTextDomain    = '';   // will be deprecated
	public static $sPluginDomainPath    = '';   // will be deprecated
	public static $sPluginNetwork       = '';   // deprecated
	public static $sPluginSiteWide      = '';   // deprecated
	public static $sPluginStoreURI      = '';   // will be deprecated
	
	const TextDomain                    = 'fetch-tweets';
    const TextDomainPath                = '/languange';
	const PluginName                    = 'Fetch Tweets';
	const PostTypeSlug                  = 'fetch_tweets';
	const PostTypeSlugAccounts          = 'fetchtweets_accounts';		// post type slugs cannot exceed 20 characters. 
	const TagSlug                       = 'fetch_tweets_tag';
	const AdminOptionKey                = 'fetch_tweets_admin';
	const PageSettingsSlug              = 'fetch_tweets_settings';
    const PageSlug_Templates            = 'fetch_tweets_templates';
	const TransientPrefix               = 'FTWS';
	const ConsumerKey                   = '97LqHiMs06VhV2rf5tUQw';
	const ConsumerSecret                = 'FIH9cr0eXtd7q9caYVqBjd5mvfUS6hZqREYsUhh9wA';
    const PrimaryTaxonomySlug           = 'fetch_tweets_tag';
	
	static public function setUp( $sPluginFilePath ) {
		
        self::$sFilePath            = $sPluginFilePath;             // 2.3.5+
        self::$sDirPath             = dirname( $sPluginFilePath );  // 2.3.5+
        
		// These static properties are for backward compatibility.
        self::$sPluginPath          = $sPluginFilePath;             // backward compat
        self::$sPluginDirPath       = self::$sDirPath;              // backward compat
        self::$sPluginName          = self::Name;
        self::$sPluginVersion       = self::Version;
        self::$sPluginDescription   = self::Description;
        self::$sPluginAuthor        = self::Author;
        self::$sPluginAuthorURI     = self::AuthorURI;
        self::$sPluginStoreURI      = 'http://michaeluno.jp';
        self::$sPluginTextDomain    = self::TextDomain;
        self::$sPluginDomainPath    = self::TextDomainPath;
        
	}
	
	public static function getPluginKey() {
		return self::$sPluginKey;
	}
	public static function getAdminKey() {
		return self::$sAdminKey;
	}
	public static function getOptionKey() {
		return self::$sOptionKey;
	}	
	public static function getPluginFilePath() {
		return self::$sPluginPath;
	} 
	public static function getPluginDirPath() {
		return dirname( self::$sPluginPath );
	}
	public static function getPluginURL( $sRelativePath='' ) {
		return plugins_url( $sRelativePath, self::$sPluginPath );
	}    
	
}

// Do not load if accessed directly. Not exiting here because other scripts will load this main file such as uninstall.php and inclusion list generator 
// and if it exists their scripts will not complete.
if ( ! defined( 'ABSPATH' ) ) { return; }
if ( defined( 'DOWING_UNINSTALL' ) ) { return; }

include( dirname( __FILE__ ). '/include/class/boot/FetchTweets_Bootstrap.php' );
include( dirname( __FILE__ ). '/include/class/boot/registry/FetchTweets_RegisterClasses.php' );
include( dirname( __FILE__ ). '/include/function/functions.php' );
new FetchTweets_Bootstrap( __FILE__ );