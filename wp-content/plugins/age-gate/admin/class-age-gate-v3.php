<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}


class Age_Gate_V3 extends Age_Gate_Common
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add the sub menu for advanced Settings
     * @since 2.14.0
     */
    public function add_settings_page()
    {
        add_submenu_page(
            $this->plugin_name,
            __('Age Gate V3', 'age-gate'),
            __('V3', 'age-gate'),
            AGE_GATE_CAP_ADVANCED,
            $this->plugin_name . '-v3',
            [$this, 'display_options_page'],
            500
        );
    }

    public function display_options_page()
    {
        include AGE_GATE_PATH . 'admin/partials/age-gate-v3.php';
    }
}
