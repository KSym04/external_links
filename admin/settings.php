<?php
/**
 * External Links for Osclass Admin Panel
 *
 * @since 1.0.1
 */

if( ! defined( 'ABS_PATH' ) ) {
	exit;
}
?>
<h2 class="render-title">
	<?php _e( 'External Links for Osclass', 'external_links' ); ?>
</h2>

<form id="external_links-form" action="<?php echo osc_admin_render_plugin_url( 'external_links/admin/settings.php' ); ?>" method="post">
    <input type="hidden" name="option" value="settings_saved" />
    <fieldset>
        <div class="grid-system">
			<div class="form-horizontal grid-row grid-50">
				<div class="form-row">
					<div class="form-label">
						<label for="new_window"><strong><?php _e( 'Open in New Windows', 'external_links' ); ?></strong></label>
					</div>
					<div class="form-controls">
						<input type="checkbox" name="new_window" id="new_window" <?php echo osc_get_preference( 'new_window', 'plugin-external_links' ) ? 'checked="true"' : ''; ?> name="new_window" value="1">
						<label for="new_window"><?php _e( 'Open outbound links in new windows', 'external_links' ); ?></label>
					</div>
				</div>
				<div class="form-row">
					<div class="form-label">
						<label for="add_no_follow"><strong><?php _e( 'Add No Follow', 'external_links' ); ?></strong></label>
					</div>
					<div class="form-controls">
						<input type="checkbox" name="add_no_follow" id="add_no_follow" <?php echo osc_get_preference( 'add_no_follow', 'plugin-external_links' ) ? 'checked="true"' : ''; ?> name="add_no_follow" value="1">
						<label for="add_no_follow"><?php _e( 'Add a rel="nofollow" attribute to outbound links.', 'external_links' ); ?></label>
					</div>
				</div>
				<div class="form-row">
					<div class="form-label">
						<label for="add_no_opener"><strong><?php _e( 'Add No Opener', 'external_links' ); ?></strong></label>
					</div>
					<div class="form-controls">
						<input type="checkbox" name="add_no_opener" id="add_no_opener" <?php echo osc_get_preference( 'add_no_opener', 'plugin-external_links' ) ? 'checked="true"' : ''; ?> name="add_no_opener" value="1">
						<label for="add_no_opener"><?php _e( 'Add a rel="noopener" attribute to outbound links.', 'external_links' ); ?></label>
					</div>
				</div>
				<div class="form-row">
					<div class="form-label">
						<label for="auto_convert_emails"><strong><?php _e( 'Auto Convert Emails', 'external_links' ); ?></strong></label>
					</div>
					<div class="form-controls">
						<input type="checkbox" name="auto_convert_emails" id="auto_convert_emails" <?php echo osc_get_preference( 'auto_convert_emails', 'plugin-external_links' ) ? 'checked="true"' : ''; ?> name="auto_convert_emails" value="1">
						<label for="auto_convert_emails"><?php _e( 'Automatically converts emails into mailto.', 'external_links' ); ?></label>
					</div>
				</div>
			</div>
			<div class="form-horizontal grid-row grid-50">
				<div class="form-well">
					<h3><?php _e( 'Frequently Asked Questions', 'external_links' ); ?></h3>
					<dl>
						<dt><?php _e( 'How to use this plugin?', 'external_links' ); ?></dt>
						<dd><?php _e( 'Wrap our helper function for outputting a string or any content using <code>external_links_make_clickable()</code> function, for an example look for <code>osc_item_description()</code> inside item.php of the theme and apply the function helpers: <code>external_links_make_clickable( osc_item_description() )</code>', 'external_links' ); ?></dd>
					</dl>
				</div>
			</div>
			<div class="form-horizontal grid-row grid-100">
				<div class="form-actions">
					<input type="submit" value="<?php _e( 'Save', 'external_links' ); ?>" class="btn btn-submit">
				</div>
			</div>
        </div><!-- form-horizontal -->
    </fieldset>
</form>
