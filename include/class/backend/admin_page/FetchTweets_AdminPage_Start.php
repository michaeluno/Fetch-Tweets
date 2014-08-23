<?php
abstract class FetchTweets_AdminPage_Start extends FetchTweets_AdminPageFramework {
	// abstract class FetchTweets_AdminPage_Start extends AdminPageFramework {

	public function start() {
				
		// Set the option property.
		$this->oOption = $GLOBALS['oFetchTweets_Option'];
		
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }
        
        // Add custom links to the description cell of the plugin listing table.
        if ( 'plugins.php' === $this->oProp->sPageNow ) {            
            $this->addLinkToPluginDescription(  
                '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=J4UJHETVAZX34">' . __( 'Donate', 'fetch-tweets' ) . '</a>',
                '<a href="http://en.michaeluno.jp/contact/custom-order/?lang=' . ( WPLANG ? WPLANG : 'en' ) . '">' . __( 'Order custom plugin', 'fetch-tweets' ) . '</a>'
            );						
        }        
        
		// Disable object caching in the plugin pages to help some caching plugins not to prevent the settings from being saved.
		if (
            isset( $_GET['post_type'] )
			&& in_array( $_GET['post_type'], array( FetchTweets_Commons::PostTypeSlug, FetchTweets_Commons::PostTypeSlugAccounts ) )
		) {
			$GLOBALS['_wp_using_ext_object_cache'] = false;	
		}		
		
		// For the list table bulk actions. The WP_List_Table class does not set the post type query string in the redirected page.
		// http://.../wp-admin/edit.php?page=fetch_tweets_templates&tab=&_wpnonce=ebed1d5343&_wp_http_referer=%2Fwp360%2Fwp-admin%2Fedit.php%3Fpost_type%3Dfetch_tweets%26page%3Dfetch_tweets_templates&action=activate&paged=1&action2=-1
		if ( 
			( isset( $_POST['post_type'] ) && $_POST['post_type'] == FetchTweets_Commons::PostTypeSlug )	// the form is submitted 
			&& ( ! isset( $_GET['post_type'] ) )	// and post_type query string is not in the url
			&& ( isset( $_GET['page'] ) && $_GET['page'] == 'fetch_tweets_templates' ) // and the page is the template listing table page,
		) {
			exit( wp_redirect( add_query_arg( array( 'post_type' => FetchTweets_Commons::PostTypeSlug ) + $_GET, admin_url() . '' . $GLOBALS['pagenow'] ) ) );
        }
	
		// Prepare the template array for the template listing table
		if ( isset( $_GET['page'] ) && 'fetch_tweets_templates' === $_GET['page'] ) {
			add_action( 'admin_menu', array( $this, '_replyToProcessBulkActionForTemplateListTable' ) );			
        }
				
	}
	
		public function _replyToProcessBulkActionForTemplateListTable() {

			$this->oTemplateListTable = new FetchTweets_ListTable(
				$GLOBALS['oFetchTweets_Templates']->getActiveTemplates() + $GLOBALS['oFetchTweets_Templates']->getUploadedTemplates()
			);
			$this->oTemplateListTable->process_bulk_action();
			
		}
        
        
	/*
	 * Customize the Menu
	 */
	public function _replyToBuildMenu() {
	
		parent::_replyToBuildMenu();
		
		// Somehow the settings link in the plugin listing page points to the Create Rule by List page. So fix it to the Settings page.
		$this->oProp->sDefaultPageSlug = FetchTweets_Commons::PageSettingsSlug;

		// Remove the default post type menu item.
		$_sPageSlug = $this->oProp->aRootMenu['sPageSlug'];
		if ( ! isset( $GLOBALS['submenu'][ $_sPageSlug ] ) ) { return; }	// logged-in users of an insufficient access level don't have the menu to be registered.
		foreach ( $GLOBALS['submenu'][ $_sPageSlug ] as $intIndex => $arrSubMenu ) {
						
			if ( ! isset( $arrSubMenu[ 2 ] ) ) continue;
			
			// Remove the default Add New entry.
			if ( $arrSubMenu[ 2 ] == 'post-new.php?post_type=' . FetchTweets_Commons::PostTypeSlug ) {
				unset( $GLOBALS['submenu'][ $_sPageSlug ][ $intIndex ] );
				continue;
			}
			
			// Copy and remove the Tag menu element to change the position. 
			if ( $arrSubMenu[ 2 ] == 'edit-tags.php?taxonomy=' . FetchTweets_Commons::TagSlug . '&amp;post_type=' . FetchTweets_Commons::PostTypeSlug ) {
				$_aMenuEntry_Tag = array( $GLOBALS['submenu'][ $_sPageSlug ][ $intIndex ] );
				unset( $GLOBALS['submenu'][ $_sPageSlug ][ $intIndex ] );
				continue;				
			}

		}
		
		// Second iteration.
		$intMenuPos_Setting = -1;
		foreach ( $GLOBALS['submenu'][ $_sPageSlug ] as $intIndex => $arrSubMenu ) {
			
			$intMenuPos_Setting++;	
			if (  isset( $arrSubMenu[ 2 ] ) && $arrSubMenu[ 2 ] == 'fetch_tweets_settings' ) 
				break;	// the position variable will now contain the position of the Setting menu item.
	
		}
	
		// Insert the Tag menu item before the Setting menu item.
		if ( isset( $_aMenuEntry_Tag ) ) {
			array_splice( 
				$GLOBALS['submenu'][ $_sPageSlug ], // original array
				$intMenuPos_Setting, 	// position
				0, 	// offset - should be 0
				$_aMenuEntry_Tag 	// replacement array
			);		
		}

		// Unfortunately array_splice() will loose all the associated keys(index).
		
	}
				        
			
}