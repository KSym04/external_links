<?php
/**
 * External Links for Osclass Admin Panel
 *
 * @since 1.0.1
 */
?>
<h2 class="render-title"><?php _e( 'Insert on Header & Footer', 'external_links' ); ?></h2>
<form id="external_links-form" action="<?php echo osc_admin_render_plugin_url( 'external_links/admin.php' ); ?>" method="post">
    <input type="hidden" name="option" value="settings_saved" />
    <fieldset>
        <div class="form-horizontal">
            <div class="form-row">
                <div class="form-label">
                    <?php _e( 'Header Code', 'external_links' ); ?>
                </div>
                <div class="form-controls">
                    <textarea name="header_code" class="iohf-textarea"><?php echo osc_get_preference( 'header_code', 'plugin-external_links' ); ?></textarea>
                    <p class="iohf-helpinfo"><?php _e( 'This code/script will appear inside head tag', 'external_links' ); ?></p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label">
                    <?php _e( 'Footer Code', 'external_links' ); ?>
                </div>
                <div class="form-controls">
                    <textarea name="footer_code" class="iohf-textarea"><?php echo osc_get_preference( 'footer_code', 'plugin-external_links' ); ?></textarea>
                    <p class="iohf-helpinfo"><?php _e( 'This code/script will appear before closing body tag', 'external_links' ); ?></p>
                </div>
            </div>
            <div class="form-actions">
                <input type="submit" value="<?php _e( 'Save', 'external_links' ); ?>" class="btn btn-submit">
            </div>
        </div><!-- form-horizontal -->
    </fieldset>
</form>
