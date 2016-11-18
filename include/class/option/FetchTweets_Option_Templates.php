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
 * Provides methods for template options.
 */
abstract class FetchTweets_Option_Templates extends FetchTweets_PluginUtility {
  
    /**
     * Caches the active templates.
     * 
     * @since       2.3.9
     */
    private static $_aActiveTemplates = array();
    
    /**
     * Returns an array that holds arrays of activated template information.
     * 
     * The active templates are stored in the plugin's option array. The array key that stores them is 'arrTemplates'.
     * @since       unknown
     * @since       2.3.9       moved from the templates class.
     * @scope       public      It is accessed from the template loader class.
     */
    public function getActiveTemplates() {
        
        if ( ! empty( self::$_aActiveTemplates ) ) {
            return self::$_aActiveTemplates;
        }
        
        // The default template (saved or dynamically generated)
        $_aDefaultTemplate = empty( $this->aOptions['arrDefaultTemplate'] )
            ? $this->findDefaultTemplateDetails()
            : $this->aOptions['arrDefaultTemplate'];
        $_aDefaultTemplate = $this->_formatTemplateArray( $_aDefaultTemplate );
        
        // The saved active templates.
        $_aActiveTemplates = isset( $this->aOptions['arrTemplates'] )
            ? $this->aOptions['arrTemplates']
            : array();

        // Add the default template to the activated template.
        $_aActiveTemplates[ $_aDefaultTemplate['sSlug'] ] = $_aDefaultTemplate;

        // Format the template array.
        foreach( $_aActiveTemplates as $_sDirSlug => &$_aActiveTemplate ) {        
                    
            $_aActiveTemplate = $this->_formatTemplateArray( $_aActiveTemplate );
            if ( ! $_aActiveTemplate ) {
                unset( $_aActiveTemplates[ $_sDirSlug ] );
            }
            $_aActiveTemplate['bIsActive'] = true;
            
        }
        
        self::$_aActiveTemplates = $_aActiveTemplates;
        return $_aActiveTemplates;
        
    }
 

    /**
     * Caches the uploaded templates.
     * 
     * @since       2.3.9
     */
    private static $_aUploadedTemplates = array();
 
    /**
     * Retrieve templates and returns the template information as array.
     * 
     * This method is called for the template listing table to list available templates. So this method generates the template information dynamically.
     * This method does not deal with saved options.
     * 
     */
    public function getUploadedTemplates() {
            
        if ( ! empty( self::$_aUploadedTemplates ) ) {
            return self::$_aUploadedTemplates;
        }
            
        // Construct a template array.
        $_aTemplates = array();
        $_iIndex     = 0;        
        foreach( $this->_getTemplateDirs() as $__sDirPath ) {
            
            $_aTemplate = $this->getTemplateArrayByDirPath( $__sDirPath );
            if ( ! $_aTemplate ) {
                continue;
            }
            $_aTemplate[ 'iIndex' ]   = ++$_iIndex;
            $_aTemplate[ 'intIndex' ] = $_aTemplate[ 'iIndex' ];    // backward compatibility                
            $_aTemplates[ $_aTemplate['sSlug'] ] = $_aTemplate;
            
        }
        
        self::$_aUploadedTemplates = $_aTemplates;
        return $_aTemplates;
        
    }
    
    
        /**
         * Stores the read template dir paths.
         * @since       2.3.9
         */
        static private $_aTemplateDirs = array();
        
        /**
         * Returns an array holding the template directories.
         * 
         * @since       2.3.5
         */
        private function _getTemplateDirs() {
                
            if ( ! empty( self::$_aTemplateDirs ) ) {
                return self::$_aTemplateDirs;
            }
            foreach( $this->_getTemplateContainerDirs() as $__sTemplateDirPath ) {
                    
                if ( ! @file_exists( $__sTemplateDirPath  ) ) { continue; }
                $__aFoundDirs = glob( $__sTemplateDirPath . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR );
                if ( is_array( $__aFoundDirs ) ) {    // glob can return false
                    self::$_aTemplateDirs = array_merge( $__aFoundDirs, self::$_aTemplateDirs );
                }
                                
            }
            self::$_aTemplateDirs = array_unique( self::$_aTemplateDirs );
            self::$_aTemplateDirs = ( array ) apply_filters( 'fetch_tweets_filter_template_directories', self::$_aTemplateDirs );
            self::$_aTemplateDirs = array_filter( self::$_aTemplateDirs );    // drops elements of empty values.
            self::$_aTemplateDirs = array_unique( self::$_aTemplateDirs );
            return self::$_aTemplateDirs;
        
        }    
            /**
             * Returns the template container directories.
             * @since       2.3.5
             */
            private function _getTemplateContainerDirs() {
                
                $_aTemplateContainerDirs    = array();
                $_aTemplateContainerDirs[]  = FetchTweets_Commons::getPluginDirPath() . DIRECTORY_SEPARATOR . 'template';
                $_aTemplateContainerDirs[]  = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'fetch-tweets';
                $_aTemplateContainerDirs    = apply_filters( 'fetch_tweets_filter_template_container_directories', $_aTemplateContainerDirs );
                $_aTemplateContainerDirs    = array_filter( $_aTemplateContainerDirs );    // drops elements of empty values.
                return array_unique( $_aTemplateContainerDirs );
                
            }       
    
 
}