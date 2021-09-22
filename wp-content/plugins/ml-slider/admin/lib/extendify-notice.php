<?php

$extendifysdk_ms_notices_key = 'extendifysdk_announcement';
$extendifysdk_ms_notices_nonce = wp_create_nonce($extendifysdk_ms_notices_key);

add_action('admin_notices', function () use ($extendifysdk_ms_notices_key, $extendifysdk_ms_notices_nonce) {
    $current_page = get_current_screen();
    if (!$current_page || !in_array($current_page->base, ['plugins'])) {
        return;
    }
    ob_start(); ?>
<p>
    <?php esc_html_e('New! MetaSlider now includes a library of Gutenberg patterns and templates. These will help you to easily add your slideshows to your site with pre-designed patterns. This is available in the editor by clicking the \'Library\' button.', 'ml-slider') ?>
</p>
<p style="max-width:850px;"><small>
    <?php
    // translators: %s surrounding the word 'here' and is wrapped with <a>.
    printf(esc_html__('Learn more at %sextendify.com%s. ', 'ml-slider'), '<a target="_blank" href="https://extendify.com?utm_source=metaslider&utm_medium=notice&utm_campaign=library_intro">', '</a>');
    // translators: %s surrounding the word 'here' and is wrapped with <a>.
    printf(esc_html__('If you\'d prefer not to have this button display in the editor, %ssee here%s.', 'ml-slider'), '<a target="_blank" href="https://extendify.com/how-to-disable-the-extendify-library?utm_source=metaslider&utm_medium=notice&utm_campaign=library_intro">', '</a>'); ?>
</small></p>
<?php
    $extendifysdk_ms_notices_content = ob_get_clean();

    // In short, the notice will always show until they press dismiss
    if (!get_user_option($extendifysdk_ms_notices_key)) { ?>
<div id="<?php echo $extendifysdk_ms_notices_key; ?>" class="notice notice-info"
    style="display:flex;align-items:stretch;justify-content:space-between;position:relative">
    <div style="display:flex;align-items:center;position:relative">
        <div style="margin-right:1.5rem;">
            <svg width="60" height="60" viewBox="0 0 103 103" fill="none" xmlns="http://www.w3.org/2000/svg">
                <title>Extendify Logo</title>
                <rect y="25.75" width="70.8125" height="77.25" fill="black" />
                <rect x="45.0625" width="57.9375" height="57.9375" fill="#37C2A2" />
            </svg>
        </div>
        <div>
            <h3 style="margin-bottom:0.25rem;">
                <?php esc_html_e('New from MetaSlider', 'ml-slider'); ?></h3>
            <div style="max-width:850px;">
                <?php echo $extendifysdk_ms_notices_content; ?>
            </div>
        </div>
    </div>
    <div style="margin:5px -5px 0 0;">
        <button
            style="max-width:15px;border:0;background:0;color: #7b7b7b;white-space:nowrap;cursor: pointer;padding: 0"
            title="<?php esc_attr_e('Dismiss notice', 'ml-slider') ?>"
            aria-label="<?php esc_attr_e('Dismiss Extendify notice', 'ml-slider') ?>"
            onclick="jQuery('#<?php echo $extendifysdk_ms_notices_key; ?>').remove();jQuery.post(window.ajaxurl, {action: 'handle_<?php echo $extendifysdk_ms_notices_key; ?>', _wpnonce: '<?php echo $extendifysdk_ms_notices_nonce ?>' });">
            <svg width="15" height="15" style="width:100%" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
<?php }
});

add_action('wp_ajax_handle_' . $extendifysdk_ms_notices_key, function () use ($extendifysdk_ms_notices_key) {
    if (!wp_verify_nonce($_REQUEST['_wpnonce'], $extendifysdk_ms_notices_key)) {
        wp_send_json_error([
            'message' => esc_html__('The security check failed. Please refresh the page and try again.', 'ml-slider')
        ], 401);
    }
    update_user_option(get_current_user_id(), $extendifysdk_ms_notices_key, time());
    wp_send_json_success();
});
