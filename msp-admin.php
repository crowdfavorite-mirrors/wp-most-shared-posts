<?php
/*
Most Shared Posts
*/

require_once('msp-fetch.php');

// Deactivate function re-initialises the welcome message
function most_shared_posts_deactivate() {
	update_option('toma_msp_welcome_message', false);
}

// Function to add settings link on plugin page
function most_shared_posts_link($links) {
	$settings_link = '<a href="options-general.php?page=most_shared_posts">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}

// Initiates and gives default values to the plugins options
function show_most_shared_posts_options() {
	add_options_page('Most Shared Posts Options', 'Most Shared Posts', 'manage_options', 'most_shared_posts', 'most_shared_posts_options');
	
	add_option('toma_msp_include_fb', 'on');
	add_option('toma_msp_include_twitter', 'on');
	add_option('toma_msp_include_google', 'on');
	add_option('toma_msp_font_size', '');
	add_option('toma_msp_icon_size', 16);
	add_option('toma_msp_suppress_icons', 'off');
	add_option('toma_msp_h3_wrap', 'off');
	add_option('toma_msp_attribution_link', 'off');
}

// This function displays the activation notice that appears
// each time the plugin is activated.
function most_shared_posts_activation_notice() {
	
	if (!get_option('toma_msp_welcome_message'))
	{
		$settings_link = "options-general.php?page=most_shared_posts";
?>
		<div class="error fade"><p>Most Shared Posts will take a few mins to a few hours to gather the data. You may want to:</p>
		
		<ul style="list-style-type: square; list-style-position:inside; margin-left: 15px;">
			<li>Add a "Most Shared Posts" widget in your <a href="widgets.php">appearance settings</a>.</li>
			<li>Configure which social networks to include in the <a href="<?=$settings_link?>">plugin settings</a>.</li>
			<li>View the social shares of your posts in the <a href="index.php?page=most-shared-posts-stats">Most Shared Posts Dashboard</a>.</li>
			<li>Read the <a href="http://www.tomanthony.co.uk/wordpress-plugins/most-shared-posts/#faq" target="_blank">online FAQ</a>.</li>
		</ul>
		
		</div>
<?php
	}
	update_option('toma_msp_welcome_message', true);
}


// Displays the menu for the admin stats screen
function most_shared_posts_stats() {
	if ( function_exists('add_submenu_page') ) 
	{
		 $plugin_page = add_submenu_page('index.php', 'Most Shared Posts', 'Most Shared Posts', 'manage_options', 'most-shared-posts-stats', 'most_shared_posts_stats_display');
		
		// Hook up our admin CSS to appear on that page
		add_action( 'admin_head-'. $plugin_page, 'most_shared_posts_admin_head' );
	}
}

// Our header function which'll hook in our CSS file
function most_shared_posts_admin_head() {
	echo '<link rel="stylesheet" type="text/css" href="' . plugins_url('most-shared-posts-admin.css', __FILE__). '">';
}

// Displays the stats screen in the admin area.
function most_shared_posts_stats_display() {
	require (dirname(__FILE__) . '/stats-screen.php');
}

// Displays the main options screen. Not the little one for each widget.
function most_shared_posts_options() { ?>
<style type="text/css">
div.headerWrap { background-color:#e4f2fds; width:200px}
#options h3 { padding:7px; padding-top:10px; margin:0px; cursor:auto }
#options label { width: 300px; float: left; margin-left: 10px; }
#options input { float: left; margin-left:10px}
#options p { clear: both; padding-bottom:10px; }
#options .postbox { margin:0px 0px 10px 0px; padding:0px; }
</style>
<div class="wrap">
<form method="post" action="options.php" id="options">
<?php wp_nonce_field('update-options') ?>
<h2>Most Shared Posts Options</h2>

<div class="postbox-container" style="width:100%;">
	<div class="metabox-holder">
	<div class="postbox">
		<h3 class="hndle"><span>Information</span></h3>
		<div style="margin:20px;">
			<p style="clear: none;">The social counts are cached to ensure good performance. Posts in the last 30 days are cached for between 1 to 12 hours depending on age. Posts up to 6 months old are cached for 48 hours, and posts over 6 months are cached for a week.</p>


	<p style="clear: none;"><a href="index.php?page=most-shared-posts-stats" style="text-decoration:none" class='button-secondary'>Your Most Shared Posts</a> - Click here to view the social stats for your most shared posts on your dashboard.</p>
	
	
	<p style="clear: none;"><a href="http://www.tomanthony.co.uk/wordpress-plugins/most-shared-posts/" style="text-decoration:none" target="_blank"  class='button-secondary'>Plugin Homepage</a> - More information on this plugin including FAQs. If you have feature requests you can contact me there. :)</p>


		</div>
	</div>
	</div>


	<div class="metabox-holder">
	<div class="postbox">
		<h3 class="hndle"><span>Settings</span></h3>
	<div style="margin:20px;">
		<p>
			You can choose which pages to include the "Most Shared Posts" widget on in the 'Widgets' section of your Wordpress control panel. You can then separately configure each widget (how many posts to list, and how far back in time to include posts) when you add them. The settings below will affect all the "Most Shared Posts" widgets. It isn't necessary to place a widget if you don't want to showcase your most shared posts to your users, but would rather just collect the stats for you to see yourself in the admin section.
		</p>
		
		<p>
			Select which networks you wish to include; this will affect whether they are counted towards the total and whether they are displayed or not:
		</p>
		
		<!-- Include Facebook Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( get_option('toma_msp_include_fb'), 'on' ); ?> id="toma_msp_include_fb" name="toma_msp_include_fb" /> 
			<label for="toma_msp_include_fb">Include Facebook Likes</label>
		</p>

		<!-- Include Twitter Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( get_option('toma_msp_include_twitter'), 'on' ); ?> id="toma_msp_include_twitter" name="toma_msp_include_twitter" /> 
			<label for="toma_msp_include_twitter">Include Tweets</label>
		</p>

		<!-- Include Google Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( get_option('toma_msp_include_google'), 'on' ); ?> id="toma_msp_include_google" name="toma_msp_include_google" /> 
			
			<label for="toma_msp_include_google">Include Google +1's</label>
		</p>
		
		<br />
		
		<p>This option will mean that no social network icons or share counts are displayed. The posts will just be shown by link, and will still be ordered by the networks selected above.</p>
		
		<!-- Suppress Icons Checbkox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( get_option('toma_msp_suppress_icons'), 'on' ); ?> id="toma_msp_suppress_icons" name="toma_msp_suppress_icons" /> 
			
			<label for="toma_msp_suppress_icons">Don't display icons and counts.</label>
		</p>
		
		<br />
		
		
		
		<p>
			Configure how the share icons and counts appear:
		</p>
		
		
		<!-- Font Size Drop Down -->
		<p>
			<label for="toma_msp_font_size">Font size for counts:</label>
			
			<select name="toma_msp_font_size">
                  <option value="smaller" <?php if (get_option('toma_msp_font_size') == 'smaller') { echo "selected=\"selected\""; } ?>>
                    smaller
                  </option>
                  <option value="standard" <?php if (get_option('toma_msp_font_size') == 'standard') { echo "selected=\"selected\""; } ?>>
                    standard
                  </option>
                  <option value="bigger" <?php if (get_option('toma_msp_font_size') == 'bigger') { echo "selected=\"selected\""; } ?>>
                    bigger
                  </option>
                  <option value="even-bigger" <?php if (get_option('toma_msp_font_size') == 'even-bigger') { echo "selected=\"selected\""; } ?>>
                    even-bigger
                  </option>
                  <option value="huge" <?php if (get_option('toma_msp_font_size') == 'huge') { echo "selected=\"selected\""; } ?>>
                    huge
                  </option>
            </select>
		</p>
		
		<!-- Icon Size Drop Down -->
		<p>
			<label for="toma_msp_icon_size">Icon size for network icons:</label>
			
			<select name="toma_msp_icon_size">
                  <option value="smaller" <?php if (get_option('toma_msp_icon_size') == 'smaller') { echo "selected=\"selected\""; } ?>>
                    smaller
                  </option>
                  <option value="standard" <?php if (get_option('toma_msp_icon_size') == 'standard') { echo "selected=\"selected\""; } ?>>
                    standard
                  </option>
                  <option value="bigger" <?php if (get_option('toma_msp_icon_size') == 'bigger') { echo "selected=\"selected\""; } ?>>
                    bigger
                  </option>
                  <option value="huge" <?php if (get_option('toma_msp_icon_size') == 'huge') { echo "selected=\"selected\""; } ?>>
                    huge
                  </option>
            </select>
		</p>
	
		</div>
	</div>
	</div>

	<div class="metabox-holder">
	<div class="postbox">
		<h3 class="hndle"><span>Advanced Settings</span></h3>
		<div style="margin:20px;">
		<p>
			Using these advanced settings is not necessary, but if you are an advanced user and want to tweak how the widget is displayed, then this is where you can do that.
		</p>
		
		<br />
		
		
		<p>Depending on your theme, you may find that the list looks better if wrapped in an H3 tag.</p>
		
		<!-- H3 Wrap Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( get_option('toma_msp_h3_wrap'), 'on' ); ?> id="toma_msp_h3_wrap" name="toma_msp_h3_wrap" /> 
			
			<label for="toma_msp_h3_wrap">Wrap post list in H3.</label>
		</p>
		
	
		</div>
	</div>
	</div>
	
	<div class="metabox-holder">
	<div class="postbox">
		<h3 class="hndle"><span>Say Thanks</span></h3>
		<div style="margin:20px;">
			<p>If you'd like to say thanks for the time I spent working on this plugin, then any of these options would be really appreciated.</p>
			
					<table>
						<tr>
							<td style="width: 30%; padding: 10px; vertical-align: top;">
								<div class="postbox" style="min-width: 200px; height: 100%;">
									<h3 class="hndle"><span>Donate</span></h3>
										<div style="margin:20px;">
											<p>Every donation, whatever size, makes me disproportionately smiley. :)</p>
											<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZS48RS294BLGN" target="_blank"><img src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" /></a>
										</div>
								</div>
							</td>
							
							<td style="width: 30%; padding: 10px; vertical-align: top;">
								<div class="postbox" style="min-width: 200px; height: 100%;">
									<h3 class="hndle"><span>Share this plugin</span></h3>
										<div style="margin:20px; height: 60px;">
										<div style="width: 178px; margin: 0 auto;">

<div style="float: left; margin-right: 10px;"><a href="http://twitter.com/share" class="twitter-share-button" data-url="http://www.tomanthony.co.uk/wordpress-plugins/most-shared-posts/" data-text="I'm using the Most Shared Posts WordPress plugin:" data-count="vertical" data-via="TomAnthonySEO">Tweet</a></div>

<div style="float: left; margin-right: 6px; padding-top: 3px;"><div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="http://www.tomanthony.co.uk/wordpress-plugins/most-shared-posts/" send="false" layout="box_count" width="50" show_faces="false" action="like" font=""></fb:like></div>
										
<div style="float: left; padding-top: 2px;"><g:plusone size="tall" href="http://www.tomanthony.co.uk/wordpress-plugins/most-shared-posts/"></g:plusone></div>

										</div>
										</div>
<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
								</div>
							</td>
							
							<td style="width: 30%; padding: 10px; vertical-align: top;">
								<div class="postbox" style="min-width: 200px; height: 100%;">
									<h3 class="hndle"><span>Let others know</span></h3>
										<div style="margin:20px;">
											<p>Activate the attribution link (adds 'Plugin by Tom Anthony' as small text below the widget):</p>
											<input class="checkbox" type="checkbox" <?php checked( get_option('toma_msp_attribution_link'), 'on' ); ?> id="toma_msp_attribution_link" name="toma_msp_attribution_link" /><label for="toma_msp_attribution_link" style="min-width: 10px; width: 100px;">Activate link</label><br />
										</div>
								</div>
							</td>
						</tr>
					</table>
					
					<div style="clear: both;"></div>
		</div>
	</div>
	</div>
	
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="toma_msp_include_fb, toma_msp_include_twitter, toma_msp_include_google, toma_msp_font_size, toma_msp_icon_size, toma_msp_suppress_icons, toma_msp_h3_wrap, toma_msp_attribution_link" />
<div class="submit"><input type="submit" class="button-primary" name="submit" value="Save Most Shared Posts Settings"></div>

</form>
</div>

<?php
}

// The uninstall function - clears out options and metadata
function most_shared_posts_deinstall() {

	// Delete the options we had setup.
	delete_option('toma_msp_include_fb');
	delete_option('toma_msp_include_twitter');
	delete_option('toma_msp_include_google');
	delete_option('toma_msp_font_size');
	delete_option('toma_msp_icon_size');
	delete_option('toma_msp_attribution_link');
	delete_option('toma_msp_suppress_icons');
	delete_option('toma_msp_welcome_message');
	
	
	// Delete the recent check transient
	delete_transient("msp_recently_checked_counts");
	
	// Delete all meta data and transients
	// We are going to have to loop all posts
	
	$args = array(
		'posts_per_page' => -1,
		'ignore_sticky_posts' => 1
	);
	
	$posts_in_range = new WP_Query( $args );
				
	// Run the loop
	while ( $posts_in_range->have_posts() ) : $posts_in_range->the_post();
		
		$transient_base = "msp_trans_" . get_the_ID() . "_";
		
		delete_transient($transient_base."_fb_likes");
		delete_transient($transient_base."_tweets");
		delete_transient($transient_base."_google_plus_ones");
		
		delete_post_meta(get_the_ID(), "_msp_fb_likes");
		delete_post_meta(get_the_ID(), "_msp_tweets");
		delete_post_meta(get_the_ID(), "_msp_google_plus_ones");
		delete_post_meta(get_the_ID(), "_msp_total_shares");
	
	endwhile;
	
	// Reset the post info after our loop
	// not sure I need this here
	wp_reset_postdata();
	
	// The transients would delete themselves, but
	// we delete them right away as it could be helpful
	// if a user is trying to uninstall and reinstall.
}

?>
