<div class="wrap">
<h2>Fotomoto</h2>
<h4 style="margin-top:0.2em">Manage your Fotomoto settings for your Wordpress site</h4><br/>
<?php if ($message != "") { ?>
<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><strong><?= $message ?></strong></p></div>
<? 
	$message = "";
} 
?>
  <div id="fotomoto_tabs">
    <ul>
        <li><a href="#settings_div"><span>Settings</span></a></li>        
        <li><a href="#pages_div"><span>Pages</span></a></li>
        <li><a href="#categories_div"><span>Categories</span></a></li>
        <?php if (fotomoto_get_option('enable_multiuser') != "") { ?>
        <li><a href="#users_div"><span>Users</span></a></li>
        <?php } ?>
    </ul>
    <div id="settings_div">
      <h3> SETTINGS </h3>
      <h4>To enable the plugin, enter your Fotomoto Site key which is found by logging into your <a href="http://my.fotomoto.com" target="_blank">Dashboard</a>, and going to Site > Settings and clicking "Site Key"</h4>
      <form method="post" id="fotomoto_form" action="<?php echo WP_FOTOMOTO_PLUGIN_ADMIN_URL ?>" method="post">
      <?php wp_nonce_field('update-options'); ?>
      <?php settings_fields('fotomoto'); ?>
      
      <table class="form-table">      
      <tr valign="top">
      <th scope="row">Fotomoto Site Key:</th>
      <td><input type="text" name="store_key" value="<?php echo fotomoto_get_option('store_key'); ?>" size="48"/></td>
      </tr>
      </table>
      <br/><br/>
			<h4>If you are an Affiliate User and have a multiuser site to enable Fotomoto, please enter your Affiliate Information. if you can't find your keys, contact us via <a href="mailto:support@fotomoto.com">support@fotomoto.com</a>.</h4>
      <table class="form-table">      
      <tr valign="top">
      <th class="checkboxes" scope="row">
      <label for="enable_multiuser"><input type="checkbox" id="enable_multiuser" name="enable_multiuser" value="1" onchange="toggleMultiuserDetail(this)" <?php echo (fotomoto_get_option("enable_multiuser") != "" ? "checked" : "") ?>> <span>Enable Fotomoto Multiuser</span></label>
      </th>
      <td>&nbsp;</td>
      </tr>
      </table>
      <table id="multiuser_details" class="form-table" style="margin-left:40px;width:800px;display:<?php echo (fotomoto_get_option("enable_multiuser") != "" ? "" : "none") ?>">
      <tr valign="top">
      <th scope="row" style="width:250px;">Please enter your affiliate key:</th>
      <td><input type="text" name="affiliate_key" value="<?php echo fotomoto_get_option('affiliate_key'); ?>" size="48"/></td>
      </tr>
      
      <tr valign="top">
      <th scope="row">Please enter your API key:</th>
      <td><input type="text" name="api_key" value="<?php echo fotomoto_get_option('api_key'); ?>" size="48"/></td>
      </tr>
      
      <tr valign="top">
      <th scope="row">Automatically Set Default Pricing:</th>
      <td class="checkboxes"><label><input type="checkbox" id="use_default_pricing" name="use_default_pricing" value="1" <?php echo (fotomoto_get_option("use_default_pricing") != "" ? "checked" : "") ?>><span>&nbsp;</span></label></td>
      </tr>
      
      <tr valign="top">
      <th scope="row">Setup Auto Pickup:</th>
      <td class="checkboxes"><label><input type="checkbox" id="setup_auto_pickup" name="setup_auto_pickup" value="1" <?php echo (fotomoto_get_option("setup_auto_pickup") != "" ? "checked" : "") ?>><span>&nbsp;</span></label></td>
      </tr>
      
      <tr valign="top">
      <th scope="row">Automatically Active New Users:</th>
      <td class="checkboxes"><label><input type="checkbox" id="activate_new_user" name="activate_new_user" value="1" <?php echo (fotomoto_get_option("activate_new_user") != "" ? "checked" : "") ?>><span>&nbsp;</span></label></td>
      </tr>
      
      </table>
      
      <input type="hidden" name="action" value="update" />
      <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
      </form>
    </div>    
    <div id="pages_div">
      <h3>PAGES</h3>
      <h4>Enable and Disable the Fotomoto Buy Button(s) on entire Pages</h4>
      <form method="post" id="fotomoto_pages_form" action="<?php echo WP_FOTOMOTO_PLUGIN_ADMIN_URL ?>#pages_div" method="post">
      <?php wp_nonce_field('update-options'); ?>
      <table class="widefat fixed" cellspacing="0">
      <thead>
      <tr class="thead">
        <th style="" class="manage-column column-username" id="page_name" scope="col">PAGE</th>
        <th style="width:360px" class="manage-column column-name" id="status" scope="col">BUY BUTTON(S)</th>
      </tr>
      </thead>
      
      <tfoot>
      <tr class="thead">
        <th style="" class="manage-column column-username" id="page_name" scope="col">PAGE</th>
        <th style="width:360px" class="manage-column column-name" id="status" scope="col">BUY BUTTON(S)</th>
      </tr>
      </tfoot>
      
      <tbody id="pages" class="list:user user-list">
      <?php
      $style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';	
      $link = get_option("siteurl");
      $enable_button = "<label for='enable_page_home'><input type='radio' id='enable_page_home' name='page_status_home' onchange='changePageStatus(\"home\", \"".FOTOMOTO_ENABLED."\")' ".(fotomoto_page_enabled("home") ? "checked": "")." value='".FOTOMOTO_ENABLED."'> <span>Enable</span></label>";
      $disable_button = "<label for='disable_page_home'><input type='radio' id='disable_page_home' name='page_status_home' onchange='changePageStatus(\"home\", \"".FOTOMOTO_DISABLED."\")' ".(fotomoto_page_enabled("home") ? "" : "checked")." value='".FOTOMOTO_DISABLED."'> <span>Disable</span></label>";
      echo "\n\t", "<tr><td class='username column-username'><strong>Home</strong></td><td class='name column-name'>$enable_button&nbsp;&nbsp;&nbsp;$disable_button</td></tr>";
      
      $pages = get_pages();
      foreach ( $pages as $page ) {
        $style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';	
        $link = get_page_link($page->ID);
        $enable_button = "<label for='enable_page_{$page->ID}'><input type='radio' id='enable_page_{$page->ID}' name='page_status_{$page->ID}' onchange='changePageStatus({$page->ID}, \"".FOTOMOTO_ENABLED."\")' ".(fotomoto_page_enabled($page->ID) ? "checked": "")." value='".FOTOMOTO_ENABLED."'> <span>Enable</span></label>";
        $disable_button = "<label for='disable_page_{$page->ID}'><input type='radio' id='disable_page_{$page->ID}' name='page_status_{$page->ID}' onchange='changePageStatus({$page->ID}, \"".FOTOMOTO_DISABLED."\")' ".(fotomoto_page_enabled($page->ID) ? "" : "checked")." value='".FOTOMOTO_DISABLED."'> <span>Disable</span></label>";
        echo "\n\t", "<tr $style><td class='username column-username'><strong>$page->post_title</strong></td><td class='name column-name'>$enable_button&nbsp;&nbsp;&nbsp;$disable_button</td></tr>";
      }
      ?>
      </tbody>
      </table>
      <input type="hidden" name="action" value="update" />
      <input type="hidden" name="act" value="update_pages_status" />
      <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
      </form>
    </div>
    <div id="categories_div">
      <h3>CATEGORIES</h3>
      <h4>Enable and Disable the Fotomoto Buy Button(s) across entire Categories</h4>
      <form method="post" id="fotomoto_pages_form" action="<?php echo WP_FOTOMOTO_PLUGIN_ADMIN_URL ?>#categories_div" method="post">
      <?php wp_nonce_field('update-options'); ?>
      <table class="widefat fixed" cellspacing="0">
      <thead>
      <tr class="thead">
        <th style="" class="manage-column column-username" id="category_name" scope="col">CATEGORY</th>
        <th style="width:360px" class="manage-column column-name" id="category_status" scope="col">BUY BUTTON(S)</th>
      </tr>
      </thead>
      
      <tfoot>
      <tr class="thead">
        <th style="" class="manage-column column-username" id="category_name" scope="col">CATEGORY</th>
        <th style="width:360px" class="manage-column column-name" id="category_status" scope="col">BUY BUTTON(S)</th>
      </tr>
      </tfoot>
      
      <tbody id="categories" class="list:user user-list">
      <?php
      $categories = get_categories(array("hide_empty"=>0));
      foreach ( $categories as $category ) {
        $style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';	
        $link = get_category_link($category->term_id);
        $enable_button = "<label for='enable_category_{$category->cat_ID}'><input type='radio' id='enable_category_{$category->cat_ID}' name='category_status_{$category->cat_ID}' onchange='changeCategoryStatus({$category->cat_ID}, \"".FOTOMOTO_ENABLED."\")' ".(fotomoto_category_enabled($category->cat_ID) ? "checked": "")." value='".FOTOMOTO_ENABLED."'> <span>Enable</span></label>";
        $disable_button = "<label for='disable_category_{$category->cat_ID}'><input type='radio' id='disable_category_{$category->cat_ID}' name='category_status_{$category->cat_ID}' onchange='changeCategoryStatus({$category->cat_ID}, \"".FOTOMOTO_DISABLED."\")' ".(fotomoto_category_enabled($category->cat_ID) ? "" : "checked")." value='".FOTOMOTO_DISABLED."'> <span>Disable</span></label>";
        echo "\n\t", "<tr $style><td class='username column-username'><strong>$category->name</strong></td><td class='name column-name checkboxes'>$enable_button&nbsp;&nbsp;&nbsp;$disable_button</td></tr>";
      }
      ?>
      </tbody>
      </table>
      <input type="hidden" name="action" value="update" />
      <input type="hidden" name="act" value="update_categories_status" />
      <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
      </form>
    </div>
    <?php if (fotomoto_get_option('enable_multiuser') != "") { ?>
    <div id="users_div">
      <h3>USERS</h3>          
      <h4>Activate and Inactivate Fotomoto for your users</h4>
      <?php if (fotomoto_get_option('affiliate_key') == "") { ?>
      <p>Please provide your affiliate's information.</p>
      <?php } else { ?>
      <table class="widefat fixed" cellspacing="0">
      <thead>
      <tr class="thead">
        <th style="" class="manage-column column-username" id="username" scope="col">Username</th>
        <th style="" class="manage-column column-name" id="name" scope="col">Name</th>
        <th style="" class="manage-column column-email" id="email" scope="col">E-mail</th>
        <th style="width:360px" class="manage-column column-name" id="site_key" scope="col">Site Key</th>
      </tr>
      </thead>
      
      <tfoot>
      <tr class="thead">
        <th style="" class="manage-column column-username" id="username" scope="col">Username</th>
        <th style="" class="manage-column column-name" id="name" scope="col">Name</th>
        <th style="" class="manage-column column-email" id="email" scope="col">E-mail</th>
        <th style="" class="manage-column column-name" id="site_key" scope="col">Site Key</th>
      </tr>
      </tfoot>
      
      <tbody id="users" class="list:user user-list">
      <?php
      global $wp_roles;
      $wp_user_search = new WP_User_Search();
      $style = '';
      foreach ( $wp_user_search->get_results() as $userid ) {      	
        $user_object = new WP_User($userid);
        if ($user_object->user_level < 2) continue;
        
        $roles = $user_object->roles;
        $role = array_shift($roles);
      
        if ( is_multisite() && empty( $role ) )
          continue;
      
        $avatar = get_avatar( $user_object->ID, 32 );
        $email = $user_object->user_email;
        $role_name = isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : __('None');
        $style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
        $site_key = get_user_meta($user_object->ID, "fotomoto_site_key", true);
        if (!$site_key) {
        	$old_site_key = get_user_meta($user_object->ID, "old_fotomoto_site_key", true);
        	if (!$old_site_key) {
        		$site_key = "Not Activate [<a href='".WP_FOTOMOTO_PLUGIN_ADMIN_URL."&act=edit&user_id=$user_object->ID'>Activate</a>]";
        	}
        	else {
        		$site_key = "Not Activate [<a href='".WP_FOTOMOTO_PLUGIN_ADMIN_URL."&act=reactivate&user_id={$user_object->ID}#users_div'>Activate</a>]";
        	}
        }
        else $site_key .= "&nbsp;[<a href='".WP_FOTOMOTO_PLUGIN_ADMIN_URL."&act=delete&user_id=$user_object->ID#users_div'>Deactivate</a>]";
        echo "\n\t", "<tr $style><td class='username column-username'>$avatar <strong>$user_object->user_login</strong></td><td class='name column-name'>$user_object->first_name $user_object->last_name</td><td class='email column-email'><a href='mailto:$email' title='" . sprintf( __('E-mail: %s' ), $email ) . "'>$email</a></td><td class='name column-name'>$site_key</td></tr>";
      }
      ?>
      </tbody>
      </table>
      <form method="post" id="fotomoto_pages_form" action="<?php echo WP_FOTOMOTO_PLUGIN_ADMIN_URL ?>#users_div" method="post">
      <input type="hidden" name="action" value="update" />
      <input type="hidden" name="act" value="activate_all" />
      <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Activate All') ?>" onclick="return confirm('Are you sure you want to activate all users?');" />
      </p>
      </form>
      <?php } ?>
    </div>
    <?php } ?>
  </div>
</div>