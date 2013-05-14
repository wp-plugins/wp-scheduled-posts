<?php 

function wpscp_get_options()
{
	$options=array(
			'show_dashboard_widget'=>1, 
			'show_in_front_end_adminbar'=>1, 
			'show_in_adminbar'=>1,
			'allow_user_role'=>array('administrator'),
			'allow_post_types'=>array('post'),
			'prevent_future_post'=>0  
	
	);
return get_option('wpscp_options',$options);
}

function wpscp_permit_user()
{
global $current_user;
$wpscp_options=wpscp_get_options();

if(!is_array($current_user->roles)) return false;
if(!is_array($wpscp_options['allow_user_role']))$wpscp_options['allow_user_role']=array('administrator');

	foreach($current_user->roles as $ur)
	{
		if(in_array($ur, $wpscp_options['allow_user_role'])) {return true; break;}
	}

return false;
}

function wpscp_dropdown_roles( $selected = array() ) { #modified function from function wp_dropdown_roles( $selected = false ) in wp-admin/include/template.php
	$p = '';
	$r = '';
	$editable_roles = get_editable_roles();
	foreach ( $editable_roles as $role => $details ) {
		$name = translate_user_role($details['name'] ); 
		if ( in_array(esc_attr($role),$selected) ) // preselect specified role
			{
			$p .= "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
			}
		else
			$r .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
	}
	echo $p . $r;

}


function wpscp_options_page()
{
	global $wpdb;
	$wpscp_options=wpscp_get_options();
	
	if(isset($_POST['save_options']))
	{
		$options=array(
				'show_dashboard_widget'=>intval($_POST['show_dashboard_widget']), 
				'show_in_front_end_adminbar'=>intval($_POST['show_in_front_end_adminbar']), 
				'show_in_adminbar'=>intval($_POST['show_in_adminbar']),
				'allow_user_role'=>$_POST['allow_user_role'],
				'allow_post_types'=>$_POST['allow_post_types'],
				'prevent_future_post'=>$_POST['prevent_future_post'] 
				
		);	
	update_option('wpscp_options',$options);
	$wpscp_options=$options;
	}#end if(isset($_POST['save_options']))
	
	
	echo "<div style=\"width: 1010px; padding-left: 10px;\" class=\"wrap\">";
		echo "<div style=\"width: 700px; float:left;\">";
		echo '<div id="icon-options-general" class="icon32"></div>';
		echo "<h2>WP Scheduled Posts Options</h2>";
		global $current_user;
		?>
			<form action="" method="post">
            <table class="form-table">
            <tr><td  colspan="2" align="left"><input type="checkbox" name="show_dashboard_widget" value="1" <?php echo ($wpscp_options['show_dashboard_widget'])?' checked="checked"': '';?> />&nbsp;&nbsp;Show Scheduled Posts in Dashboard Widget</td></tr>
            <tr><td  colspan="2" align="left"><input type="checkbox" name="show_in_front_end_adminbar" value="1" <?php echo ($wpscp_options['show_in_front_end_adminbar'])?' checked="checked"': '';?>/>&nbsp; &nbsp;Show Scheduled Posts in Sitewide Admin Bar</td></tr>
            <tr><td  colspan="2" align="left"><input type="checkbox" name="show_in_adminbar" value="1" <?php echo ($wpscp_options['show_in_adminbar'])?' checked="checked"': '';?>/>&nbsp;&nbsp;Show Scheduled Posts in Admin Bar</td></tr>

			<tr>
            <td scope="row" align="left" style="vertical-align:top;">Show Post Types: </td>
            <td>
            <select name="allow_post_types[]" MULTIPLE style="height:70px;width:150px;">
			<?php
			$typeswehave = array('post,revision'); //oneTarek
			$post_types=get_post_types('','names'); 
			$rempost = array('attachment','revision','nav_menu_item');
			$post_types = array_diff($post_types,$rempost);
			foreach ($post_types as $post_type ) {
				echo "<option ";
				
				if(in_array($post_type,$wpscp_options['allow_post_types'])) echo "selected ";
				echo 'value="'.$post_type.'">'.$post_type.'</option>';
			}
			
			?>
			</select>
            </td>
            </tr>
            
            <tr valign="top">
            <td width="100" scope="row" align="align="left""><label for="allow_user_role">Allow users:</label></td>
            <td>
            <select name="allow_user_role[]" id="allow_user_role" multiple="multiple"  style="height:100px;width:150px;" ><?php  wpscp_dropdown_roles( $wpscp_options['allow_user_role'] ); ?></select>
            </td>
            </tr>
            
            <tr><td  colspan="2" align="left"><input type="checkbox" name="prevent_future_post" value="1" <?php echo ($wpscp_options['prevent_future_post'])?' checked="checked"': '';?> />&nbsp;&nbsp;Show Option to Publish Post Immediately but with Future Date <span style="color:#666666"> (Two option buttons will be appeared above the publish button in the post edit panel)</span> </td></tr>  
                        
            <tr><td><input type="submit" name="save_options" value="Save Options" class='button-primary'/></td><td>&nbsp;</td></tr>
            </table>
            </form>
            
            <div style=" text-align:center; margin-top:60px;"><a target="_blank" href="http://wpdeveloper.net"><img src="<?php echo WPSCP_PLUGIN_URL."/includes/wpdevlogo.png" ?>" /></a></div>
<?php
		
		echo "</div>";
	
		include_once(WPSCP_PLUGIN_PATH."includes/wpscp-sidebar.php");
		echo '<div style="clear:both"></div>';
	echo "</div>";

}

?>