<?php
global $current_user, $wpdb;

defined('ABSPATH') || exit;

$dismissed = get_option('newsletter_dismissed', []);

$user_count = Newsletter::instance()->get_user_count();

$is_administrator = current_user_can('administrator');

function newsletter_print_entries($group) {
    $entries = apply_filters('newsletter_menu_' . $group, array());
    if (!$entries) {
        return;
    }

    foreach ($entries as &$entry) {
        echo '<li><a href="', $entry['url'], '">', $entry['label'];
        if (!empty($entry['description'])) {
            echo '<small>', $entry['description'], '</small>';
        }
        echo '</a></li>';
    }
}

// Check the status to show a warning if needed
$status_options = Newsletter::instance()->get_options('status');
$warning = false;

$warning |= empty($status_options['mail']);
?>
<script>
    function tnp_close_promotion() {
        jQuery.post(ajaxurl + "?action=tnp_hide_promotion", {id: 'june-2020'});
        document.getElementById('tnp-promotion-bar').style.display = 'none';
    }
</script>

<?php if (get_option('newsletter_promotion') !== 'june-2020' && time() < gmmktime(0, 0, 0, 7, 15, 2020)) { ?>
    <div id="tnp-promotion-bar">
        <a href="https://www.thenewsletterplugin.com/premium?utm_source=plugin-bar&utm_campaign=june-2020" onclick="tnp_close_promotion(); return true;" target="_blank">50% discount offer until July, 15. Check it out.</a>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="javascript:void(tnp_close_promotion('june-2020'))">No, thank you!</a>
    </div>
<?php } ?>


<div class="tnp-drowpdown" id="tnp-header">
    <a href="?page=newsletter_main_index"><img src="<?php echo plugins_url('newsletter'); ?>/images/header/tnp-logo-red-header.png" class="tnp-header-logo" style="vertical-align: bottom;"></a>
    <ul>
        <li><a href="#"><i class="fas fa-users"></i> <?php _e('Subscribers', 'newsletter') ?> <i class="fas fa-chevron-down"></i></a>
            <ul>
                <li>
                    <a href="?page=newsletter_users_index"><i class="fas fa-search"></i> <?php _e('Search And Edit', 'newsletter') ?>
                        <small><?php _e('Add, edit, search', 'newsletter') ?></small></a>
                </li>

                <li>
                    <a href="?page=newsletter_profile_index"><i class="fas fa-user-circle"></i> <?php _e('Profile page', 'newsletter') ?>
                        <small><?php _e('The subscriber personal profile editing panel', 'newsletter') ?></small></a>
                </li>

                <?php if (!class_exists('NewsletterImport')) { ?>
                    <li>
                        <a href="?page=newsletter_users_import"><i class="fas fa-upload"></i> <?php _e('Import', 'newsletter') ?>
                            <small><?php _e('Import from external sources', 'newsletter') ?></small></a>
                    </li>
                <?php } ?>

                <li>
                    <a href="?page=newsletter_users_export"><i class="fas fa-download"></i> <?php _e('Export', 'newsletter') ?>
                        <small><?php _e('Export your subscribers list', 'newsletter') ?></small></a>
                </li>

                <li>
                    <a href="?page=newsletter_users_massive"><i class="fas fa-wrench"></i> <?php _e('Maintenance', 'newsletter') ?>
                        <small><?php _e('Massive actions: change list, clean up, ...', 'newsletter') ?></small></a>
                </li>

                <li>
                    <a href="?page=newsletter_users_statistics"><i class="fas fa-chart-bar"></i> <?php _e('Statistics', 'newsletter') ?>
                        <small><?php _e('All about your subscribers', 'newsletter') ?></small></a>
                </li>

                <?php newsletter_print_entries('subscribers') ?>
            </ul>
        </li>
        <li><a href="#"><i class="fas fa-list"></i> <?php _e('List Building', 'newsletter') ?> <i class="fas fa-chevron-down"></i></a>
            <ul>
                <li>
                    <a href="?page=newsletter_subscription_profile"><i class="fas fa-check-square"></i> <?php _e('Subscription Form Fields, Buttons, Labels', 'newsletter') ?>
                        <small><?php _e('When and what data to collect', 'newsletter') ?></small></a>
                </li>

                <li>
                    <a href="?page=newsletter_subscription_options"><i class="fas fa-sign-in-alt"></i> <?php _e('Subscription', 'newsletter') ?>
                        <small><?php _e('The subscription process in detail', 'newsletter') ?></small></a>
                </li>

                <li>
                    <a href="?page=newsletter_subscription_lists"><i class="fas fa-th-list"></i> <?php _e('Lists', 'newsletter') ?>
                        <small><?php _e('Profile the subscribers for a better targeting', 'newsletter') ?></small></a>
                </li>

                <li>
                    <a href="?page=newsletter_subscription_antibot"><i class="fas fa-lock"></i> <?php _e('Security', 'newsletter') ?>
                        <small><?php _e('Spam subscriptions control', 'newsletter') ?></small></a>
                </li>

                <li>
                    <a href="?page=newsletter_unsubscription_index"><i class="fas fa-sign-out-alt"></i> <?php _e('Unsubscription', 'newsletter') ?>
                        <small><?php _e('How to give the last goodbye (or avoid it!)', 'newsletter') ?></small></a>
                </li>

                <?php
                newsletter_print_entries('subscription');
                ?>
            </ul>
        </li>

        <li>
            <a href="#"><i class="fas fa-newspaper"></i> <?php _e('Newsletters', 'newsletter') ?> <i class="fas fa-chevron-down"></i></a>
            <ul>
                <li>
                    <a href="?page=newsletter_emails_composer"><i class="fas fa-plus"></i> <?php _e('Create newsletter', 'newsletter') ?>
                        <small><?php _e('Start your new campaign', 'newsletter') ?></small></a>
                </li>
                
                <li>
                    <a href="?page=newsletter_emails_index"><i class="fas fa-newspaper"></i> <?php _e('Newsletters', 'newsletter') ?>
                        <small><?php _e('The classic "write & send" newsletters', 'newsletter') ?></small></a>
                </li>
                
                <li>
                    <a href="<?php echo NewsletterStatistics::instance()->get_index_url() ?>"><i class="fas fa-chart-bar"></i> <?php _e('Statistics', 'newsletter') ?>
                        <small><?php _e('Tracking configuration and basic data', 'newsletter') ?></small></a>
                </li>
                <?php
                newsletter_print_entries('newsletters');
                ?>
            </ul>
        </li>

        <li>
            <a href="#"><i class="fas fa-cog"></i> <?php _e('Settings', 'newsletter') ?> <i class="fas fa-chevron-down"></i></a>
            <ul>
                <?php if ($is_administrator) { ?>
                    <li>
                        <a href="?page=newsletter_main_main"><i class="fas fa-cogs"></i> <?php _e('General Settings', 'newsletter') ?>
                            <small><?php _e('Delivery speed, sender details, ...', 'newsletter') ?></small></a>
                    </li>
                    <?php if (!class_exists('NewsletterSmtp')) { ?>
                        <li>
                            <a href="?page=newsletter_main_smtp"><i class="fas fa-server"></i> <?php _e('SMTP', 'newsletter') ?>
                                <small><?php _e('External mail server', 'newsletter') ?></small>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>

                <li>
                    <a href="?page=newsletter_main_info"><i class="fas fa-info"></i> <?php _e('Company Info', 'newsletter') ?>
                        <small><?php _e('Social, address, logo and general info', 'newsletter') ?></small></a>
                </li>
                
                <li>
                    <a href="?page=newsletter_subscription_template"><i class="fas fa-file-alt"></i> <?php _e('Messages Template', 'newsletter') ?>
                        <small><?php _e('Change the look of your service emails', 'newsletter') ?></small></a>
                </li>

                <?php
                newsletter_print_entries('settings');
                ?>
            </ul>
        </li>

        <?php if ($is_administrator) { ?>
            <li>
                <a href="#"><i class="fas fa-thermometer"></i> <?php _e('System', 'newsletter') ?>
                    <?php if ($warning) { ?>
                        <i class="fas fa-exclamation-triangle" style="color: red;"></i>
                    <?php } ?>
                </a>
                <ul>
                    <li>
                        <a href="<?php echo admin_url('site-health.php')?> "><i class="fas fa-file"></i> <?php _e('Site health') ?>
                            <small><?php _e('WP native site health checks', 'newsletter') ?></small></a>
                    </li>
                    <li>
                        <a href="?page=newsletter_main_delivery"><i class="fas fa-file"></i> <?php _e('Delivery Diagnostic', 'newsletter') ?>
                            <small><?php _e('Delivery analysis and test', 'newsletter') ?></small></a>
                    </li>
                    <li>
                        <a href="?page=newsletter_main_status"><i class="fas fa-file"></i> <?php _e('Status', 'newsletter') ?>
                            <small><?php _e('Checks and parameters', 'newsletter') ?></small></a>
                    </li>
                    
                    <li>
                        <a href="?page=newsletter_main_logs"><i class="fas fa-file"></i> <?php _e('Logs', 'newsletter') ?>
                            <small><?php _e('Plugin and addons logs', 'newsletter') ?></small></a>
                    </li>
                </ul>
            </li>
        <?php } ?>

        <?php
        $license_data = Newsletter::instance()->get_license_data();
        $premium_url = 'https://www.thenewsletterplugin.com/premium?utm_source=plugin&utm_medium=link&utm_campaign=header&utm_content=' . urlencode($_GET['page']);
        ?>

        <?php if (empty($license_data)) { ?>
            <li class="tnp-professional-extensions-button"><a href="<?php echo $premium_url ?>" target="_blank">
                    <i class="fas fa-trophy"></i> <?php _e('Get Professional Addons', 'newsletter') ?></a>
            </li>
        <?php } elseif (is_wp_error($license_data)) { ?>
            <li class="tnp-professional-extensions-button-red">
                <a href="?page=newsletter_main_main"><i class="fas fa-hand-paper" style="color: white"></i> <?php _e('Unable to check', 'newsletter') ?></a>
            </li>

        <?php } elseif ($license_data->expire == 0) { ?>

            <li class="tnp-professional-extensions-button"><a href="<?php echo $premium_url ?>" target="_blank">
                    <i class="fas fa-trophy"></i> <?php _e('Get Professional Addons', 'newsletter') ?></a>
            </li>

        <?php } elseif ($license_data->expire < time()) { ?>

            <li class="tnp-professional-extensions-button-red">
                <a href="?page=newsletter_main_main"><i class="fas fa-hand-paper" style="color: white"></i> <?php _e('License expired', 'newsletter') ?></a>
            </li>

        <?php } elseif ($license_data->expire >= time()) { ?>

            <?php $p = class_exists('NewsletterExtensions') ? 'newsletter_extensions_index' : 'newsletter_main_extensions'; ?>
            <li class="tnp-professional-extensions-button">
                <a href="?page=<?php echo $p ?>"><i class="fas fa-check-square"></i> <?php _e('License active', 'newsletter') ?></a>
            </li>


        <?php } ?>
    </ul>
</div>

<?php if (isset($_GET['debug']) || !isset($dismissed['newsletter-shortcode'])) { ?>
    <?php
    // Check of Newsletter dedicated page
    if (!empty(Newsletter::instance()->options['page'])) {
        if (get_post_status(Newsletter::instance()->options['page']) === 'publish') {
            $content = get_post_field('post_content', Newsletter::instance()->options['page']);
            // With and without attributes
            if (strpos($content, '[newsletter]') === false && strpos($content, '[newsletter ') === false) {
                ?>
                <div class="tnp-notice">
                    <a href="<?php echo $_SERVER['REQUEST_URI'] . '&noheader=1&dismiss=newsletter-shortcode' ?>" class="tnp-dismiss">&times;</a>
                    The Newsletter dedicated page does not contain the [newsletter] shortcode. If you're using a visual composer it could be ok.
                    <a href="<?php echo site_url('/wp-admin/post.php') ?>?post=<?php echo esc_attr(Newsletter::instance()->options['page']) ?>&action=edit"><strong>Edit the page</strong></a>.

                </div>
                <?php
            }
        }
    }
    ?>
<?php } ?>

<?php if (isset($_GET['debug']) || !isset($dismissed['rate']) && $user_count > 300) { ?>
    <div class="tnp-notice">
        <a href="<?php echo $_SERVER['REQUEST_URI'] . '&noheader=1&dismiss=rate' ?>" class="tnp-dismiss">&times;</a>

        We never asked before and we're curious: <a href="http://wordpress.org/plugins/newsletter/" target="_blank">would you rate this plugin</a>?
        (few seconds required - account on WordPress.org required, every blog owner should have one...). <strong>Really appreciated, The Newsletter Team</strong>.

    </div>
<?php } ?>

<?php if (isset($_GET['debug']) || !isset($dismissed['newsletter-page']) && empty(Newsletter::instance()->options['page'])) { ?>
    <div class="tnp-notice">
        <a href="<?php echo $_SERVER['REQUEST_URI'] . '&noheader=1&dismiss=newsletter-page' ?>" class="tnp-dismiss">&times;</a>

        You should create a blog page to show the subscription form and the subscription messages. Go to the
        <a href="?page=newsletter_main_main">general settings panel</a> to configure it.

    </div>
<?php } ?>

<?php if (isset($_GET['debug']) || !isset($dismissed['newsletter-subscribe']) && get_option('newsletter_install_time') && get_option('newsletter_install_time') < time() - 86400 * 15) { ?>
    <div class="tnp-notice">
        <a href="<?php echo $_SERVER['REQUEST_URI'] . '&noheader=1&dismiss=newsletter-subscribe' ?>" class="tnp-dismiss">&times;</a>
        If you want to be informed of important updates of Newsletter, you may want to subscribe to our newsletter<br>
        <form action="https://www.thenewsletterplugin.com/?na=s" target="_blank" method="post">
            <input type="hidden" value="plugin-header" name="nr">
            <input type="hidden" value="3" name="nl[]">
            <input type="hidden" value="single" name="optin">
            <input type="email" name="ne" value="<?php echo esc_attr(get_option('admin_email')) ?>">
            <input type="submit" value="<?php esc_attr_e('Subscribe', 'newsletter') ?>">
        </form>
    </div>
<?php } ?>

<?php
if (!defined('NEWSLETTER_CRON_WARNINGS') || NEWSLETTER_CRON_WARNINGS) {
    $x = wp_next_scheduled('newsletter');
    if ($x === false) {
        echo '<div class="tnpc-warning">The Newsletter delivery engine is off (it should never be off). Deactivate and reactivate the Newsletter plugin.</div>';
    } else if (time() - $x > 900) {
        echo '<div class="tnpc-warning">The WP scheduler doesn\'t seem to be running correctly for Newsletter. <a href="https://www.thenewsletterplugin.com/documentation/?p=6128" target="_blank"><strong>Read this page to solve the problem</strong></a>.</div>';
    } else {
//            if (empty($this->options['disable_cron_notice'])) {
//                $cron_data = get_option('newsletter_diagnostic_cron_data');
//                if ($cron_data && $cron_data['mean'] > 500) {
//                    echo '<div class="notice notice-error"><p>The WP scheduler doesn\'t seem to be triggered enough often for Newsletter. <a href="https://www.thenewsletterplugin.com/documentation/newsletter-delivery-engine#cron" target="_blank"><strong>Read this page to solve the problem</strong></a> or disable this notice on <a href="admin.php?page=newsletter_main_main"><strong>main settings</strong></a>.</p></div>';
//                }
//            }
            }
        }
 ?>

<?php
if ( $_GET['page'] !== 'newsletter_emails_edit' ) {

	$last_failed_newsletters = Newsletter::instance()->get_emails_by_field( 'status', TNP_Email::STATUS_ERROR );

	foreach ( $last_failed_newsletters as $newsletter ) {
		echo '<div class="tnpc-error">';
		printf(__('Newsletter "%s" stopped by fatal error.', 'newsletter'), esc_html( $newsletter->subject ));
                echo '&nbsp;';
                $c = new NewsletterControls();
                $c->btn_link('?page=newsletter_emails_edit&id=' . $newsletter->id, __('Check', 'newsletter'));
		echo '</div>';
	}

}
?>

<div id="tnp-notification">
    <?php
    if (isset($controls)) {
        $controls->show();
        $controls->messages = '';
        $controls->errors = '';
    }
    ?>
</div>


