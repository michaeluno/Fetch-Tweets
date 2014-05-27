<?php
/**
	Provides utility methods for the template class.
	
	@package		Fetch Tweets
	@copyright		Copyright (c) 2013, Michael Uno
	@authorurl		http://michaeluno.jp
	@license     	http://opensource.org/licenses/gpl-2.0.php GNU Public License
	@since			2.3.5
	@filter			apply			fetch_tweets_filter_template_container_directories				Applies to the loading template container directories
	@filter			apply			fetch_tweets_filter_template_directories						Applies to the loading template directories
*/

abstract class FetchTweets_Templates_Utility {
	
	/**
	 * Returns an array holding the template directories.
	 * 
	 * @since			2.3.5
	 */
	protected function _getTemplateDirs() {
	
		$_aTemplateDirs = array();
		foreach( $this->_getTemplateContainerDirs() as $__sTemplateDirPath ) {
				
			if ( ! @file_exists( $__sTemplateDirPath  ) ) continue;
			$__aFoundDirs = glob( $__sTemplateDirPath . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR );
			if ( is_array( $__aFoundDirs ) ) {	// glob can return false
				$_aTemplateDirs = array_merge( $__aFoundDirs, $_aTemplateDirs );
			}
							
		}
		$_aTemplateDirs = array_unique( $_aTemplateDirs );
		$_aTemplateDirs = ( array ) apply_filters( 'fetch_tweets_filter_template_directories', $_aTemplateDirs );
		$_aTemplateDirs = array_filter( $_aTemplateDirs );	// drops elements of empty values.
		$_aTemplateDirs = array_unique( $_aTemplateDirs );
		return $_aTemplateDirs;
	
	}	
		/**
		 * Returns the template container directories.
		 * @since			2.3.5
		 */
		private function _getTemplateContainerDirs() {
			
			$_aTemplateContainerDirs = array();
			$_aTemplateContainerDirs[] = FetchTweets_Commons::getPluginDirPath() . DIRECTORY_SEPARATOR . 'template';
			$_aTemplateContainerDirs[] = get_template_directory() . DIRECTORY_SEPARATOR . 'fetch-tweets';
			$_aTemplateContainerDirs = apply_filters( 'fetch_tweets_filter_template_container_directories', $_aTemplateContainerDirs );
			$_aTemplateContainerDirs = array_filter( $_aTemplateContainerDirs );	// drops elements of empty values.
			return array_unique( $_aTemplateContainerDirs );
			
		}		
	
	
	/**
	 * Returns the file path of the screen shot.
	 */
	protected function _getScreenshotPath( $sDirPath ) {
		
		foreach( array( 'jpg', 'jpeg', 'png', 'gif' ) as $__sExt ) 
			if ( @is_file( $sDirPath . DIRECTORY_SEPARATOR . 'screenshot.' . $__sExt ) )
				return $sDirPath . DIRECTORY_SEPARATOR . 'screenshot.' . $__sExt;
			
	}
	
	/**
	 * Extracts information from the specified style.css file.
	 * 
	 * An alternative to get_plugin_data() as some users change the location of the wp-admin directory.
	 * 
	 * @return			array			Returns an array of template detail information from the given file path.	
	 */
	protected function _getTemplateData( $sPath, $sType='theme' )	{
	
		return get_file_data( 
			$sPath, 
			array(
				'strName'			=> 'Template Name',
				'strTemplateURI'	=> 'Template URI',
				'strVersion'		=> 'Version',
				'strDescription'	=> 'Description',
				'strAuthor'			=> 'Author',
				'strAuthorURI'		=> 'Author URI',
				'strTextDomain'		=> 'Text Domain',
				'strDomainPath'		=> 'Domain Path',
				'strNetwork'		=> 'Network',
				// Site Wide Only is deprecated in favour of Network.
				'_sitewide'			=> 'Site Wide Only',
			),
			$sType	// 'plugin' or 'theme'
		);				
		
	}		
	

}