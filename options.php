<div class="wrap">
<h2>Fotomoto</h2>
<?php if ($message != "") { ?>
<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><strong><?= $message ?></strong></p></div>
<? } ?>
<form method="post" id="fotomoto_form">
<?php wp_nonce_field('update-options'); ?>
<?php settings_fields('fotomoto'); ?>

<table class="form-table">

<tr valign="top">
<th scope="row">Please enter your site key:</th>
<td><input type="text" name="store_key" value="<?php echo fotomoto_get_option('store_key'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Do not use Fotomoto with:<br/><small>List of paths to exclude the Fotomoto script, separated by comma. For example if your blog is at www.xyz.com/blog/, and you want to exclude Fotomoto on the home page and a certain category, enter something like: /,/?cat=3</small></th>
<td><input type="text" name="exclude_url" value="<?= implode(",", fotomoto_get_option("exclusive_list")) ?>" /></td>
</tr>

</table>
<input type="hidden" id="delete_url_id" name="delete_url_id" value="" />
<input type="hidden" name="action" value="update" />
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
</div>
<script type="text/javascript">
//<![CDATA[
function deleteURL(id) {
	if (confirm("Are you sure to delete it?")) {
		jQuery("#delete_url_id").val(id);
		jQuery("#fotomoto_form").submit();
	}
}//]]>
</script>