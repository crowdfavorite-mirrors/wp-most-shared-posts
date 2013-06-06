<?php
/*

Most Shared Posts plugin

This file contains the code for the widget. It is required by both the admin side and the front end.

*/

// Add our hook on init to load the widget
add_action( 'widgets_init', 'load_most_shared_posts_widget' );

// Standard register widget function
function load_most_shared_posts_widget() {
	register_widget( 'Most_Shared_Posts' );
}


/**
 * Most_Shared_Posts class.
 *
 */
class Most_Shared_Posts extends WP_Widget {

	private $recency_limit; 

	/**
	 * Widget setup.
	 */
	function Most_Shared_Posts() {
		global $wp_version;
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'most-shared-posts', 'description' => 'Showcases your most shared posts to your visitors in your blog\'s sidebar.' );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'toma_msp' );

		/* Create the widget. */
		$this->WP_Widget( 'toma_msp', 'Most Shared Posts', $widget_ops, $control_ops );
		
		if (version_compare($wp_version,"2.8","<"))
		{
			exit ("Most Shared Posts requires Wordpress version 2.8 or later. Please update Wordpress. :)");
		}
	}
		
	function within_recency_limit( $where = '' ) {
		
		$where .= " AND post_date >= '" . date('Y-m-d', strtotime('-' . $this->recency_limit .' days')) . "'";
		
		return $where;
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
				
		// Setup our recency limit for this widget instance
		$recency_limit_unit = isset( $instance['recency_limit_unit'] ) ? $instance['recency_limit_unit'] : 2;
		
		$this->recency_limit = 730;
		
		switch($recency_limit_unit)
		{
			case "days":
				$this->recency_limit = intval($instance['recency_limit_number']);
				break;
			case "months":
				$this->recency_limit = intval($instance['recency_limit_number']) * 31;
				break;
			case "years":
				$this->recency_limit = intval($instance['recency_limit_number']) * 365;
				break;
			default:
				$this->recency_limit = 730;
				break;
		}
		
		// If something went wrong then set 2 years as a fallback.
		if ($this->recency_limit <= 0)
			$this->recency_limit = 730;
		
		// Read options on which networks to include
					
		$include_fb_count = (get_option('toma_msp_include_fb') == 'on') ? true : false;
		$include_twitter_count = (get_option('toma_msp_include_twitter') == 'on') ? true : false;
		$include_google_count = (get_option('toma_msp_include_google') == 'on') ? true : false;
		$suppress_icons = (get_option('toma_msp_suppress_icons') == 'on') ? true : false;
		$h3_wrap = (get_option('toma_msp_h3_wrap') == 'on') ? true : false;
		$attribution_link = (get_option('toma_msp_attribution_link') == 'on') ? true : false;
		
		
		
		// Read the option on font-size
		
		$css_class_font = '';
		
		switch(get_option('toma_msp_font_size'))
		{
			case "smaller":
				$css_class_font = "share-counts-smaller";
				break;
			case "standard":
				$css_class_font = "";
				break;
			case "bigger":
				$css_class_font = "share-counts-bigger";
				break;
			case "even-bigger":
				$css_class_font = "share-counts-even-bigger";
				break;
			case "huge":
				$css_class_font = "share-counts-huge";
				break;
			default:
				$css_class_font = "share-counts-bigger";
				break;
		}
		
		// Read the option on icon-size
		
		$icon_pixel_size = 16;
		
		switch(get_option('toma_msp_icon_size'))
		{
			case "smaller":
				$icon_pixel_size = 12;
				break;
			case "standard":
				$icon_pixel_size = 16;
				break;
			case "bigger":
				$icon_pixel_size = 20;
				break;
			case "huge":
				$icon_pixel_size = 25;
				break;
			default:
				$icon_pixel_size = 16;
				break;
		}
		
		
		// Read the option on how many posts to display.
		$number_of_posts = intval($instance['number_of_posts_to_list']);
		
		if ($number_of_posts <= 0)
			$number_of_posts = 5;
		
		// Setup and run the query for getting the list of posts to show
		$args = array(
			'posts_per_page' => $number_of_posts,
			'orderby' => 'meta_value_num',
		    'meta_key' => '_msp_total_shares',
			'order' => 'DESC'
		);
		
		// Add the filter here to get only those
		// within the setting of how far back
		// to check.
		add_filter( 'posts_where', array($this, 'within_recency_limit') );
		
		$posts_in_range = new WP_Query( $args );
		
		
		// Start the loop to loop over each post we are going to
		// list in the widget.
		
		
		//echo "<!-- Begin loop for showing. -->";
					
		echo '<ul class="entries">';
			
		while ( $posts_in_range->have_posts() ) : $posts_in_range->the_post();
			
			//echo "<!-- Next post -->";
		
			$fb_likes = get_post_meta(get_the_ID(), "_msp_fb_likes", true);
			$tweets = get_post_meta(get_the_ID(), "_msp_tweets", true);
			$plusones = get_post_meta(get_the_ID(), "_msp_google_plus_ones", true);
			$totals = get_post_meta(get_the_ID(), "_msp_total_shares", true);
			
			
			//echo "<!-- Likes fetched as = " . $fb_likes . " -->";
			//echo "<!-- Tweets fetched as = " . $tweets . " -->";
			//echo "<!-- PlusOnes fetched as = " . $plusones . " -->";
			//echo "<!-- Totals fetched as = " . $totals . " -->";
			
			echo '<li>';
	
			if ($h3_wrap)
				echo '<h3 class="post-title" >';
			
			echo '<a href="' . get_permalink() . '" rel="bookmark">' . get_the_title() . '</a>';
			
			if ($h3_wrap)
				echo '</h3>';
			
			//echo '<span class="date">' . get_the_date() . '</span>';
			
			if (!$suppress_icons)
			{
				echo '<div class="share-counts ' . $css_class_font . '">';
				
				
				
				if ($include_google_count)
				{
				echo '<img src="' . plugins_url('google_icon.png', __FILE__) . '" width="'.$icon_pixel_size.'px" height="'.$icon_pixel_size.'px" title="Google +1s" alt="Google +1 logo" />' . $plusones;
				
				echo " &nbsp; ";
				}
				
				
				if ($include_twitter_count)
				{
				echo '<img src="' . plugins_url('twitter_icon.png', __FILE__) . '" width="'.$icon_pixel_size.'px" height="'.$icon_pixel_size.'px" title="Tweets" alt="Twitter logo" />' . $tweets;
				
				echo " &nbsp; ";
				}
				
				if ($include_fb_count)
				{
				echo '<img src="' . plugins_url('facebook_icon.png', __FILE__) . '" width="'.$icon_pixel_size.'px" height="'.$icon_pixel_size.'px" title="Facebook shares" alt="Facebook logo" />' . $fb_likes;
				}
				
				//echo '<img src="' . plugins_url('shares_icon.png', __FILE__) . '" width="12px" height="12px" />' . $totals;
				
				
				echo '</div>';
			}
				
			echo '</li>';
		
		endwhile;
		// End loop over ther posts to show.
				
		echo '</ul>';
		
		if ($attribution_link)
		{
			echo "<small>Plugin by <a href='http://www.tomanthony.co.uk/wordpress-plugins/most-shared-posts/'>Tom Anthony</a></small>";
		}
		
		wp_reset_postdata();
		
		// Remove the date range filter.
		remove_filter('posts_where', array($this, 'within_recency_limit'));

		echo $after_widget;
	}

	// Update the settings for a particular instance
	// of the widget. This is separate to the global
	// settings that apply to all our widgets.
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['recency_limit_unit'] = strip_tags( $new_instance['recency_limit_unit'] );
		$instance['recency_limit_number'] = intval( $new_instance['recency_limit_number'] );
		$instance['number_of_posts_to_list'] = intval( $new_instance['number_of_posts_to_list'] );

		return $instance;
	}

	// Display the form with the options for an
	// instance of the widget.
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Most Shared Posts', 'recency_limit_unit' => 'years', 'recency_limit_number' => 2, 'number_of_posts_to_list' => 5);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
	
        <p>
        	<label for="<?php echo $this->get_field_id( 'recency_limit_number' ); ?>">Lists posts from the last:</label><br />
        	 
        	<input type="text" name="<?php echo $this->get_field_name( 'recency_limit_number' ); ?>" value="<?php echo $instance['recency_limit_number']; ?>" id="<?php echo $this->get_field_id( 'recency_limit_number' ); ?>" size="4" /> 

		    <select name="<?php echo $this->get_field_name( 'recency_limit_unit' ); ?>">
		                  <option value="days" <?php if ($instance['recency_limit_unit'] == 'days') { echo "selected=\"selected\""; } ?>>
		                    days
		                  </option>
		                  <option value="months" <?php if ($instance['recency_limit_unit'] == 'months') { echo "selected=\"selected\""; } ?>>
		                    months
		                  </option>
		                  <option value="years" <?php if ($instance['recency_limit_unit'] == 'years') { echo "selected=\"selected\""; } ?>>
		                    years
		                  </option>
	                </select>
	    </p>
	    
        <p>
        	<label for="<?php echo $this->get_field_id( 'number_of_posts_to_list' ); ?>">How many posts to list:</label><br />
        	 
        	<input type="text" name="<?php echo $this->get_field_name( 'number_of_posts_to_list' ); ?>" value="<?php echo $instance['number_of_posts_to_list']; ?>" size="4" />
	    </p>

	<?php
	}
}




?>