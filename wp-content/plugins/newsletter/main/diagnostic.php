<?php
/* @var $this Newsletter */
/* @var $wpdb wpdb */

defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

function tnp_get_hook_functions($tag) {
    global $wp_filter;
    if (isset($wp_filter)) {
        $b = '';
        foreach ($wp_filter[$tag]->callbacks as $priority => $functions) {

            foreach ($functions as $function) {
                //var_dump($function);
                $b .= '[' . $priority . '] ';
                if (is_array($function['function'])) {
                    if (is_object($function['function'][0])) {
                        $b .= get_class($function['function'][0]) . '::' . $function['function'][1];
                    } else {
                        $b .= $function['function'][0] . '::' . $function['function'][1];
                    }
                } else {
                    if (is_object($function['function'])) {
                        $fn = new ReflectionFunction($function['function']);
                        $b .= get_class($fn->getClosureThis()) . '(closure)';
                    } else {
                        $b .= $function['function'];
                    }
                }
                $b .= "<br>";
            }
        }
    }
    return $b;
}
?>

<div class="wrap tnp-main-diagnostic" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Diagnostic', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>

            <h3>Hooks</h3>
            <table class="widefat" id="tnp-status-table">

                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th><?php _e('Status', 'newsletter') ?></th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Filter "newsletter_replace"</td>
                        <td>
                        </td>
                        <td>
                            <?php echo tnp_get_hook_functions('newsletter_replace') ?>
                        </td>

                    </tr>
                </tbody>
            </table>
        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
