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

?>
<style>
    .placeholder {
        border: 3px dashed #ddd!important;
        background-color: #eee!important;
        height: 50px;
        margin: 0;
        width: 100%;
        box-sizing: border-box!important;
    }
    #newsletter-builder-area-center-frame-content {
        min-height: 300px!important;
    }
</style>

<style>
<?php echo NewsletterEmails::instance()->get_composer_css(); ?>
</style>

<div id="newsletter-builder">

    <div id="newsletter-builder-area" class="tnp-builder-column">

        <?php if ($tnpc_show_subject) { ?>
            <p>
                <?php $this->text('title', 60, 'Newsletter subject'); ?>
                <a href="#" class="tnp-suggest-button" onclick="tnp_suggest_subject(); return false;"><?php _e('Get ideas', 'newsletter') ?></a>
            </p>
        <?php } ?>

        <div id="newsletter-builder-area-center-frame-content">

            <!-- Composer content -->

        </div>
    </div>

    <div id="newsletter-builder-sidebar" class="tnp-builder-column">

        <div class="tnpc-tabs">
        <button class="tablinks" onclick="openTab(event, 'tnpc-blocks')" id="defaultOpen"><?php _e('Blocks', 'newsletter') ?></button>
        <button class="tablinks" onclick="openTab(event, 'tnpc-mobile-tab')"><i class="fas fa-mobile"></i> <?php _e('Mobile Preview', 'newsletter') ?></button>
        <?php if ($show_test) { ?>
        <button class="tablinks" onclick="openTab(event, 'tnpc-test-tab')"><i class="fas fa-paper-plane"></i> <?php _e('Test', 'newsletter') ?></button>
        <?php } ?>
        
        </div>

        <div id="tnpc-blocks" class="tabcontent">
            <?php foreach ($blocks as $k => $section) { ?>
                <div class="newsletter-sidebar-add-buttons" id="sidebar-add-<?php echo $k ?>">
                    <h4><span><?php echo ucfirst($k) ?></span></h4>
                    <?php foreach ($section AS $key => $block) { ?>
                        <div class="newsletter-sidebar-buttons-content-tab" data-id="<?php echo $key ?>" data-name="<?php echo esc_attr($block['name']) ?>">
                            <img src="<?php echo $block['icon'] ?>" title="<?php echo esc_attr($block['name']) ?>">
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <div id="tnpc-mobile-tab" class="tabcontent">

            <iframe id="tnpc-mobile-preview"></iframe>

        </div>

        <div id="tnpc-test-tab" class="tabcontent">

            <p><?php _e("Test subscribers:") ?></p>
            <ul>
                <?php foreach (NewsletterUsers::instance()->get_test_users() AS $user) { ?>
                    <li><?php echo $user->email ?></li>
                <?php } ?>
            </ul>
            <button class="button-secondary" onclick="tnpc_test()"><?php _e("Send a test", 'newsletter') ?></button>
            <p>
                <a href="https://www.thenewsletterplugin.com/documentation/subscribers#test" target="_blank">
                    <?php _e('Read more about test subscribers', 'newsletter') ?></a>
            </p>
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
    <div id="draggable-helper" style="width: 500px; border: 3px dashed #ddd; opacity: .7; background-color: #fff; text-align: center; text-transform: uppercase; font-size: 14px; color: #aaa; padding: 20px;"></div>
    <div id="sortable-helper" style="width: 700px; height: 75px;border: 3px dashed #ddd; opacity: .7; background-color: #fff; text-align: center; text-transform: uppercase; font-size: 14px; color: #aaa; padding: 20px;"></div>
</div>

<script type="text/javascript">
    TNP_PLUGIN_URL = "<?php echo NEWSLETTER_URL ?>";
    TNP_HOME_URL = "<?php echo home_url('/', is_ssl() ? 'https' : 'http') ?>";
    tnp_context_type = "<?php echo $context_type ?>";
</script>
<script type="text/javascript" src="<?php echo plugins_url('newsletter'); ?>/emails/tnp-composer/_scripts/newsletter-builder.js?ver=<?php echo time() ?>"></script>

<?php include NEWSLETTER_DIR . '/emails/subjects.php'; ?>

<?php if (function_exists('wp_enqueue_editor')) wp_enqueue_editor(); ?>
