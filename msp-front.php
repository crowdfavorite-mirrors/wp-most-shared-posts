<?php
/*
Most Shared Posts
*/

require_once('msp-fetch.php');
  
// === SETUP HOOKS ===

// Add a header hook so we can link to a CSS file
add_action('wp_head', 'most_shared_posts_head');

// Scheduled events don't seem very good in WordPress
// so we use transients and just checked with each visitor
// whether we are due an update.
add_action('wp_footer', 'most_shared_posts_update_social_data_if_necessary');

// Place hook for our [shortcode] so users can put the widget inside posts.
add_shortcode( 'most-shared-posts', 'most_shared_posts_shortcode_widget_placer' );

// Shortcode placement function
// [most-shared-posts num_posts="5" max_month_age="24" title="Most Shared Posts"]
function most_shared_posts_shortcode_widget_placer( $atts ) {
	extract( shortcode_atts( array(
		'title' => 'Most Shared Posts',
		'num_posts' => '5',
		'max_month_age' => '24',
	), $atts ) );
	
	$widget_instance = array( 'title' => $title, 'recency_limit_unit' => 'months', 'recency_limit_number' => $max_month_age, 'number_of_posts_to_list' => $num_posts);
	$args = array();
		
	the_widget('Most_Shared_Posts', $widget_instance, $args);
}




// Our header function which'll hook in our CSS file
function most_shared_posts_head() {
	echo '<link rel="stylesheet" type="text/css" href="' . plugins_url('most-shared-posts.css', __FILE__). '">';
}
	

//============
// Start Time filters
//============

function add_day_filter( $where = '', $from_x_days_back, $to_x_days_back )
{
	$days_into_past = $from_x_days_back;
	
	$where .= " AND post_date >= '" . date('Y-m-d', strtotime('-' . $to_x_days_back .' days')) . "'" . " AND post_date <= '" . date('Y-m-d', strtotime('-' . $days_into_past .' days')) . "'";
	
	return $where;
}

function last_2_days( $where = '' ) {
	$where .= " AND post_date > '" . date('Y-m-d', strtotime('-2 days')) . "'";
	return $where;
}

function from_2_to_7_days( $where = '' ) {
	$where .= " AND post_date >= '" . date('Y-m-d', strtotime('-7 days')) . "'" . " AND post_date <= '" . date('Y-m-d', strtotime('-2 days')) . "'";
	return $where;
}

function from_7_to_30_days( $where = '' ) {
	return add_day_filter($where, 8, 30);
}

function from_30_to_180_days( $where = '' ) {
	return add_day_filter($where, 31, 180);
}

function older_than_180( $where = '' ) {
	return add_day_filter($where, 181, 3600); // 10 year default
}

//============
// End Time filters
//============


// thumbnails: http://codex.wordpress.org/Function_Reference/has_post_thumbnail

?>
