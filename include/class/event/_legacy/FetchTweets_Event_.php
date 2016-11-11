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
 * An event handler class.
 *   
 * @since        1.0.0
 * @action       hook            fetch_tweets_action_setup_transients
 * @action       hook            fetch_tweets_action_simplepie_renew_cache
 * @action       hook            fetch_tweets_action_transient_renewal
 * @filter       apply           fetch_tweets_filter_plugin_cron_actions            Applies to the action arrays that the plugin Cron triggers.
 * 
 * @deprecated   2.5.0
 */
abstract class FetchTweets_Event_ {}