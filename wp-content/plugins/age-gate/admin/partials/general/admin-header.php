<?php
$current = (isset($_GET['page']) ? $_GET['page'] : null);
$custom = array();
$custom = apply_filters('age_gate_admin_tabs', $custom);
// merge default first to keep order, then theirs to get filters,
// then default again to ensure nothing is overwritten
$tabs = array_merge($this->config->tabs, $custom, $this->config->tabs);
?>

<?php global $pagenow;
    if ($pagenow === 'admin.php' && isset($_GET['page']) && array_key_exists($_GET['page'], $tabs)) : ?>
        <div class="age-gate-toolbar">
            <h2 class="age-gate-toolbar__title"><i class="age-gate-toolbar__icon dashicons dashicons-lock"></i> <?php echo _e('Age Gate', 'age-gate'); ?></h2>

            <ul class="age-gate-toolbar__tabs">
                <?php
                    $markup = '';
                    $link = '<li class="age-gate-toolbar__tab%s"><a href="%s" class="age-gate-toolbar__link">%s</a></li>';
                    foreach ($this->config->tabs as $slug => $tab) {
                        if (current_user_can($tab['cap'])) {
                            $url = esc_url(add_query_arg(array('page' => $slug)));
                            $class = ($current === $slug) ? ' age-gate-toolbar__tab--active' : '';
                            $markup .= sprintf($link, $class, $url, $tab['title']);
                        }
                    }
                    echo $markup;
                ?>

            <?php if ($custom) : ?>
            <li class="age-gate-toolbar__tab age-gate-toolbar__tab--dropdown"><a href="#" class="age-gate-toolbar__link"><?php _e('More', 'age-gate'); ?> <span class="dashicons dashicons-arrow-down"></span></a>
                <ul class="age-gate-toolbar__subnav">
                    <?php
                    $markup = '';
                    $link = '<li class="age-gate-toolbar__subtab%s"><a href="%s" class="age-gate-toolbar__link">%s</a></li>';

                    foreach ($custom as $slug => $tab) {
                        if (current_user_can($tab['cap'])) {
                            $url = esc_url(add_query_arg(array('page' => $slug)));
                            $class = ($current === $slug) ? ' age-gate-toolbar__subtab--active' : '';
                            $markup .= sprintf($link, $class, $url, $tab['title']);
                        }
                    }
                    echo $markup;
                    ?>
                </ul>
            </li>
            <?php endif; ?>
        </div>
<?php endif; ?>
