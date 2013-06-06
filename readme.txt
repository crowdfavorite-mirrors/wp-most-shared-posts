=== Most Shared Posts - Social Media counter for Twitter, Facebook & Google+ ===
Contributors: tomanthony
Link: http://www.tomanthony.co.uk/wordpress-plugins/most-shared-posts/
Donate link:  https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZS48RS294BLGN
Description:  A widget to list posts by those with the most social shares (Facebook likes, Twitter tweets and Google +1s) in your sidebar.
Tags: facebook, like button, twitter, tweet, google, plusone, shares, social media, sharing, social, seo, popular, posts, sharethis, addthis, sharedaddy
Requires at least: 2.8
Tested up to: 3.2.1
Version: 1.1.0
Stable tag: 1.1.0
Author:       Tom Anthony
Author URI:   http://www.tomanthony.co.uk/

== Description ==

Track your most popular blog posts, sorted by those with the most social shares. It checks the number of Facebook Likes, Twitter Tweets, and Google +1s. You can display them to your users or just view the data in the WP admin area.

Features include:

* You can choose which of the 3 social counts to include.
* Caches results to ensure good performance. Older posts are cached for longer.
* For beginners, it is very easy to install with no necessary customisation.
* Can include a list of most shared posts in a post/page using a shortcode.
* Various layout options, including post thumbnails.

* [Most Shared Posts](http://www.tomanthony.co.uk/wordpress-plugins/most-shared-posts/) plugin homepage.
* [Follow @tomanthonyseo on Twitter](http://twitter.com/tomanthonyseo).

== Installation ==

The steps to install 'Most Shared Posts' are quite simple:

1. Upload `most-shared-posts` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. You are now collecting data, which you can view in Dashboard->Most Shared Posts Stats.
4. NOTE: The plugin throttles how quickly it gathers the social data, to ensure it does not cause any slowness for your users. For small sites with less than 100 posts, this should typically be done in the first hour. For sites with other 2000 posts it could take a day or more.

If you'd like display the count to your users then add a widget:

1. Go to the 'Widgets' page in the 'Appearance' section of WordPress.
2. Now you can add the widget to different pages of your site. Recommended pages to add the widget: "Sidebar Index", "Sidebar Single", "Sidebar Category"
3. You can customise the plugin differently for each of these sections should you wish.

Please consider a donation! :)

== Upgrade Notice ==

= 1.1.0 =
Improves data collection, adds a stats panel in WP admin, and adds a shortcode option.

== Frequently Asked Questions ==

= I just installed and refreshed the page, but my most shared posts are not at the top of the list? =

The plugin will take a little time to collect all the data about your posts. It does so in batches so as not to upset the social networks by requesting too much data at once. You should find that it'll sort itself out and catch up quite quickly.

= I just got tweeted but the count hasn't updated! Why not? =

The results for each post are cached for varying lengths of time depending on the posts age. Very recent posts (less than 2 days old) are cached for an hour, and then older posts are cached for incrementally longer periods of time. Posts in the last month are cached for 12 hours, posts 1-6 months old are cached for 48 hours and older posts are cached for a week.

= My older posts aren't showing up! =

When you add an instance of the widget to a sidebar in the WordPress admin section, you can customise how far back you wish to look; the default is 2 years.

= Where can I change how many posts are included in the list? =

When you add an instance of the widget to a sidebar in the WordPress admin section, you can customise how many posts to include; the default is 5.

= I activated the plugin, but I don't see it.   =

This plugin is a widget, so after activating it you need to go to the Appearance->Widgets section of your WordPress settings and select which pages you'd like it to appear on.

= Can I include the count inside a post or a page? =

Yes - there is now a shortcode option. The shortcode has a few options (all are optional):

[most-shared-posts num_posts="5" max_month_age="24" title="Most Shared Posts"]

= I have no shares. How depressing... help! =

Assuming you're providing content worthy of being shared… Make sure you are including sharing buttons on your blog in a consistent fashion. I recommend adding them into your theme, or alternatively:

* Alex Moss, of Pleer, has created some great social button plugins for Twitter, Facebook and Google+.
* The AddThis plugin is very popular, and provides a variety of sharing buttons.
* At sharethis.com, they provide a variety of sharing plugins.
* The Sharedaddy plugin, part of Jetpack from WordPress.com, provides a range of social buttons.

== Screenshots ==

1. Widget Settings.
2. Most Shared Posts Widget on Sofa Avatar Theme's sidebar.
3. Social metrics shown in admin dashboard.

== Changelog ==

= 1.0.0 =

* Initial release.

= 1.0.1 =

* Update on the format of the API requests for added robustness.
* Added link to the FAQ on plugin activation.

= 1.1.0 =

* Can now view social stats in admin area (with or without an active widget).
* Added a shortcode option [most-shared-posts] to include within posts/pages.
* Data now collected without an active widget.
* Improved method for scheduling data collection.
* Various code tidying and improvements.



