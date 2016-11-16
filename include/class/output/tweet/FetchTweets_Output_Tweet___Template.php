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
 * Output tweets by applying the template.
 */
class FetchTweets_Output_Tweet___Template extends FetchTweets_PluginUtility {
    
    private $___aTweets = array();
    
    private $___aArguments = array();
    
    private $___oOption;
    
    /**
     * Sets up properties.
     */
    public function __construct( $aTweets, $aArguments ) {
        
        $this->___aTweets    = $aTweets;
        $this->___aArguments = $aArguments;
        $this->___oOption    = FetchTweets_Option::getInstance();
        
    }
    
    /**
     * @return      string
     */
    public function get() {
// return '<pre>' . print_r( $this->___aTweets, true ) . '</pre>';
        return $this->getOutputBuffer( 
            array( $this, 'replyToApplyTemplate' ), // callback
            array( $this->___aTweets, $this->___aArguments )    // parameters
        );        
    }
    
    /**
     * Renders outputs by applying the template to the fetched tweet array.
     * @return      void
     */
    public function replyToApplyTemplate( $aTweets, $aArguments ) {
        
        if ( empty( $aTweets ) && ! $aArguments[ 'apply_template_on_no_result' ] ) {
            return;
        }    
        
        $this->___includeTemplate(
            $aTweets, 
            $aArguments
        );
        
    }
        /**
         * Includes the template.
         * 
         * @since       2.5.0
         * @remark      the local variables defined here will be accessed from the template file.
         * @param       array       $aTweets        the fetched tweet arrays.
         * @param       array       $aArguments     the passed arguments such as item count etc.
         * @param       array       $aOptions       the plugin options saved in the database.
         */
        private function ___includeTemplate( $aTweets, $aArguments ) {
            
            // For backward compatibility for v1 - these variables will be accessible from the included template file.
            $arrTweets  = $aTweets;
            $arrArgs    = $aArgs    = $aArguments;
            $arrOptions = $aOptions = $this->___oOption->get();
            
            // Retrieve the template slug we are going to use.
            $aArguments[ 'template' ]  = $this->___getTemplateSlug( $aArguments );

            // Call the template. ( template.php )
            $_sTemplatePath = apply_filters( 
                "fetch_tweets_template_path", 
                $this->___getTemplatePathBySlug( $aArguments[ 'template' ] ), 
                $aArguments 
             );
            if ( ! file_exists( $_sTemplatePath ) ) {
                echo "<p class='error'>"
                        . FetchTweets_Commons::NAME . ": " 
                        . __( 'The template path could not be found. Please select the template in the rule definition page.', 'fetch-tweets' )
                    . "</p>";
                return;            
            }
            include( $_sTemplatePath );        
            
        }
            
            /**
             * Retrieves the template slug by the given post ID(s) or the preceding template slug.
             * 
             * @return      string
             */
            private function ___getTemplateSlug( $aArguments ) {
                
                /**
                 * The template slug. If this is set, this slug will be used. This is for the shortcode or PHP code that directly displays the output.
                 */
                $_sTemplateSlug = $aArguments[ 'template' ];
            
                // If the template slug is explicitly set, use that.
                if ( $_sTemplateSlug && $this->___oOption->get( array( 'arrTemplates', $_sTemplateSlug ) ) ) {
                    return $this->___getTemplateSlugValidated( $_sTemplateSlug );
                }
                
                // At this point, the user does not set the template slug.
                // We are going to return the one defined in the custom post rule.
                $_iPostID            = $this->___getRulePostID( $aArguments );
                if ( $_iPostID ) {
                    $_sTemplateSlug = get_post_meta( $_iPostID, 'fetch_tweets_template', true );
                }
                return $this->___getTemplateSlugValidated( $_sTemplateSlug, $_iPostID );
                
            }
                /**
                 * Returns the post ID of the rule.
                 * @return      integer
                 */
                private function ___getRulePostID( $aArguments ) {
                    
                    $_aPostIDs = array_values( $this->getAsArray( $aArguments[ 'id' ] ) );
                    return array_shift( $_aPostIDs );  
                    
                }
            
                /**
                 * Checks if the necessary files are present. Otherwise, return the default template slug.
                 * 
                 * @since       unknown
                 * @since       2.3.9       Changed the name from `_checkNecessaryFileExists()`.
                 * @return      string
                 */
                private function ___getTemplateSlugValidated( $sTemplateSlug, $iPostID=0 ) {
                    
                    if ( empty( $sTemplateSlug ) ) {
                        return $this->___getDefaultTemplateSlug();
                    }
                    
                    // this happens when the options have been reset
                    if ( ! $this->___oOption->get( array( 'arrTemplates', $sTemplateSlug ) ) ) {
                        foreach( $this->___oOption->aOptions[ 'arrTemplates' ] as $_aTemplate ) {
                            if ( ! isset( $_aTemplate[ 'sOldSlug' ] ) ) {
                                continue;
                            }
                            if ( $_aTemplate[ 'sOldSlug' ] !== $sTemplateSlug ) {
                                continue;
                            }
                            // It means the passed slug matches the old type slug of md5 hash of the absolute directory path which has been deprecated as of v2.3.9.
                            // Update the meta value and store the correct slug.
                            update_post_meta( $iPostID, 'fetch_tweets_template', $_aTemplate[ 'sSlug' ] );
                            return $_aTemplate[ 'sSlug' ];
                        }
                        return $this->___getDefaultTemplateSlug();
                        
                    }
                    
                    return $sTemplateSlug;
                    
                }
                    /**
                     * Returns the default template slug.
                     * @since       2.3.9
                     * @return      string
                     */
                    private function ___getDefaultTemplateSlug() {
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
            private function ___getTemplatePathBySlug( $sTemplateSlug ) {
                $_oTemplate = new FetchTweets_Template( $sTemplateSlug );    // passing none to the constructor creates default template object.
                return $_oTemplate->getPathByFileName( 'template.php' );
            }    

}
