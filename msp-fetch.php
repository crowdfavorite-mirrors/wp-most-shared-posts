<?php
/*

Most Shared Posts plugin

This file contains the functions that update the social counts from APIs.

*/

// This function collects the social data.
function most_shared_posts_update_social_data_if_necessary()
{
	// if we recently checked our caches, then do not bother
	if (is_numeric(get_transient("msp_recently_checked_counts")))
	{
		// All is well with the world. Tum te tum.
		
		//echo "<!-- No Check Performed due to recency. -->";
	
	}else{ // Otherwise, check the post caches and update...
		most_shared_posts_update_social_data();
	}
}

// This function collects the social data.
function most_shared_posts_update_social_data()
{
	
	// This is our standard set of arguments for our query.
	// TODO (for v 1.2.0): update his naive 500 limit
	$args = array(
		'posts_per_page' => 500,
		'orderby' => 'date',
		'order' => 'DESC',
		'ignore_sticky_posts' => 1
	);
	
	// last 2 days - every 1 hour
	// last week - every 6 hours
	// last month - every 12 hours
	// last 6 months - every 48 hours
	// older - every week
	
	// This specifies how long to cache posts of different
	// ages, by associating a specific filter function for
	// an age range with a cache length (in secs).
	//
	// We cache older posts for much longer, on the assumption
	// they won't be getting shared so often.
	$filter_functions_and_cache_times = array(
		'last_2_days' => (60*60),
		'from_2_to_7_days' => (60*60*6),
		'from_7_to_30_days' => (60*60*12),
		'from_30_to_180_days' => (60*60*48),
		'older_than_180' => (60*60*24*7)
	);

	//echo "<!-- Ok beginning checks. -->";
	
	// First, set this so we don't check again too soon.
	set_transient("msp_recently_checked_counts", 99, 60*10); // don't check again for 10 minutes.
	
	// This variable will count how many API calls
	// we have made this time around. We'll use it
	// to prevent too many in one go, so a single
	// user isn't slowed up too much.
	$api_hits_counter = 0;
	
	// Loop over te various date ranges fetching posts and checking if we need
	// to update our caches for each.
	
	foreach ($filter_functions_and_cache_times as $date_range_filter_function=>$cache_time)
	{
		//echo "<!-- Next Filter. -->";
		
		// Set the date range_filter
		add_filter( 'posts_where', $date_range_filter_function );

		$posts_in_range = new WP_Query( $args );
		
		while ( $posts_in_range->have_posts() ) : $posts_in_range->the_post();
			
	
			//echo "<!-- Next Post. -->";
		
			$transient_base = "msp_trans_" . get_the_ID() . "_";
			
			// Now we check if we have cached results for each of the 3 social counts
			// and update if necessary. Currently, they are all cached for the same time
			// but I check them each separately in case in future I cached for different
			// time periods.
			
			// We gather the data for each, even if in the options they are set not
			// to be included. This is such that users can change settings in an
			// instant and it'll not have to queue to fetch that data.
			
			// We keep a cached recent copy using transients, which also serves
			// as how we detect if we checked recently. We also keep a longer term
			// cached copy using meta data. These are prefixed with an underscore
			// which makes them hidden in the Wordpress admin interface when people
			// are editing posts.
		
			// ============
			// FACEBOOK
			// ============
	
			// If have a cached Facebook Likes count for this post...
			if (is_numeric($fb_likes = get_transient($transient_base."_fb_likes")))
			{
				// ... then great. We can sit back and relax. :)
				$post_likes = $fb_likes;
				
				//echo "<!-- FB Likes fetched with Transient = " . $post_likes . " -->";
				
			}else{
				
				//echo "<!-- Fetching FB Likes from API -->";
				
				// ... if not, then lets check the Facebook API.
				$api_hits_counter++;
				$facebook_api_results = file_get_contents("http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=" . urlencode(get_permalink()));
				$parsed_facebook_api_results = json_decode($facebook_api_results, true);
				
				// The FB like button now shows the total shares count for FB,
				// so we do too! :)
				//$post_likes = $parsed_facebook_api_results[0]['like_count'];
				$post_likes = $parsed_facebook_api_results[0]['total_count'];
				
				if (is_numeric($post_likes)) // We got a valid response from the API.
				{
					// Now cache the result...
					
					// The short version:
					// Includes a randomiser to offset the odds of one
					// user having to request multiple posts.
					$this_cache_time = $cache_time + rand(60*1, 60*25);
					set_transient($transient_base."_fb_likes", $post_likes, $this_cache_time);
					
					// The long version
					update_post_meta(get_the_ID(), "_msp_fb_likes", $post_likes);
					
				
					//echo "<!-- FB Likes fetched with API = " . $post_likes . " -->";
					
				}else{ // Looks like we failed to get a valid response
				
					// Fetch the cached version
					// if we don't have a cached version either, then
					// we'll just end up with 0 which is fine. :)
					
					$post_likes = intval(get_post_meta(get_the_ID(), "_msp_fb_likes", true));
				
					//echo "<!-- FB Likes API failed. Cached = " . $post_likes . " -->";
					
					// Now we cache it to transient, but always for 6 hours, until
					// hopefully the API comes back.
					
					set_transient($transient_base."_fb_likes", $post_likes, 60*60*6);
				}
				
			}
		
			// ============
			// TWITTER
			// ============
	
			// If have a cached Twitter Tweets count for this post...
			if (is_numeric($tweets = get_transient($transient_base."_tweets")))
			{
				// ... then great. We can sit back and relax. :)
				$post_tweets = $tweets;
				
				//echo "<!-- Tweets fetched with Transient = " . $post_tweets . " -->";
			}else{
				//echo "<!-- Fetching Tweets from API -->";
				
				// ... if not, then lets check the Twitter API.
				$api_hits_counter++;
				$twitter_api_results = file_get_contents("http://urls.api.twitter.com/1/urls/count.json?url=" . urlencode(get_permalink()));
				
				$parsed_twitter_api_results = json_decode($twitter_api_results, true);
				$post_tweets = $parsed_twitter_api_results['count'];
				
				if (is_numeric($post_tweets)) // We got a valid response from the API.
				{
					// Now cache the result...
					
					// The short version:
					// Includes a randomiser to offset the odds of one
					// user having to request multiple posts.
					$this_cache_time = $cache_time + rand(60*1, 60*25);
					set_transient($transient_base."_tweets", $post_tweets, $this_cache_time);
					
					// The long version
					update_post_meta(get_the_ID(), "_msp_tweets", $post_tweets);
					
				
					//echo "<!-- Tweets fetched with API = " . $post_tweets . " -->";
					
				}else{ // Looks like we failed to get a valid response
				
					// Fetch the cached version
					// if we don't have a cached version either, then
					// we'll just end up with 0 which is fine. :)
					$post_tweets = intval(get_post_meta(get_the_ID(), "_msp_tweets", true));
				
					//echo "<!-- Tweets API failed. Cached = " . $post_tweets . " -->";
					
					// Now we cache it to transient, but always for 6 hours, until
					// hopefully the API comes back.
					
					set_transient($transient_base."_tweets", $post_tweets, 60*60*6);
				}
			}
		
			// ============
			// GOOGLE
			// ============
			
			// If have a cached Google +1 count for this post...
			if (is_numeric($plusones = get_transient($transient_base."_google_plus_ones")))
			{
				// ... then great. We can sit back and relax. :)
				$post_plus_ones = $plusones;
				
				//echo "<!-- Google +s fetched with Transient = " . $post_plus_ones . " -->";
			}else{
				
				//echo "<!-- Fetching Google +s from API -->";
				
				// ... if not, then lets check the Google API.
				
				$api_hits_counter++;
				
				// This API is a bit more complicated as we have to send a JSON request
				// using POST. Details: http://www.tomanthony.co.uk/blog/google_plus_one_button_seo_count_api/
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_URL, "https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . get_permalink() . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
				$curl_results = curl_exec ($ch);
				curl_close ($ch);

				$parsed_results = json_decode($curl_results, true);

				$post_plus_ones = $parsed_results[0]['result']['metadata']['globalCounts']['count'];

				if (is_numeric($post_plus_ones)) // We got a valid response from the API.
				{
					// Now cache the result...
					
					// The short version:
					// Includes a randomiser to offset the odds of one
					// user having to request multiple posts.
					$this_cache_time = $cache_time + rand(60*1, 60*25);
					set_transient($transient_base."_google_plus_ones", $post_plus_ones, $this_cache_time);
					
					// The long version
					update_post_meta(get_the_ID(), "_msp_google_plus_ones", $post_plus_ones);
					
				
					//echo "<!-- Google +s fetched with API = " . $post_plus_ones . " -->";
					
				}else{ // Looks like we failed to get a valid response
				
					// Fetch the cached version
					// if we don't have a cached version either, then
					// we'll just end up with 0 which is fine. :)
					$post_plus_ones = intval(get_post_meta(get_the_ID(), "_msp_google_plus_ones", true));
				
					//echo "<!-- Google +s API failed. Cached = " . $post_plus_ones . " -->";

					// Now we cache it to transient, but always for 6 hours, until
					// hopefully the API comes back.

					set_transient($transient_base."_google_plus_ones", $post_plus_ones, 60*60*6);
				}
			}
			
			// ============
			// TOTALS
			// ============
			
			
			// Add the 3 networks values to get a total
			// Check the options of which to include
			
			$post_totals = 0;
			
			if (get_option('toma_msp_include_fb') == 'on')
				$post_totals += intval($post_likes);
			
			if (get_option('toma_msp_include_twitter') == 'on')
				$post_totals += intval($post_tweets);
			
			if (get_option('toma_msp_include_google') == 'on')
				$post_totals += intval($post_plus_ones);
			
			
				
			//echo "<!-- Totals calculated as = " . $post_totals . " -->";
					
			// This will overwrite the previous value
			update_post_meta(get_the_ID(), "_msp_total_shares", $post_totals);
			
			// If we have done more than 15 API calls then
			// stop checking for this user and move on to
			// displaying.
			// Don't be tempted to 'break 2' because
			// we need to make sure we do the post
			// reset and remove_filter at the bottom of
			// the next loop out
			if ($api_hits_counter > 14)
				break;
	
		endwhile;

		wp_reset_postdata();
		
		// Remove the date range filter.
		remove_filter('posts_where', $date_range_filter_function);
		
		// If we have done more than 15 API calls then
		// stop checking for this user and move on to
		// displaying.
		if ($api_hits_counter > 14)
			break;
	}
}

?>