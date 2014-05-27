<?php
/**
 * Provides methods for output templates
 * 
 * @package			Fetch Tweets
 * @subpackage		
 * @copyright		Michael Uno
 * @since			2
 * @filter			fetch_tweets_template_path
 */
abstract class FetchTweets_Fetch_Template extends FetchTweets_Fetch_Format {
	
	/**
	 * Includes the template.
	 * 
	 * @since			2
	 * @param			array			$aTweets			the fetched tweet arrays.
	 * @param			array			$aArgs			the passed arguments such as item count etc.
	 * @param			array			$aOptions			the plugin options saved in the database.
	 */
	protected function _includeTemplate( $aTweets, $aArgs, $aOptions ) {

		// For backward compatibility for v1 - these variables will be accessible from the included template file.
		$arrTweets = & $aTweets;
		$arrArgs = & $aArgs;
		$arrOptions = & $aOptions;
		
		// Retrieve the template slug we are going to use.
		$aArgs['template'] = $this->_getTemplateSlug( ( array ) $aArgs['id'], $aArgs['template'] );
		
		// Call the template. ( template.php )
		include( apply_filters( "fetch_tweets_template_path", $this->_getTemplatePath( $aArgs['id'], $aArgs['template'] ), $aArgs ) );		
		
	}
	
		protected function _getTemplateSlug( $aPostIDs, $sTemplateSlug='' ) {

			// Return the one defined in the caller argument.
			if ( $sTemplateSlug && isset( $this->oOption->aOptions['arrTemplates'][ $sTemplateSlug ] ) )
				return $this->_checkNecessaryFileExists( $sTemplateSlug );
			
			// Return the one defined in the custom post rule.
			if ( isset( $aPostIDs[ 0 ] ) )
				$sTemplateSlug = get_post_meta( $aPostIDs[ 0 ], 'fetch_tweets_template', true );

			$sTemplateSlug = $this->_checkNecessaryFileExists( $sTemplateSlug );
			
			// Find the default template slug.
			if ( 
				empty( $sTemplateSlug ) 
				|| ! isset( $this->oOption->aOptions['arrTemplates'][ $sTemplateSlug ] ) 
			)
				return $GLOBALS['oFetchTweets_Templates']->getDefaultTemplateSlug();
			
			// Something wrong happened.
			return $sTemplateSlug;
			
		}
			/**
			 * Check if the necessary files are present. Otherwise, return the default template slug.
			 */
			protected function _checkNecessaryFileExists( $sTemplateSlug ) {
				
				if ( 
					( ! empty( $sTemplateSlug ) || $sTemplateSlug != '' ) 
					&& ( 
						! isset( $this->oOption->aOptions['arrTemplates'][ $sTemplateSlug ] )	// this happens when the options have been reset.
						|| ! @is_file( $this->oOption->aOptions['arrTemplates'][ $sTemplateSlug ]['strDirPath'] . '/template.php' )
						|| ! @is_file( $this->oOption->aOptions['arrTemplates'][ $sTemplateSlug ]['strDirPath'] . '/style.css' )
					)
				)
					return $GLOBALS['oFetchTweets_Templates']->getDefaultTemplateSlug();		
				
				return $sTemplateSlug;
				
			}
		
		/**
		 * Returns the path of the specified template.
		 * 
		 */
		protected function _getTemplatePath( $aPostIDs, $sTemplateSlug ) {
			
			if ( empty( $sTemplateSlug ) && isset( $aPostIDs[ 0 ] ) ) {
				$sTemplateSlug = get_post_meta( $aPostIDs[ 0 ], 'fetch_tweets_template', true );
			}
			
			if ( empty( $sTemplateSlug ) || ! isset( $this->oOption->aOptions['arrTemplates'][ $sTemplateSlug ] ) ) {
				return $GLOBALS['oFetchTweets_Templates']->getDefaultTemplatePath();
			}
				
			$_sTemplatePath = $this->oOption->aOptions['arrTemplates'][ $sTemplateSlug ]['strTemplatePath'];
			$_sTemplatePath = ( ! $_sTemplatePath || ! @is_file( $_sTemplatePath ) )
				? dirname( $this->oOption->aOptions['arrTemplates'][ $sTemplateSlug ]['strCSSPath'] ) . '/template.php'
				: $_sTemplatePath;
				
			return $_sTemplatePath;			
			
		}	
	
}