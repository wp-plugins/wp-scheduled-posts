<?php
/*
 * Plugin Name: WP Scheduled Posts
 * Plugin URI: http://wpdeveloper.net/free-plugin/wp-scheduled-posts/
 * Description: A complete solution for WordPress Scheduled Post. Get an admin Bar & Dashboard Widget showing all your scheduled post.
 * Version: 1.0.1
 * Author: WPDeveloper.net
 * Author URI: http://wpdeveloper.net
 * License: GPL2+
 * Text Domain: wp-scheduled-posts
 * Min WP Version: 2.5.0
 * Max WP Version: 3.9.0
 */


define("WPSCP_PLUGIN_SLUG",'wp-scheduled-posts');
define("WPSCP_PLUGIN_URL",plugins_url("",__FILE__ ));#without trailing slash (/)
define("WPSCP_PLUGIN_PATH",plugin_dir_path(__FILE__)); #with trailing slash (/)

include_once('includes/wpscp-options.php');
include_once('includes/wpdev-dashboard-widget.php');

function add_wpscp_menu_pages()

{
add_options_page( "WP Scheduled Posts", "WP Scheduled Posts" ,'manage_options', WPSCP_PLUGIN_SLUG, 'wpscp_options_page');
}

add_action('admin_menu', 'add_wpscp_menu_pages'); 

	
	function wp_scheduled_post_widget_function() {
		global $wpdb;
		$wpscp_options=wpscp_get_options();
		$post_types=implode("', '",$wpscp_options['allow_post_types']); $post_types="'".$post_types."'";
		$result=$wpdb->get_results("select * from ".$wpdb->prefix."posts where post_status = 'future' AND post_type IN(".$post_types.") ORDER BY post_date ASC ");

		echo '<table class="widefat">';
		foreach($result as $scpost)
		{
		echo '<tr><td><a href="'.get_edit_post_link($scpost->ID).'">'.$scpost->post_title.'</a></td><td>'.get_date_from_gmt($scpost->post_date_gmt, $format = 'Y-m-d H:i:s').'</td><td>'.get_the_author_meta( 'user_login', $scpost->post_author ).'</td></tr>';
		
		}
		echo "</table>";
		
	} # END OF wp_scheduled_post_widget_function()

// Create the function use in the action hook

function wp_scp_add_dashboard_widgets()
	{
	global $current_user;
	$wpscp_options=wpscp_get_options();
	if($wpscp_options['show_dashboard_widget'])
		{
			if(wpscp_permit_user())
			{
			wp_add_dashboard_widget('wp_scp_dashboard_widget', 'WP Scheduled Posts', 'wp_scheduled_post_widget_function');	
			}
		}
	} 
// Hook into the 'wp_dashboard_setup' action to register our other functions
add_action('wp_dashboard_setup', 'wp_scp_add_dashboard_widgets');

#-----------ADMINBAR---------------------
add_action( 'admin_bar_menu', 'wp_scheduled_post_menu', 1000 );


  function wp_scheduled_post_menu() {
  	  global $wp_admin_bar;
	  $wpscp_options=wpscp_get_options();
	  if($wpscp_options['show_in_adminbar'] || $wpscp_options['show_in_front_end_adminbar'])
	  {
		  if(is_admin() && !$wpscp_options['show_in_adminbar']) return;
		  if(!is_admin() && !$wpscp_options['show_in_front_end_adminbar']) return;
		  
		  if(wpscp_permit_user())
		  {
				
				
				global $wpdb;
				$post_types=implode("', '",$wpscp_options['allow_post_types']); $post_types="'".$post_types."'";
				$result=$wpdb->get_results("select * from ".$wpdb->prefix."posts where post_status = 'future' AND post_type IN(".$post_types.") ORDER BY post_date ASC ");
				$totalPost=0;
				if(is_array($result)){$totalPost=count($result);}
				$wp_admin_bar->add_menu( array( 'id' => 'wpscp', 'title' =>'Scheduled Posts ('.$totalPost.')') );
				
				if(is_array($result))
				{
					foreach($result as $scpost)
					{
					$wp_admin_bar->add_menu( array( 'parent' => 'wpscp' , 'title' =>substr($scpost->post_title, 0,45)."...&nbsp; - &nbsp By: ".get_the_author_meta( 'user_nicename', $scpost->post_author )."&nbsp For ".get_date_from_gmt($scpost->post_date_gmt, $format = 'h:i:a') , 'href' =>get_edit_post_link($scpost->ID),'meta'=>array('title'=>$scpost->post_title) ) );
					}
				}
		  }
	  }
  }

#-----------------------------SHOWING scheduled POSTS ON HOMEPAGE--------------------------------------
function wp_scheduled_posts()
{
	global $wpdb;
		$wpscp_options=wpscp_get_options();
		$post_types=implode("', '",$wpscp_options['allow_post_types']); $post_types="'".$post_types."'";
		$result=$wpdb->get_results("select * from ".$wpdb->prefix."posts where post_status = 'future' AND post_type IN(".$post_types.") ORDER BY post_date ASC ");

	if(is_array($result))
	{
		echo '<div class="scheduled_posts_box">';
		foreach($result as $scpost)
		{
			echo '<div class="scheduled_post"><div>'.get_date_from_gmt($scpost->post_date_gmt, $format = 'Y-m-d H:i:s')." | ".$scpost->post_title.'</div></div>';
			//echo $scpost->post_title;
		}
		echo '</div>';
	}
	
}#end wp_scheduled_posts()
?>