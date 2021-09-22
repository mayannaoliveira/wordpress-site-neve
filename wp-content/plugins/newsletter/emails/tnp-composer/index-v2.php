<?php
/**
 * This file is included by NewsletterControls to create the composer.
 */
/* @var $this NewsletterControls */

defined('ABSPATH') || exit;

$list = NewsletterEmails::instance()->get_blocks();

$blocks = array();
foreach ($list as $key => $data) {
    if (!isset($blocks[$data['section']])) {
        $blocks[$data['section']] = array();
    }
    $blocks[$data['section']][$key]['name'] = $data['name'];
    $blocks[$data['section']][$key]['filename'] = $key;
    $blocks[$data['section']][$key]['icon'] = $data['icon'];
}

// order the sections
$blocks = array_merge(array_flip(array('header', 'content', 'footer')), $blocks);

// prepare the options for the default blocks
$block_options = get_option('newsletter_main');

$fields = new NewsletterFields($controls);

$dir = is_rtl() ? 'rtl' : 'ltr';
$rev_dir = is_rtl() ? 'ltr' : 'rlt';

?>
<script type="text/javascript">
    // collapse wp menu
    document.body.classList.add('folded');

    function tnp_view(type) {
        if (type === 'mobile') {
            jQuery('#newsletter-builder-area-center-frame-content').addClass('tnp-view-mobile');
        } else {
            jQuery('#newsletter-builder-area-center-frame-content').removeClass('tnp-view-mobile');
        }
    }
</script>


<style>
<?php echo NewsletterEmails::instance()->get_composer_css(); ?>
</style>
<div id="newsletter-builder" dir="ltr">

    <div id="newsletter-builder-area" class="tnp-builder-column">

	    <?php if ( $tnpc_show_subject ) { ?>
            <div id="tnpc-subject-wrap" dir="<?php echo $dir ?>">
                <table role="presentation" style="width: 100%">
                    <tr>
                        <th dir="<?php echo $dir ?>"><?php _e('From', 'newsletter') ?></th>
                        <td dir="<?php echo $dir ?>"><?php echo esc_html( $controls->data['sender_email'] ) ?></td>
                    </tr>
                    <tr>
                        <th dir="<?php echo $dir ?>"><?php _e('Subject', 'newsletter') ?></th>
                        <td dir="<?php echo $dir ?>">
                            <div id="tnpc-subject">
                                <?php $this->subject( 'subject' ); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th dir="<?php echo $dir ?>"><span title="<?php esc_attr_e('Shown by some email clients as excerpt', 'newsletter')?>"><?php _e('Snippet', 'newsletter') ?></span>
                        <?php $this->field_help('https://www.thenewsletterplugin.com/documentation/newsletters/composer/#subject') ?>
                        </th>
                        <td dir="<?php echo $dir ?>"><?php $this->text('preheader') ?></td>
                    </tr>
                </table>

                <div style="text-align: left; margin-left: 1em;">
                    <a href="https://www.thenewsletterplugin.com/documentation/newsletters/newsletter-tags/"
                       target="_blank">You can use tags to inject subscriber fields</a>. Even on subject.


                </div>

                <div class="composer-actions">
                    
                    <div id="attachment-newsletter-button" class="button-primary" data-tnp-modal-target="#attachment-modal">
                        <i class="fas fa-paperclip"></i>
                    </div>
                    
                    <div id="test-newsletter-button" class="button-primary" data-tnp-modal-target="#test-newsletter-modal">
                        <i class="fas fa-paper-plane"></i> <?php _e( 'Test', 'newsletter' ) ?>
                    </div>
                    
                    <div class="composer-view-mode">
                        <span class="composer-view-mode__item" data-view-mode="desktop">
                            <i class="fas fa-desktop"></i>
                        </span>
                        
                        <span class="composer-view-mode__item" data-view-mode="mobile">
                            <i class="fas fa-mobile"></i>
                        <span>
                    </div>
                    
                </div>

                <?php include NEWSLETTER_DIR . '/emails/tnp-composer/modal/test-newsletter.php' ?>
                <?php include NEWSLETTER_DIR . '/emails/tnp-composer/modal/attachment.php' ?>

            </div>
	    <?php } ?>


        <div id="newsletter-builder-area-center-frame-content" dir="<?php echo $dir ?>">

            <!-- Composer content -->

        </div>
    </div>

    <div id="newsletter-builder-sidebar" dir="<?php echo is_rtl() ? 'rtl' : 'ltr' ?>">

        <div class="tnpc-tabs">
            <button class="tablinks" onclick="openTab(event, 'tnpc-blocks')" data-tab-id='tnpc-blocks' id="defaultOpen"><?php _e('Blocks', 'newsletter') ?></button>
            <button class="tablinks" onclick="openTab(event, 'tnpc-global-styles')" data-tab-id='tnpc-global-styles'><?php _e('Settings', 'newsletter') ?></button>
        </div>

        <div id="tnpc-blocks" class="tabcontent">
            <?php foreach ($blocks as $k => $section) { ?>
                <div class="newsletter-sidebar-add-buttons" id="sidebar-add-<?php echo $k ?>">
                    <!--<h4><span><?php echo ucfirst($k) ?></span></h4>-->
                    <?php foreach ($section AS $key => $block) { ?>
                        <div class="newsletter-sidebar-buttons-content-tab" data-id="<?php echo $key ?>" data-name="<?php echo esc_attr($block['name']) ?>">
                            <img src="<?php echo $block['icon'] ?>" title="<?php echo esc_attr($block['name']) ?>">
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <div id="tnpc-global-styles" class="tabcontent">

            <form id="tnpc-global-styles-form">

                <div class="tnp-field-row">
                    <div class="tnp-field-col-2">
                        <?php $fields->color('options_composer_background', __('Main background', 'newsletter')) ?>
                    </div>
                    <div class="tnp-field-col-2">
                        <?php $fields->color('options_composer_block_background', 'Blocks background') ?>
                    </div>
                </div>

                <?php $fields->font('options_composer_title_font', __('Titles font', 'newsletter')) ?>
                <?php $fields->font('options_composer_text_font', __('Text font', 'newsletter')) ?>
                <?php $fields->button_style('options_composer_button', __('Button style', 'newsletter')); ?>

                <button class="button-secondary" name="apply"><?php _e("Apply", 'newsletter') ?></button>

            </form>

        </div>

        <!-- Block options container (dynamically loaded -->
        <div id="tnpc-block-options">
            <div id="tnpc-block-options-buttons">
                <span id="tnpc-block-options-cancel" class="button-secondary"><?php _e("Cancel", "newsletter") ?></span>
                <span id="tnpc-block-options-save" class="button-primary"><?php _e("Apply", "newsletter") ?></span>
            </div>
            <form id="tnpc-block-options-form" onsubmit="return false;"></form>
        </div>

    </div>

    <div style="clear: both"></div>

</div>

<div style="display: none">
    <div id="newsletter-preloaded-export"></div>
    <!-- Block placeholder used by jQuery UI -->
    <div id="draggable-helper"></div>
    <div id="sortable-helper"></div>
</div>

<script type="text/javascript">
    TNP_PLUGIN_URL = "<?php echo esc_js(NEWSLETTER_URL) ?>";
    TNP_HOME_URL = "<?php echo esc_js(home_url('/', is_ssl() ? 'https' : 'http')) ?>";
    tnp_context_type = "<?php echo esc_js($context_type) ?>";
    tnp_nonce = '<?php echo esc_js(wp_create_nonce('save')) ?>';
    tnp_preset_nonce = '<?php echo esc_js(wp_create_nonce('preset')) ?>';
</script>
<?php
wp_enqueue_script('tnp-composer', plugins_url('newsletter') . '/emails/tnp-composer/_scripts/newsletter-builder-v2.js', ['tnp-modal', 'tnp-toast'], NEWSLETTER_VERSION);
?>

<?php include NEWSLETTER_DIR . '/emails/subjects.php'; ?>

<?php if (function_exists('wp_enqueue_editor')) wp_enqueue_editor(); ?>
