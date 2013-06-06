<?php
	if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__))
		exit("This is an include file for Most Shared Posts and doesn't do anything on it's own!");
		
		
	$settings_link = "options-general.php?page=most_shared_posts";
?>

<div class="wrap">
    <div id="icon-edit" class="icon32"></div>
    <h2>Most Shared Posts</h2>
    <p>Here you will see your top 100 most shared posts. For settings you should go to the <a href="<?=$settings_link?>">Settings Page</a>.</p>
	
	<p>
	<a class='button-secondary' href='?page=most-shared-posts-stats&post-age-limit=all' title='All Posts'>All Posts</a>
	<a class='button-secondary' href='?page=most-shared-posts-stats&post-age-limit=week' title='Last Week'>Last Week</a>
	<a class='button-secondary' href='?page=most-shared-posts-stats&post-age-limit=month' title='Last Month'>Last Month</a>
	<a class='button-secondary' href='?page=most-shared-posts-stats&post-age-limit=year' title='Last Year'>Last Year</a>
	</p>
<?php

	global $date_limit;
	$date_limit = 3650;

	switch ($_GET['post-age-limit'])
	{
		case "all":
			$date_limit = 3650;
			break;
		case "day":
			$date_limit = 1;
			break;
		case "week":
			$date_limit = 7;
			break;
		case "month":
			$date_limit = 31;
			break;
		case "year":
			$date_limit = 365;
			break;
	}

	function within_set_limit( $where = '' ) {
		global $date_limit;
		
		echo "<!-- monkey = " . $date_limit . "-->";
		
		$where .= " AND post_date >= '" . date('Y-m-d', strtotime('-' . $date_limit .' days')) . "'";
		
		return $where;
	}

	// Setup and run the query for getting the list of posts to show
	$args = array(
		'posts_per_page' => 100,
		'orderby' => 'meta_value_num',
	    'meta_key' => '_msp_total_shares',
		'order' => 'DESC'
	);
	
	// Add the filter here to get only those
	// within the setting of how far back
	// to check.
	add_filter( 'posts_where', 'within_set_limit' );
	
	$posts_in_range = new WP_Query( $args );
	
?>
	<table class="stats-table widefat" cellspacing="0">
		<thead>
		<tr>
			<th>Position</th>
			<th>ID</th>
			<th>Title</th>
			<th><img src="<?=plugins_url('google_icon.png', __FILE__)?>" width="20px" height="20px" title="Google +1s" /></th>
			<th><img src="<?=plugins_url('twitter_icon.png', __FILE__)?>" width="20px" height="20px" title="Tweets" /></th>
			<th><img src="<?=plugins_url('facebook_icon.png', __FILE__)?>" width="20px" height="20px" title="Facebooks Likes" /></th>
			<th>Totals</th>
		</tr>
		</thead>
		<tbody>
<?php
	
	$position = 1;
	
	$odd = false;
	
	while ( $posts_in_range->have_posts() ) : $posts_in_range->the_post();
		
		$fb_likes = get_post_meta(get_the_ID(), "_msp_fb_likes", true);
		$tweets = get_post_meta(get_the_ID(), "_msp_tweets", true);
		$plusones = get_post_meta(get_the_ID(), "_msp_google_plus_ones", true);
		$totals = get_post_meta(get_the_ID(), "_msp_total_shares", true);
		
		
		if ($odd)
		{
			$odd = false;
			$css_style = ' class="odd"';
		}else{
			$odd = true;
			$css_style = "";
		}
		
		echo '<tr'.$css_style.'>';
		
		echo '<td>' . $position . '</td>';
		
		echo '<td>' . get_the_ID() . '</td>';
		
		echo '<td class="titles"><a href="' . get_permalink() . '" rel="bookmark">' . get_the_title() . '</a></td>';

		echo '<td>' . $plusones . '</td>';
		
		echo '<td>' . $tweets . '</td>';
		
		echo '<td>' . $fb_likes . '</td>';
		
		echo '<td>' . $totals . '</td>';
			
		echo '</tr>';
		
		$position++;
	
	endwhile;
	
	echo '</tbody></table>';
?>
    
</div>