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
 * Provides methods to retrieve information of a particular Fetch Tweets template.
 *  
 * @since            2.3.9
 */
class FetchTweets_Template {
    
    /**
     * Sets up properties.
     * @since       2.3.9
     * @param       string|array      The template slug. If it is an empty, the default template will be set.
     * Or if it is array, it is considers as the template definition array. Use it for inactive templates.
     */
    public function __construct( $asTemplateSlugOrData='' ) {
        
        $this->oOption      = FetchTweets_Option::getInstance();
        
        if ( is_array( $asTemplateSlugOrData ) ) {
            $this->aData = $asTemplateSlugOrData;
            return;
        }
        
        // At this point a string slug is given.
        $_sTemplateSlug = $asTemplateSlugOrData;
        if ( ! $_sTemplateSlug ) {
            $this->aData = $this->_getDefault();          
            return;
        }
        $this->aData = $this->_getTemplateBySlug( $_sTemplateSlug );
     
    }
        
        /**
         * Returns the default template array.
         * @since       2.3.9
         */
        private function _getDefault() {
            return empty( $this->oOption->aOptions['arrDefaultTemplate'] )
                ? $this->oOption->findDefaultTemplateDetails()
                : $this->_getAsArray( $this->oOption->aOptions['arrDefaultTemplate'] );
        }
        /**
         * Returns the template array by slug.
         * @since       2.3.9
         */
        private function _getTemplateBySlug( $sSlug ) {

            // First find from the active templates + default template. It is fast because the they are stores in the options.
            $_aActiveTemplates = $this->_getAsArray( $this->oOption->aOptions[ 'arrTemplates' ] );
             
            $_aTemplate = $this->_findTemplateBySlug( $sSlug, $_aActiveTemplates );
            if ( $_aTemplate ) {
                return $_aTemplate;
            }
            
            // Now try find from uploaded templates.
            $_aUploadedTemplates = $this->oOption->getUploadedTemplates();
            $_aTemplate = $this->_findTemplateBySlug( $sSlug, $_aUploadedTemplates );
            if ( $_aTemplate ) {
                return $_aTemplate;
            }            
      
            // At this point, the template is not found. Then use the default template.
            return $this->_getDefault();
            
        }
            /**
             * Attempts to find a template of the given slug from the given tempaltes array.
             * 
             * @since       2.3.9
             */
            private function _findTemplateBySlug( $sSlug, $aTemplates ) {
                
                if ( isset( $aTemplates[ $sSlug ] ) ) {
                    return $aTemplates[ $sSlug ];
                }
                                
                // It could be that the slug is of an old format.
                foreach( $aTemplates as $_aTemplate ) {
                    if ( $sSlug === $_aTemplate['sOldSlug'] ) {
                        return $_aTemplate;
                    }
                }                
                return false;
                
            }
    /**
     * Returns the slug of the template.
     * 
     * To be accurate, use this method over `$this->get( 'sSlug' )` as it handles backwarad compatibility.
     * 
     * @since       2.3.9
     */
    public function getSlug() {
        if ( isset( $this->aData['sSlug'] ) ) {
            return $this->aData['sSlug'];
        }
        if ( isset( $this->aData['sRelativeDirPath'] ) ) {
            return $this->aData['sRelativeDirPath'];
        }
        // backward compatibility
        return isset( $this->aData['strSlug'] )
            ? $this->aData['strSlug']
            : '';
    }
    
    /**
     * Returns the absolute path of the template directory path.
     * 
     * Consider a possibility that the user moves the site. So it needs to calculates the path from the relative path.
     * Do not use the $aData['sDirPath'] value because it stores the full path of the directory when it is registered.
     * 
     * @since       2.3.9
     */
    public function getDirPath() {
        $_sRelativeDirPath = $this->getRelativeDirPath();
        return FetchTweets_WPUtility::getAbsolutePathFromRelative( untrailingslashit( $_sRelativeDirPath ) );
    }
    
    /**
     * Returns the relative path to ABSPATH.
     * @since       2.3.9
     */    
    public function getRelativeDirPath() {
        
        if ( isset( $this->aData['sRelativeDirPath'] ) ) {
            return $this->aData['sRelativeDirPath'];
        }        
        if ( isset( $this->aData['sDirPath'] ) ) {
            $_sRelativeDirPath = str_replace( '\\', '/', untrailingslashit( FetchTweets_Utilities::getRelativePath( ABSPATH, $this->aData['sDirPath'] ) ) );
            $this->aData['sRelativeDirPath'] = $_sRelativeDirPath;  // update it
            return $_sRelativeDirPath;
        }
        return '';
        
    }
    
    /**
     * Returns the template file as an url.
     * 
     * Use it for style.css files.
     * 
     * @since       2.3.9
     */
    public function getURLByFIleName( $sFileName='style.css' ) {
        $_sFilePath = $this->getPathByFileName( $sFileName );
        return FetchTweets_WPUtility::getSRCFromPath( $_sFilePath );
    }
    /**
     * Returns the template related file absolute path by the given file name.
     * 
     * @since       2.3.9
     * @return      string          The path. If not found, an empty string.
     */
    public function getPathByFileName( $sFileName='template.php' ) {
        
        if ( ! $_sRelativeDirPath = $this->getRelativeDirPath() ) {
            return '';
        }

        $_sPath = $this->getDirPath() . DIRECTORY_SEPARATOR . $sFileName;
        if ( file_exists( $_sPath ) ) {
            return $_sPath;
        }
        // Try the set full dir path.
        $_sDirPath = isset( $this->aData['sDirPath'] )
            ? $this->aData['sDirPath']
            : '';
        if ( ! $_sDirPath )  {
            return '';
        }
        $_sPath = untrailingslashit( $_sDirPath ) . '/' . $sFileName;
        if ( file_exists( $_sPath ) ) {
            return $_sPath;
        }
        return '';
        
    }

    /**
     * Returns the thumbnail url.
     * @since       2.3.9
     */
    public function getThumbnailURL()  {
        return FetchTweets_WPUtility::getSRCFromPath( $this->getThumbnailPath() );
    }
    /**
     * Returns the thumbnail path.
     * @since       2.3.9
     */    
    public function getThumbnailPath() {
        return $this->_getScreenshotPath( $this->getDirPath() );
    }
        /**
         * Returns the file path of the screen shot.
         */
        private function _getScreenshotPath( $sDirPath ) {
            foreach( array( 'jpg', 'jpeg', 'png', 'gif' ) as $_sExt ) {
                if ( @is_file( $sDirPath . DIRECTORY_SEPARATOR . 'screenshot.' . $_sExt ) ) {
                    return $sDirPath . DIRECTORY_SEPARATOR . 'screenshot.' . $_sExt;
                }
            }
            return '';
        }    
    
    /*
     * The below require to perform get_file_data().
     */    
    
    /**
     * Returns the template info by the given key.
     * 
     * The retrievable value must be written in the style.css header comment section. 
     * 
     * @since       2.3.9
     */
    public function get( $sKey ) {

        if ( isset( $this->aData[ $sKey ] ) ) {
            return $this->aData[ $sKey ];
        }
        $_sPath = $this->getDirPath() . DIRECTORY_SEPARATOR . 'style.css';
        if ( ! file_exists( $_sPath ) ) {
            $_aData = array();
        } else {            
            $_aData = $this->_getTemplateData( $_sPath );
        }
        $this->aData = $_aData + $this->aData;
        return isset( $this->aData[ $sKey ] )
            ? $this->aData[ $sKey ]
            : null;
        
    }    
        
        /**
         * Caches the template data by path.
         * @since       2.3.8
         */
        static private $_aTemplateData = array();
        
        /**
         * Extracts information from the specified style.css file.
         * 
         * An alternative to get_plugin_data() as some users change the location of the wp-admin directory.
         * 
         * @since           unknown     
         * @since           2.3.9       Changed the keys for the new format. 
         * @return          array       Returns an array of template detail information from the given file path.    
         */
        private function _getTemplateData( $sFilePath, $sType='fetch_tweets' )    {
        
            if ( isset( self::$_aTemplateData[ $sFilePath ] ) ) {
                return self::$_aTemplateData[ $sFilePath ];
            }
        
            $_aData = get_file_data( 
                $sFilePath, 
                array(
                    'sName'            => 'Template Name',
                    'sTemplateURI'     => 'Template URI',
                    'sVersion'         => 'Version',
                    'sDescription'     => 'Description',
                    'sAuthor'          => 'Author',
                    'sAuthorURI'       => 'Author URI',
                ),
                $sType    // context
            );      
            // backward compatibility
            $_aData = array(
                'strName'            => $_aData['sName'],
                'strTemplateURI'     => $_aData['sTemplateURI'],
                'strVersion'         => $_aData['sVersion'],
                'strDescription'     => $_aData['sDescription'],
                'strAuthor'          => $_aData['sAuthor'],
                'strAuthorURI'       => $_aData['sAuthorURI'],
            ) + $_aData;
            
            // Cache it.
            self::$_aTemplateData[ $sFilePath ] = $_aData;
            return $_aData;
            
        }        

    /*
     * Utility methods
     */
    /**
     * Returns the value as an array.
     * 
     * @since       2.4.2
     */
    private function _getAsArray( $vValue ) {
        
        if ( is_array( $vValue ) ) {
            return $vValue;
        }
        if ( $vValue ) {
            return ( array ) $vValue;
        }
        return array();
        
    }    
}