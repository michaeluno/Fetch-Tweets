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
 * Provides methods for output templates
 * 
 * @since            2
 * @filter           fetch_tweets_template_path
 */
abstract class FetchTweets_Fetch_Template extends FetchTweets_Fetch_Format {
    
    /**
     * Outputs the tweets by applying the template.
     * 
     * This method is also called from filter callbacks (which requires a return value)
     * 
     * @since       2.4.8
     * @remark      Echoes an output.
     * @remark      The scope must be public as some extension plugins access this method.
     * @return      void
     */
    public function applyTemplate( array $aTweets, array $aArgs ) {

        if ( empty( $aTweets ) && ! $aArgs[ 'apply_template_on_no_result' ] ) {
            return;
        }    
        
        $this->_includeTemplate(
            $aTweets, 
            $aArgs, 
            $this->oOption->aOptions 
        );
        
    }      
    
    /**
     * Includes the template.
     * 
     * @since       2
     * @remark      the local variables defined here will be accessed from the template file.
     * @param       array       $aTweets        the fetched tweet arrays.
     * @param       array       $aArgs          the passed arguments such as item count etc.
     * @param       array       $aOptions       the plugin options saved in the database.
     */
    protected function _includeTemplate( $aTweets, $aArgs, $aOptions ) {

        // For backward compatibility for v1 - these variables will be accessible from the included template file.
        $arrTweets  = & $aTweets;
        $arrArgs    = & $aArgs;
        $arrOptions = & $aOptions;
        
        // Retrieve the template slug we are going to use.
        $_aPostIDs          = array_values( ( array ) $aArgs['id'] );
        $_iPostID           = array_shift( $_aPostIDs );
        $aArgs['template']  = $this->_getTemplateSlug( $_iPostID, $aArgs['template'] );

        // Call the template. ( template.php )
        $_sTemplatePath = apply_filters( "fetch_tweets_template_path", $this->_getTemplatePathBySlug( $aArgs['template'] ), $aArgs );
        if ( ! $_sTemplatePath ) {
            echo "<p class='error'>Fetch Tweets: " . __( 'The template path could not be found. Please select the template in the rule definition page.', 'fetch-tweets' ) . "</p>";
            return;            
        }
        include( $_sTemplatePath );        
        
    }
        
        /**
         * Retrieves the template slug by the given post ID(s) or the preceding template slug.
         * 
         * @param       integer     $iPostID            The post ID of the rule.
         * @param       string      $sTemplateSlug      The template slug. If this is set, this slug will be used. This is for the shortcode or PHP code that directly displays the output.
         */
        protected function _getTemplateSlug( $iPostID, $sTemplateSlug='' ) {

            // If the template slug is explicitly set, use that.
            if ( $sTemplateSlug && isset( $this->oOption->aOptions['arrTemplates'][ $sTemplateSlug ] ) ) {
                return $this->_validateTemplateSlug( $sTemplateSlug );
            }
            
            // At this point, the user does not set the template slug.
            // We are going to return the one defined in the custom post rule.
            if ( $iPostID ) {
                $sTemplateSlug = get_post_meta( $iPostID, 'fetch_tweets_template', true );
            }

            return $this->_validateTemplateSlug( $sTemplateSlug, $iPostID );
            
        }
            /**
             * Checks if the necessary files are present. Otherwise, return the default template slug.
             * 
             * @since       unknown
             * @since       2.3.9       Changed the name from `_checkNecessaryFileExists()`.
             */
            protected function _validateTemplateSlug( $sTemplateSlug, $iPostID=0 ) {
                
                if ( empty( $sTemplateSlug ) ) {
                    return $this->_getDefaultTemplateSlug();
                }
                
                // this happens when the options have been reset
                if ( ! isset( $this->oOption->aOptions['arrTemplates'][ $sTemplateSlug ] ) ) {
                    foreach( $this->oOption->aOptions['arrTemplates'] as $_aTemplate ) {
                        if ( ! isset( $_aTemplate[ 'sOldSlug' ] ) ) {
                            continue;
                        }
                        if ( $_aTemplate[ 'sOldSlug' ] !== $sTemplateSlug ) {
                            continue;
                        }
                        // It means the passed slug matches the old type slug of md5 hash of the absolute directory path which has been deprecated as of v2.3.9.
                        // Update the meta value and store the correct slug.
                        update_post_meta( $iPostID, 'fetch_tweets_template', $_aTemplate['sSlug'] );
                        return $_aTemplate['sSlug'];
                    }
                    return $this->_getDefaultTemplateSlug();
                }
                
                return $sTemplateSlug;
                
            }
            /**
             * Returns the default template slug.
             * @since       2.3.9
             */
            private function _getDefaultTemplateSlug() {
                $_oTemplate = new FetchTweets_Template();
                return $_oTemplate->getSlug();
            }
            
        /**
         * Returns the path of the specified template.
         * 
         * @since       Unknown
         * @since       2.3.9       Changed it to use the relative path to WordPress installed directory.
         * @return      The template path; false if not exist.
         */
        protected function _getTemplatePathBySlug( $sTemplateSlug ) {
            $_oTemplate     = new FetchTweets_Template( $sTemplateSlug );    // passing none to the constructor creates default template object.
            return $_oTemplate->getPathByFileName( 'template.php' );
        }    

}
