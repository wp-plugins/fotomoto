<?php
$user_object = get_userdata($_GET["user_id"]);
$domain = fotomoto_user_domain($user_object->user_login);
?>
<div class="wrap">
<h2>Fotomoto - Retrieve Site Key</h2>
<?php if ($message != "") { ?>
<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><strong><?php echo $message ?></strong></p></div>

<?php 
  $message = "";
} 
?>
<form method="post" id="fotomoto_form">
<?php wp_nonce_field('save-user-key'); ?>

<table class="form-table">

<tr valign="top">
<th scope="row">Email:</th>
<td><input type="text" name="email" value="<?php echo $user_object->user_email ?>" size="48"/></td>
</tr>

<tr valign="top">
<th scope="row">First Name:</th>
<td><input type="text" name="first_name" value="<?php echo $user_object->first_name ?>" size="48"/></td>
</tr>

<tr valign="top">
<th scope="row">Last Name:</th>
<td><input type="text" name="last_name" value="<?php echo $user_object->last_name ?>" size="48"/></td>
</tr>

<tr valign="top" style="display:none">
<th scope="row" style="display:none">Domain:</th>
<td style="display:none"><input type="text" name="domain" value="<?php echo $domain ?>" size="48"/></td>
</tr>

</table>

<input type="hidden" name="action" value="save-user-key" />
<input type="hidden" name="user_id" value="<?php echo $_GET["user_id"] ?>" />
<input type="hidden" name="act" value="edit" />
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Get Site Key') ?>" />
<input type="button" class="button-secondary" value="<?php _e('Go Back') ?>" onclick="window.location='<?php echo WP_FOTOMOTO_PLUGIN_ADMIN_URL ?>#users_div'" />
</p>
</form>
</div>