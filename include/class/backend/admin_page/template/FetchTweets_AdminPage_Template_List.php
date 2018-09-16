<?php
/**
 * Fetch Tweets
 * 
 * Fetches and displays tweets from twitter.com.
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Defines an in-page tab.
 * 
 * @since       2.4.5
 */
class FetchTweets_AdminPage_Template_List extends FetchTweets_AdminPage_Tab_Base {

    
    /**
     * Called when the tab loads.
     * 
     */
    public function replyToLoadTab( $oFactory ) {

        // do_fetch_tweets_templates_list_template_table
        add_action( "do_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToDoTab' ) );
    
    }
    
    /**
     * Called in the middle of tab's rendering.
     */
    public function replyToDoTab( $oFactory ) {
        
        $oFactory->oTemplateListTable->prepare_items();
        
        // Embed input fields for the list table form.
        ?>
        <form id="template-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : 'fetch_tweets_templates'; ?>" />
            <input type="hidden" name="tab" value="<?php echo isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'list_template_table'; ?>" />
            <input type="hidden" name="post_type" value="<?php echo isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : FetchTweets_Commons::PostTypeSlug; ?>" />
            <!-- Now we can render the completed list table -->
            <?php $oFactory->oTemplateListTable->display() ?>
        </form>        
        <?php        
        
    }    
}