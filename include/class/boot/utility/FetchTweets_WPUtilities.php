<?php
/**
 *	Provides utility methods which use WordPress functions.
 *
 * @package     Fetch Tweets
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl	http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since		1.3.3.2
 * 
 */

final class FetchTweets_WPUtilities {

	/**
	 * Calculates the URL from the given path.
	 * 
	 * @since			1.3.3.2
	 * @since			1.3.3.8			FIxed an issue that /./ gets inserted.
	 * @static
	 * @access			public
	 * @return			string			The source url
	 */
	static public function getSRCFromPath( $sFilePath ) {
				
		$oWPStyles = new WP_Styles();	// It doesn't matter whether the file is a style or not. Just use the built-in WordPress class to calculate the SRC URL.
		$sRelativePath = FetchTweets_Utilities::getRelativePath( ABSPATH, $sFilePath );		
		$sRelativePath = preg_replace( "/^\.[\/\\\]/", '', $sRelativePath, 1 );	// removes the heading ./ or .\ 
		$sHref = trailingslashit( $oWPStyles->base_url ) . $sRelativePath;
		unset( $oWPStyles );	// for PHP 5.2.x or below
		return esc_url( $sHref );
		
	}
	
	/**
	 * Returns the file path by checking if the given path is a file.
	 * 
	 * If fails, it attempts to check with the relative path to ABSPATH.
	 * 
	 * This is necessary when some users build the WordPress site locally and immigrate to the production site.
	 * In that case, the stored absolute path won't work so it needs to be converted to the one that works in the new environment.
	 * 
	 * @since			2.3.5
	 */
	public static function getReadableFilePath( $sFilePath, $sRelativePathToABSPATH='' ) {
		
		if ( @is_file( $sFilePath ) ) {
			return $sFilePath;
		}
		
		if ( ! $sRelativePathToABSPATH ) {
			return false;
		}
		
		// try with the relative path.
		$_sAbsolutePath = realpath( trailingslashit( ABSPATH ) . $sRelativePathToABSPATH );
		if ( ! $_sAbsolutePath ) {
			return false;
		}
		
		if ( @is_file( $_sAbsolutePath ) ) {
			return $_sAbsolutePath;
		}
		
		return false;		
		
	}	
	

}