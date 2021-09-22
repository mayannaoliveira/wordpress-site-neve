<?php
/* @var $this Newsletter */
defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$extensions = $this->getTnpExtensions();

if ($controls->is_action('activate')) {
    $result = activate_plugin('newsletter-extensions/extensions.php');
    if (is_wp_error($result)) {
        $controls->errors .= __('Error while activating:', 'newsletter') . " " . $result->get_error_message();
    } else {
        wp_clean_plugins_cache(false);
        $this->clear_extensions_cache();
        $controls->js_redirect('admin.php?page=newsletter_extensions_index');
    }
}

?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-body">
        <?php if (is_wp_error(validate_plugin('newsletter-extensions/extensions.php'))) { ?>
            <div id="tnp-promo">

                <h1>Supercharge Newsletter with our Professional Addons</h1>
                <div class="tnp-promo-how-to">
                    <h3>How to install:</h3>
                    <p>To add our addons, free or professional, you need to install our Addons Manager. But don't worry, it's super easy! Just click on "Download" button to download the zip file of
                        the Addon Manager from our website, then click on "Install" to upload the same zip file to your WordPress installation.</p>
                </div>
                <div class="tnp-promo-buttons">
                    <a class="tnp-promo-button" href="https://www.thenewsletterplugin.com/get-addons-manager"><i class="fas fa-cloud-download-alt"></i> Download Addons Manager</a>
                    <a class="tnp-promo-button" href="<?php echo admin_url('plugin-install.php?tab=upload') ?>"><i class="fas fa-cloud-upload-alt"></i> Install</a>
                </div>

            </div>
        <?php } elseif (is_plugin_inactive('newsletter-extensions/extensions.php')) { ?>
            <div id="tnp-promo">
                <div class="tnp-promo-how-to">
                    <p>Addons Manager seems installed but not active.</p>
                    <p>Activate it to install and update our free and professional addons.</p>
                </div>
                <div class="tnp-promo-buttons">
                    <a class="tnp-promo-button" href="<?php echo wp_nonce_url(admin_url('admin.php') . '?page=newsletter_main_extensions&act=activate', 'save'); ?>"><i class="fas fa-power-off"></i> Activate</a>
                </div>
            </div>
        <?php } ?>

        <?php if (is_array($extensions)) { ?>

            <!-- Extensions -->
            <h3 class="tnp-section-title">Additional professional features</h3>
            <?php foreach ($extensions AS $e) { ?>

                <?php if ($e->type == "extension" || $e->type == "premium") { ?>
                    <div class="<?php echo $e->free ? 'tnp-extension-free-box' : 'tnp-extension-premium-box' ?> <?php echo $e->slug ?>">

                        <?php if ($e->free) { ?>
                            <img class="tnp-extensions-free-badge" src="<?php echo plugins_url('newsletter') ?>/images/extension-free.png">
                        <?php } ?>
                        <div class="tnp-extensions-image"><img src="<?php echo $e->image ?>" alt="" /></div>
                        <h3><?php echo $e->title ?></h3>
                        <p><?php echo $e->description ?></p>
                    </div>
                <?php } ?>
            <?php } ?>

            <!-- Integrations -->
            <h3 class="tnp-section-title">Integrations with 3rd party plugins</h3>
            <?php foreach ($extensions AS $e) { ?>

                <?php if ($e->type == "integration") { ?>

                    <div class="<?php echo $e->free ? 'tnp-extension-free-box' : 'tnp-integration-box' ?> <?php echo $e->slug ?>">

                        <?php if ($e->free) { ?>
                            <img class="tnp-extensions-free-badge" src="<?php echo plugins_url('newsletter') ?>/images/extension-free.png">
                        <?php } ?>
                        <div class="tnp-extensions-image"><img src="<?php echo $e->image ?>"></div>
                        <h3><?php echo $e->title ?></h3>
                        <p><?php echo $e->description ?></p>
                    </div>
                <?php } ?>

            <?php } ?>

            <!-- Delivery -->
            <h3 class="tnp-section-title">Integrations with reliable mail delivery services</h3>
            <?php foreach ($extensions AS $e) { ?>

                <?php if ($e->type == "delivery") { ?>
                    <div class="<?php echo $e->free ? 'tnp-extension-free-box' : 'tnp-integration-box' ?> <?php echo $e->slug ?>">

                        <?php if ($e->free) { ?>
                            <img class="tnp-extensions-free-badge" src="<?php echo plugins_url('newsletter') ?>/images/extension-free.png">
                        <?php } ?>
                        <div class="tnp-extensions-image"><img src="<?php echo $e->image ?>" alt="" /></div>
                        <h3><?php echo $e->title ?></h3>
                        <p><?php echo $e->description ?></p>
                    </div>
                <?php } ?>

            <?php } ?>


        <?php } else { ?>

            <p style="color: white;">No addons available, try later.</p>

        <?php } ?>


        <p class="clear"></p>

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
