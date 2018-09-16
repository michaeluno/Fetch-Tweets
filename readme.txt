﻿=== Fetch Tweets ===
Contributors:       Michael Uno, miunosoft
Donate link:        http://en.michaeluno.jp/donate
Tags:               twitter, tweet, tweets, widget, timeline, feed
Requires at least:  3.4
Requires PHP:       5.2.4
Tested up to:       4.7.3
Stable tag:         2.6.3
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html

Fetches and displays tweets from twitter.com with the Twitter API.

== Description ==
It displays tweets in the sidebar, posts, and pages. It does not rely on JavaScript so the tweets will be displayed to visitors disabling JavaScript in their browsers. Not only can you show your own tweets but also the mashed up results of multiple user's timelines.

It is easy to set up for WordPress beginners. It does not require you to provide authentication keys. 

Media files are automatically displayed such as YouTube videos and photos posted in tweets. You can disable them with the setting.

It allows developers to write additional add-ons and templates. One of the extensions, [Feeder](http://en.michaeluno.jp/fetch-tweets/extensions/feeder/), supports feeds so you can subscribe your favorite person’s tweets as RSS, which has become harder as the Twitter API was upgraded and the previous version no longer support tweet feed without authentication keys. With this addon, if you are a programmer, you can import the tweet data as JSON to your application by making the WordPress as own Twitter API server.

If you are a theme developer, you can easily customize the template for the tweet outputs. Just copy the existing template and modify the copied files and rename the template name. Then place the copied folder into the theme folder. And there you go! Your own template will be listed in the plugin’s setting page. This way, when the plugin updates, you won’t loose your modifications.

<h4>Features</h4>
- **Fetching Timeline** - by specifying the user name, the timeline can be fetched and displayed as well as your account's home timeline.
- **Search Results** - by specifying the search keyword, the results can be fetched and displayed.
- **Lists**	- tweet timeline for members of the specified list can be fetched and displayed.
- **Custom API Query** - if you are a developer and familiar with Twitter API, you can directly specify the query url to send to Twitter.
- **Tweet ID** - you can fetch tweets by Tweet ID.
- **Mashups** - you can display the combined results from multiple rule sets of your choosing.
- **Widget** - tweets can be displayed in the widgets that the plugin provides.
- **Shortcode** - with the shortcode, the fetched tweets can be displayed in posts and pages.
- **PHP Code** - with the PHP function, the fetched tweets can be embedded in the templates.
- **Custom Templates** - you can change the design by modifying/creating the template file.
- **Background Cache Renewal** - it renews the caches in the background so it will prevent the page load from suddenly getting stuck for fetching external sources.
- **Embedded Media** - urls of media elements can be automatically converted to embedded elements.
- **URL Query** - show tweets by setting a url query key such as `?screen_name=yourscreenamehere` to dynamically display tweets.
- **Server-side Processing** - the outputs of tweets are handled server-side. This means even your visitors turn off JavaScript in their browsers, tweets will be visible and search engines can catch those contents.
  
== Installation ==

= Install = 

1. Upload **`fetch-tweets.php`** and other files compressed in the zip folder to the **`/wp-content/plugins/`** directory.,
2. Activate the plugin through the 'Plugins' menu in WordPress.

= How to Use = 
1. Authenticate the plugin via **Dashboard** -> **Fetch Tweets** -> **Settings** ( -> **Authentication** ( the default tab ) ) -> the **Connect** button.
2. Set a rule via **Dashboard** -> **Fetch Tweets** -> **Add Rule by USer Name** / **Add Rule by Keyword Search**.
3. To use it as a widget, go to **Appearance** -> **Widgets** and add **Fetch Tweets by Rule Set** to the desired sidebar. And select the rule in the widget form.
4. To use the shortcode to display tweets in posts and pages, simply enter the shortcode like below in the post,

`[fetch_tweets id="123"]` 

where 123 is the rule ID you just created. The ID can be found in the *Fetch Tweets* page in the administration panel.

Go to the [Other Notes](http://wordpress.org/extend/plugins/fetch-tweets/other_notes/) section for more usage details.

== Other Notes ==

= Shortcode and Function Parameters =
The following parameters can be used for the shortcode or the PHP function of the plugin, <code>fetchTweets()</code>

* **id** - the ID(s) of the rule set. This cannot be used with the `tag`, `q`, or `screen_name` parameter. e.g.

`[fetch_tweets id="123"]`

`<?php fetchTweets( array( 'id' => 123 ) ); ?>`

In order to set multiple IDs, pass them with commas as the delimiter. e.g.

`[fetch_tweets id="123, 234, 345"]`

`<?php fetchTweets( array( 'id' => '123, 234, 345' ) ); ?>`

* **tag** - the tag(s) associated with the rule sets. This cannot be used with the `id`, `q`, or `screen_name` parameter. e.g.

`[fetch_tweets tag="WordPress"]`

`<?php fetchTweets( array( 'tag' => 'WordPress' ) ); ?>`

In order to set multiple tags, pass them with commas as the delimiter. e.g.

`[fetch_tweets tag="WordPress, developer"]`

`<?php fetchTweets( array( 'tag' => 'WordPress, developer' ) ); ?>`

* **operator** - the database query operator that is performed with the *tag* parameters. Either **AND**, **NOT IN**, or **IN** can be used. If this parameter is not set, `IN` will be used as the default value. For more information about this operator, refer to the [Taxonomy Parameter](http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters) section of Codex. e.g.

`[fetch_tweets tag="WordPress, PHP, JavaScript" operator="IN" ]`

`<?php fetchTweets( array( 'tag' => 'WordPress, PHP, JavaScript', 'operator' => 'IN' ) ); ?>`

`[fetch_tweets tag="developer" operator="NOT IN" ]`

`<?php fetchTweets( array( 'tag' => 'developer', 'operator' => 'NOT IN' ) ); ?>`

* **q** - the search keyword. This cannot be used with the `id`, `tag`, or `screen_name` parameter. e.g.

`[fetch_tweets q="#wordpress" lang="en"]`

`<?php fetchTweets( array( 'q' => '#wordpress', 'lang' => 'en' ) ); ?>`

* **screen_name** - the screen name(s). To pass multiple screen names, pass them separated by commas. This cannot be used with the `id`, `tag`, or `q` parameter. e.g.

`[fetch_tweets screen_name="WordPress,photomatt"]`

`<?php fetchTweets( array( 'screen_name' => 'WordPress,photomatt' ) ); ?>`

* **count** - the maximum number of tweets to display. e.g.

`[fetch_tweets id="456, 567" count="10" ]`

`<?php fetchTweets( array( 'id' => '456, 567', 'count' => 10 ) ); ?>`

* **avatar_size** - the size( max-width ) of the profile image in pixel. e.g.

`[fetch_tweets id="678" count="8" avatar_size="96" ]`

`<?php fetchTweets( array( 'id' => 678, 'count' => 8, 'avatar_size' => 96 ) ); ?>`

* **twitter_media** - true (1) / false (0). Determines whether the twitter media elements should be displayed.
* **external_media** - true (1)/ false (0). Determines whether the external media links should be replaced with embedded elements.

`[fetch_tweets id="678" twitter_media="0" external_media="1" ]`

`<?php fetchTweets( array( 'id' => 678, twitter_media="0" external_media="1" ) ); ?>`

- **get** - [2.4.0+] true (1) / false (0). Converts URL query key-values of the HTTP GET method into the functiom/shortcode arguments.

`[fetch_tweets get="1" ]`

`<?php fetchTweets( array( 'get' => true, ) ); ?>`

Then access the page that has the shortcode or the page by passing the arguments in the url.

`
http://your-site.domain/page-slug/?screen_name=miunosoft
`

`
http://your-site.domain/page-slug/?q=wordpress
`

- **show_error_on_no_result** - [2.4.7+] true / false. Determines whether a message should be shown when no result is fetched.
- **apply_template_on_no_result** - [2.4.8+] true / false. Determines whether a plugin template should be applied to the fetched tweets data when no result is fetched.

= How to Create Own Template =

**Step 1**

Copy the folder named ***plain*** or ***single*** in the plugin's template folder. Rename the copied folder to something you like.

**Step 2**

Edit the following files.

* **style.css** - defines the template's CSS rules. Also some of the information in the header comment sections will appear in the template listing table.
* **template.php** - defines the layout of the tweets. PHP coding skill is required.
* **functions.php** (optional) - loaded if the template is activated when the plugin starts. If you don't edit this file, do not include it. Be careful not to declare any PHP function or class that is already declared in the original file.
* **settings.php** (optional) - loaded only in the admin area if the template is activated. If you don't edit this file, do not include it. Be careful not to declare any PHP function or class that is already declared in the original file.

In the *style.css* file, include the comment area ( with /* */ ) at the top of the file with the following entries.

* **Template Name:** - the template name.
* **Author:** - your name.
* **Author URI:** - your web site url.
* **Description:** - a brief description about the template.
* **Version:** - the version number of the template.

e.g.

`
/**
 *	Template Name: Sample
 *	Author: Michael Uno
 *	Author URI: http://en.michaeluno.jp
 *	Description: A very simple sample template added as a WordPress plugin.
 *	Version: 1.0.0
 */
`

**Step 3** (optional)

Include a thumbnail image. Prepare an image with the name screenshot.jpg, screenshot.png, or screenshot.gif, and place the image in the working(copied in step 1) folder.

**Step 4**

Create a folder named **fetch-tweets** in the theme folder. If you use, for instance, Twenty Twelve, the location would be `.../wp-content/themes/twentytwelve/fetch-tweets/`.

Place the working folder (the copied and renamed one in step 1) in there. The plugin will automatically detect it and the template will be listed in the Template page of the admin page.

Optionally, a template can be added via a plugin. If you do so, add the template directory with the <code>fetch_tweets_filter_template_directories</code> filter hook.

e.g.
`
function FetchTweets_AddSampleTemplateDirPath( $aDirPaths ) {
	
	// Add the template directory to the passed array.
	$aDirPaths[] = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'sample';
	return $aDirPaths;
	
}
add_filter( 'fetch_tweets_filter_template_directories', 'FetchTweets_AddSampleTemplateDirPath' );
`

An example template is available [here](https://github.com/michaeluno/fetch-tweets-sample-template).

== Frequently Asked Questions ==

<h4>How to Set up</h4>
<h5><strong>Do I need API keys to use the plugin?</strong></h5>
They are not necessary as of v1.3.0. Just click on the **Connect** button in the `Authentication` (`Dashboard` -> `Fetch Tweets` -> `Settings` -> `Authentication`) page of the plugin. It will redirect you to the Twitter's authentication page. Then you log in to Twitter there and authorize the plugin.

<h5><strong>Can I use own API Application keys for authentication?</strong></h5>
Yes. Click on the *Set Keys Manually* button in the **Authentication** tab. It will redirects you to the page to set the keys manually.

If you do this, you need to create an application to access the Twitter API [here](https://dev.twitter.com/apps). Then create *consumer key*, *consumer secret*, *access token*, and *access token secret*. Without these, you won't be able to fetch tweets.

<h4>Customizing Outputs</h4>
<h5><strong>How can I create my own template file?</strong></h5>
See the How to Create Own Template section of the **[Other Notes](http://wordpress.org/plugins/fetch-tweets/other_notes/)** page.

<h4>Techniques</h4>
<h5><strong>How do I display tweets with a certain hashtag from a specific user?</strong></h5>

- Go to Dashboard -> Fetch Tweets -> Add Rule by Search.
- Type `#HASHTAG AND from:SCREENNAME` in the Search Keyword meta box option field. Change the part, `#HASHTAG`, to your searching hash tag and the part, `SCREENNAME`, to the account's screen name to fetch tweets from.
- Create a rule by pressing the Publish button.

<h4>Troubleshooting</h4>
<h5><strong>Why don't tweets update?</strong></h5>

It could be that your host disables WP Cron. In that case, try the `intense` caching mode which can be configured via `Dashboard` -> `Fetch Tweets` -> `Settings` -> `Cache` -> `Cache Settings` -> `Caching Mode`.

<h5><strong>I get the error "WordPress database error: [Lost connection to MySQL server during query]"?</strong></h5>

Increase the value set to `max_allowed_packet` in the MySQL server configuration file. (for [instruction](https://dev.mysql.com/doc/refman/5.5/en/packet-too-large.html))

== Screenshots ==

1. ***Twitter Template (Dark) ***
2. ***Twitter Template (Light) ***
3. ***Tweets Displayed in Page and Sidebar***
4. ***Fetching Rule Listing Table***
5. ***Widget Settings***
6. ***Manage Templates***
7. ***Authentication***

== Changelog ==

= 2.6.4 =
- Fixed an issue that the API authentication was no longer functioning due to the recent change of the [Twitter API policy](https://twittercommunity.com/t/action-required-sign-in-with-twitter-users-must-whitelist-callback-urls/105342).
- Fixed an issue that an error from the Twitter server could not be displayed when trying authentication.
- Fixed the PHP warning, `Notice: class-feed.php is <strong>deprecated</strong> since version 4.7.0! Use fetch_feed() instead.` in the `Extension` setting page.

= 2.6.3 - 03/13/2017 =
- Fixed an issue that some media elements did not open in a new browser window.

= 2.6.2 - 12/24/2016 =
- Fixed a bug that caused an error `Using $this when not in object context` when accessing a deprecated method.

= 2.6.1 - 12/22/2016 =
- Fixed a bug that caused an error "Call to undefined method FetchTweets_Output_Tweet::___getArguments_()".

= 2.6.0 - 11/28/2016 =
- Added the `Twitter` template.

= 2.5.3 - 11/25/2016 =
- Added cache count indications in the `Cache` setting section. 
- Fixed a bug that deleting expired caches were deleting unexpired items in some occasions.
- Changed the natively activated templates to include `Single` and `Sidebar`.

= 2.5.2 - 11/23/2016 =
- Fixed a warning that occurred in the `Manage Rules` page when a template is manually deleted while it is assigned to a rule.
- Fixed a bug that the `Settings` action link in plugins.php was linked to the wrong page.
- Fixed a bug that hash-tags in tweets were no longer linked, introduced in v2.5.0.
- Fixed a bug that embedded media were not updated after caches were renewed.
- Fixed a warning "Declaration of FetchTweets__FormSection__AddRuleByList::_getArguments() should be compatible with..." appeared in the `Add Rule by List` page.

= 2.5.1 - 11/20/20106 =
- Added embedded media options.
- Fixed an incompatibility issue of the time format in templates with Fetch Tweets v2.5 or below.
- Fixed a bug with the Plain template that caused a warning of undefined index when resetting the plugin options.
- Fixed a warning `declaration of FetchTweets_Template_Settings_Single::validateSettings() should be compatible with ...`.

= 2.5.0 - 11/19/2016 =
- Added the `Sidebar` template.
- Added an option for deleting settings upon plugin uninstallation.
- Added the ability to export/import settings.
- Added the `Manage Options` tab in the setting page.
- Fixed a bug that multiple media were not displayed.
- Fixed an issue that some tweets were truncated.
- Fixed a bug that retweet media were not displayed.
- Changed the `created_at` element value of tweet array loaded in templates to be intact.
- Changed the default height to 100% from 400px of the `Plain` template option.
- Changed the default value of the `operator` argument to `IN` from `AND` used when fetching tweets with tags.
- Refined the caching mechanism.
- Updated the [Admin Page Framework](http://admin-page-framework/michaeluno.jp/) library.

= 2.4.9 - 04/25/2015 =
- Fixed a compatibility issue with WordPress 4.2 in the template listing page.

= 2.4.8 - 04/17/2015 =
- Added the `apply_template_on_no_result` argument.
- Added the `Possibly Sensitive Materials` option.
- Changed the locations of some settings in the plugin admin pages.
- Changed the access rights set in the plugin settings applied to the menu/page access rights of the rule listing, definition, and tag pages.

= 2.4.7 - 04/11/2015 =
- Added the `show_error_on_no_result` argument.
- Added more languages for the Search rule type.
- Updated the [Admin Page Framework](http://admin-page-framework/michaeluno.jp/) library.

= 2.4.6 - 02/08/2015 =
- Added progress bars for rate limit statuses in the plugin setting page.
- Fixed an issue with the default templates that a vertical scroll bar did not disappear in some themes even when the height is set to `100%`.
- Fixed a compatibility issue with extension plugins that arguments and tweets were not formatted.

= 2.4.5 - 02/03/2015 =
- Added an option to disable content security policy warnings which appear in the browser console when the Follow button is displayed.
- Refined some form fields in the plugin setting page.
- Fixed broken links in extension and template lists.
- Fixed a bug in the Single template that media elements were not shown.
- Updated the [Admin Page Framework](http://admin-page-framework/michaeluno.jp/) library.

= 2.4.4 - 01/16/2015 =
- Tweaked the styling of built-in templates.

= 2.4.3 - 01/15/2015 =
- Removed the `!important` notations from the CSS files of the built-in templates.

= 2.4.2 - 01/12/2015 =
- Fixed an issue that custom arguments could not be set in the `fetchTweeets()` function and the plugin shortcode.
- Fixed an issue that a `fetch-tweets` template directory could not be loaded in child themes.
- Fixed a bug that template names were not listed properly in the rule listing table in one of the plugin setting pages.
- Removed the underlines of hyper links in the `Plain` and `Single` templates which appeared in the Twenty Fifteenth theme.
- Changed the timing of plugin localization to the `init` action hook from `plugins_loaded`.
- Updated the [Admin Page Framework](http://en.michaeluno.jp/admin-page-framework/) library.

= 2.4.1 - 12/06/2014 =
- Fixed the PHP Warning: `strlen() expects parameter 1 to be string...` occured in the `fetchTweets()` function introduced in v2.4.0.

= 2.4.0 - 12/06/2014 =
- Added a contact form in the plugin setting page.
- Added the `get` argument for the shortcode and the PHP function that converts URL query key-values to the shortcode/function parameters.
- Changed the media elements to be displayed outsize the container of the tweet text container and made the text enclosed in `p` tags, defined in the templates.
- Updated the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library.

= 2.3.9 - 11/30/2014 =
- Fixed an issue that templates could not be automatically detected when the user moves the site or changes the plugin directory name.
- Fixed a PHP warning occurred occasionally in the background: call_user_func_array() function.call-user-func-array: First argument is expected to be a valid callback, 'FetchTweets_oEmbed::_strip_newlines' was given in ...plugin.php on line 214.

= 2.3.8 - 10/16/2014 =
- Added the Follow button visibility option for the `Plain` template.
- Allowed 0 to be set for the `count` option.
- Optimized the caching mechanism to save the server memory usage.
- Optimized the process of loading template files.
- Fixed a bug that caches were not saved for the rules created by Home timeline (via Add Rule by Timeline).
- Fixed a PHP undefined constant warning in the admin area.

= 2.3.7 - 08/31/2014 =
- Fixed a bug that caused infinite function calls after resetting options as of v2.3.6.
- Fixed an issue that response data were not cached properly with sites that enables object caching.
- Updated the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library.

= 2.3.6 - 08/27/2014 =
- Added an option to enable/disable separator lines for the `Single` template.
- Added the ability to display an alternative profile image if the first set one fails to load in the built-in templates.
- Changed it not to enclose tweets in `p` tags as media elements are enclosed in `div` tags in built-in templates.
- Fixed an issue that strings applied to the `href` attributes did not get escaped.
- Fixed the widget title formatting.
- Fixed a bug that `Settings` link of plugin templates became not to appear as of v2.3.5.

= 2.3.5 - 08/25/2014 =
- Optimized the performance.
- Tweaked the built-in templates to escape certain strings in HTML attributes.
- Added warning messages to be displayed in the widget outputs when no rule or tag is selected.
- Added the ability to prevent excessive API requests could be made in a single page load when multiple shortcodes or plugin PHP functions are used in the page.
- Fixed an issue that tweets displayed with tags could not be formatted properly.
- Updated the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library.

= 2.3.4 - 05/15/2014 =
- Fixed an issue on some non GNU systems, like Solaris, that a fatal error occurred when the plugin is loaded.

= 2.3.3 - 05/14/2014 =
- Fixed an issue that when the `include retweet` option is unchecked, the count parameter was not accurately reflected in the `search` rule type.
- Fixed an undefined index warning that occurred by specifying an excessive value in the count parameter. 
- Added the visibility options for media elements to the widget forms.

= 2.3.2 - 05/04/2014 =
- Fixed a bug that some profile images without the file extension in the url were not shown.

= 2.3.1 - 05/03/2014 =
- Changed not to retrieve oembed element in the front end to prevent delays in page load.

= 2.3.0 - 05/01/2014 =
- Changed to display an error message for tweets of not authorized accounts.
- Added some more status items in the rate limit status table in the setting page.
- Added the `Template` column in the rule listing table.
- Added the `border-radius` property to the profile image element in the built-in templates.
- Added the ability to create a rule by tweet ID.
- Tweaked the styling of the `debug` template and added a screen-shot.
- Updated the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v3.0.5 which includes patches for issues with [WordPress MU](https://codex.wordpress.org/WordPress_MU).

= 2.2.1 - 04/26/2014 =
- Rephrased a label of an option item.
- Made larger profile image sizes set in the template setting page take effect in preview pages.

= 2.2.0 - 04/24/2014 =
- Made the built-in templates respect the `Include Retweets` option value.
- Added the `Include Retweets` option for the `Search` and `Home Timeline` rule type. 
- Updated the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v3.0.5b.
- Added an option that determines whether the rule pages should be searchable with the WordPress search form.
- Added the `Debug` template.
- Added the ability to add a rule by custom API request.

= 2.1.1.1 - 03/25/2014 =
* Fixed the warnings: `Invalid argument supplied for foreach() in ...wp-content/plugins/fetch-tweets/include/class/boot/registry/FetchTweets_AutoLoad.php on line 93`, which occurred on some servers.

= 2.1.1 - 03/25/2014 =
* Tweaked the routines of plugin setting pages.

= 2.1 - 03/23/2014 =
* Added the ability to create a rule by external JSON feed source.
* Added the `Caching Mode` option.

= 2 - 03/19/2014 =
* Added the ability to create a rule by home timeline.
* Added the geometry coordinate picker field for the search tweet type.
* Renewed the positions of meta boxes in the rule definition page. 
* (Breaking change) Changed the internal options structure. Accordingly, options of custom templates may be affected.
* (Breaking change) Updated the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v3.0.2b. Accordingly, setting pages of custom templates may be affected.

= 1.3.5.1 - 03/06/2014 =
* Fixed a bug that caused a fatal error when newly connecting to Twitter introduced since v1.3.5.

= 1.3.5 - 03/06/2014 =
* Fixed the issue of TwitteroAuth library version conflicts.
* Refactored the code. 

= 1.3.4.1 - 03/05/2014 =
* Fixed the warning that occurs when a user with the insufficient access level to the plugin setting pages logged in to the admin page, `Warning: Invalid argument supplied for foreach() in ...\wp-content\plugins\fetch-tweets\class\FetchTweets_AdminPage_.php on line 544`

= 1.3.4 - 02/28/2014 = 
* Improved the caching mechanism.

= 1.3.3.11 - 02/26/2014 =
* Improved the caching mechanism by reducing the number of background processes.

= 1.3.3.10 - 02/21/2014 =
* Fixed a possible issue that caused extra database queries.

= 1.3.3.9 - 02/02/2014 =
* Fixed a fatal error caused since v1.3.3.8.

= 1.3.3.8 - 02/01/2014 =
* Fixed a bug that the date picker ui did not appear in the date option input field.

= 1.3.3.7 - 01/26/2014 =
* Fixed a bug that the link of the setting page in the plugin listing table was pointing not to the Setting page.
* Improved the caching mechanism not to entirely rely on WP Cron.
* Updated the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v2.1.7.2.
* Fixed the warning: `Strict standards: Declaration of ... should be compatible with ...`.
* Fixed a possible security issue in the plugin admin pages.
* Fixed a possible issue caused by locked transients that could prevent caches from updating.

= 1.3.3.6 - 01/15/2014 =
* Fixed a bug that setting 0 for the height or width did not set no limit but setting 0px.

= 1.3.3.5 - 01/10/2014 =
* Fixed an issue that the plugin post type output ignored the `the_content` filter.
* Added a parameter to the `fetchTweets()` function to decide whether the output should be returned or printed.

= 1.3.3.4 - 12/17/2013 =
* Fixed a bug that caused lots of database queries in the plugin setting pages due to disabling object caching since v1.3.3.3.

= 1.3.3.3 - 12/12/2013 =
* Updated the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v2.1.5.
* Disabled object caching in the plugin setting pages.
* Added the List Request Limit status item in the authentication setting page.
* Improved the method of renewing caches.
* Added the `fetchTweets()` function to support the `screen_name` and `q` parameters.

= 1.3.3.2 - 11/20/2013 =
* Fixed an issue with Instagram's images. 
* Improved the method of loading template CSS files.
* Tweaked the default templates to have an `!imporatant` declaration for each rule.
* Added the Japanese localization.

= 1.3.3.1 - 11/13/2013 =
* Fixed an issue with below the WordPress version 3.5 that the function `wp_safe_remote_get` was not found.
* Fixed an issue with Instagram's images.

= 1.3.3 - 11/07/2013 =
* Fixed a bug that caused the cache to be renewed every time when the rule ID is specified.
* Added the option to cache errors. 
* Added the geometrical option for the search rule.
* Added more languages for the search rule.
* Added the `screen_name` parameter to the shortcode that displays the tweets by specified Twitter accounts.
* Added the `q` parameter to the shortcode that displays the result of Twitter keyword searches.
* Fixed an issue that duplicated tweets were displayed in mush-up results.

= 1.3.2.1 - 10/30/2013 = 
* Fixed a bug that a fatal error occurred in the background, Fatal error:  Call to a member function decode() on a non-object.
* Fixed a bug that an undefined index typenow warning occurred when a third-party script performs a custom database query with the WP_Query class in the edit.php admin page.

= 1.3.2 - 10/02/2013 =
* Added the CSS links in the template listing table.
* Fixed an issue that bullets of list tags appeared in some themes with the default templates.
* Fixed a possible issue that profile images do not get aligned properly in some themes in the default templates.
* Fixed a possible issue that profile images do not get sized properly in some themes in the default templates.
* Fixed the GMT time difference for the time output in the default templates.

= 1.3.1 - 09/30/2013 =
* Added the option to set follower count and screen name for the Follow button for the default templates.
* Added the Retweet, Reply, Favourite buttons and their visibility option for the default templates.

= 1.3.0 - 09/27/2013 =
* Fixed a bug that an undefined constant warning occurred in WordPress v3.4.x or below.
* Added the ability to perform authentication without creating an application by the user.
* Tweaked the caching system for embedded media elements.
* Tweaked the default template styles for embedded images to keep the aspect ratio.

= 1.2.0 - 09/21/2013 =
* Added the ability to automatically embed media elements.
* Fixed the minimum required WordPress version to 3.3.
* Removed redundant functions from the default template functions file.
* Added authentication information in the setting page.
* Removed the *Include Retweets* option from the search type because Twitter API does not support that parameter and in fact it was not working.
* Fixed an issue that sometimes caches could not be saved due to the corrupt serialized array.
* Fixed a bug that meta box fields' input values did not get validated. 
* Fixed a bug that the Item Count option did not take effect in the preview.
* Added the option to set profile image position for the default templates.
* Tweaked the default template styles for right to left text display.
* Updated the Admin Page Framework library to v2.1.0.
* Added the option to set access rights to the setting pages.
* Added the option to clear caches.
* Added the ability to fetch tweets by list.

= 1.1.1 - 09/08/2013 =
* Tweaked the default template styles.
* Changed the timing of loading active template's functions files to before the header gets sent.
* Added the visibility, margins, paddings, and the background color options for the default templates. The users may need to re-configure the height and width of the template option. 
* Updated the Admin Page Framework library to v2.0.2.

= 1.1.0 - 08/18/2013 = 
* Added the ability to reset the plugin options.
* Added the templates named **Single** and **Plain**.
* Changed the template system ( ***Breaking Change*** ).
* Changed to display the error message when the Twitter API returns an error. 

= 1.0.1 - 07/29/2013 =
* Added the ability for other plugins to override the registering classes of this plugin.
* Supported third party extensions to be added.
* Added the *widget-title* class selector to the widget title output.
* Changed the sub-menu positions and the menu name of the rule listing table page to Manage Rule from the plugin name.
* Changed the *ids* and *tags* parameters to be obsolete. These will be removed in near updates.
* Changed the *id* and *tag* parameters to accept comma-delimited elements like the *ids* and *tags* parameters. 
* Changed the variables passed to the template file. ( ***Breaking Change*** )

= 1.0.0.3 - 07/23/2013 =
* Added the ability to convert media links to the hyper links.
* Tweaked the default template style.
* Added the title field for the widget form.

= 1.0.0.2 - 07/22/2013 =
* Added the *operator* parameter for the *tag* and *tags* parameters that specifies the use of *AND*, *IN*, or *NOT IN* for the database query.
* Added the *tags* parameter that enables to fetch tweets with multiple tags.
* Added a widget that fetches tweets by tag.
* Fixed a bug that profile images get lost with the *tag* parameter since 1.0.0.1.
* Fixed a bug that caused a warning in the background, "PHP Warning:  in_array(): Wrong datatype for second argument in ...\wp-content\plugins\fetch-tweets\class\FetchTweets_WidgetByID_.php on line 76"

= 1.0.0.1 - 07/21/2013 =
* Added the *avater_size* parameter for the *fetchTweets()* function and the shortcode.
* Added the ability to specify the profile image size as well as the visibility of the image.
* Fixed a bug that caused a warning in the background, "PHP Notice: Undefined index: title in ...\wp-admin\includes\meta-boxes.php on line 352"
* Fixed a bug that some transients did not get renewed. 
* Tweaked the precision of converting urls, hashtags, and user mentions to the hyper-links.

= 1.0.0 - 07/20/2013 =
* Initial Release.