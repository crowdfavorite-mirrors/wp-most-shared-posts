<?php
/*
Plugin Name: Most Shared Posts
Plugin URI: http://www.tomanthony.co.uk/wordpress-plugins/most-shared-posts/
Description: Showcases your posts with the most social shares to your visitors in the sidebar. Please consider a small <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZS48RS294BLGN" target="_blank">donation</a>.
Version: 1.1.0
Author: Tom Anthony
Author URI: http://www.tomanthony.co.uk/

Copyright (C) 2011-2011, Tom Anthony
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
The name of Tom Anthony may not be used to endorse or promote products derived from this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/


// Both front and backend require the widget
require_once(plugin_dir_path(__FILE__) . 'msp-widget.php');

// Load either the admin code or the front end code
// depending on what we are currently viewing.

if ( is_admin() )
{

	require_once(plugin_dir_path(__FILE__) . 'msp-admin.php');
	
	// The admin hooks have to go here to prevent activation issues.
		
	// Add the admin menu for showing the options
	add_action('admin_menu', 'show_most_shared_posts_options');
	
	// This will show any messages we have for the user.
	add_action( 'admin_notices', 'most_shared_posts_activation_notice' );
	
	// Add a link to our settings page in the plugins list
	$plugin = plugin_basename(__FILE__);
	add_filter("plugin_action_links_$plugin", 'most_shared_posts_link' );
	
	// Register our de-activate function
	register_deactivation_hook(__FILE__, 'most_shared_posts_deactivate' );
	
	// Register our uninstall function
	register_uninstall_hook(__FILE__, 'most_shared_posts_deinstall');
	
	// Register hook for out stats menu
	add_action('admin_menu', 'most_shared_posts_stats');


}else{

	require_once(plugin_dir_path(__FILE__) . 'msp-front.php');

}



?>