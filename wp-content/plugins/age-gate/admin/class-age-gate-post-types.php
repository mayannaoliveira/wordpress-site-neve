<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://agegate.io
 * @since      2.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 */

/**
 * Adds the restriction options to the posts types
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 */
class Age_Gate_Post_Types extends Age_Gate_Common
{
    public function __construct()
    {
        parent::__construct();
        // $this->register_age_gate_bulk_actions();
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name .'-post', AGE_GATE_URL . 'admin/js/age-gate-post.js', array( 'jquery' ), $this->version, true);

        wp_localize_script($this->plugin_name .'-post', 'ag_post', array(
      'restricted_to' => __('Restricted to', 'age-gate'),
      'change' => __('Change', 'age-gate'),
      'unrestricted' => __('Unrestricted', 'age-gate'),
    ));
    }

    public function test_box()
    {
        add_meta_box('agegate', _x('Age Gate Options', 'title', 'age-gate'), function () {
        }, 'posts', 'side', 'high');
    }

    /**
     * [add_restriction_options description]
     * @since 2.0.0
     */
    public function add_restriction_options()
    {
        if (!isset($this->settings['access'][get_post_type()]) || !$this->settings['access'][get_post_type()]) {
            $post_types = get_post_types(array('public' => true));
            add_meta_box('agegate', _x('Age Gate Options', 'title', 'age-gate'), array($this, 'misc_actions_metabox'), $post_types, 'side', 'high');
        }
    }

    /**
     * Output the meta box
     * @return [type] [description]
     */
    public function misc_actions_metabox()
    {
        $this->_misc_actions();
    }

    /**
     * Add the options to the misc actions section
     * @return [type] [description]
     */
    private function _misc_actions($post_id = null)
    {
        if (!isset($this->settings['access'][get_post_type()]) || !$this->settings['access'][get_post_type()]) {
            $id = (!$post_id) ? get_the_ID() : $post_id;
            $post_meta = array(
        'age' => get_post_meta($id, '_age_gate-age', true),
        'bypass' => get_post_meta($id, '_age_gate-bypass', true),
        'restrict' => get_post_meta($id, '_age_gate-restrict', true),
      );

            if (empty($post_meta['age'])) {
                $post_meta['age'] = $this->settings['restrictions']['min_age'];
            }

            $post_meta['is_restricted'] = $this->settings['restrictions']['restriction_type'] === 'selected' && $post_meta['restrict'] || $this->settings['restrictions']['restriction_type'] !== 'selected' && !$post_meta['bypass'];

            include AGE_GATE_PATH . 'admin/partials/parts/publish-options.php';
        }
    }

    /**
     * Save any custom options on a per post basis
     * @return [type] [description]
     * @since 2.0.0
     *
     */
    public function save_post($post_id)
    {
        // wp_die($post_id);
        // Return if autosaving
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || !isset($_POST['ag_settings'])) {
            return;
        }

        $post = $this->_filter_values($this->validation->sanitize($_POST['ag_settings']), null);

        // check the nonce
        if (! wp_verify_nonce($post['ag_nonce'], 'ag_save_post')) {
            return;
        }

        // set custom age
        $this->_set_age($post['age'], $post_id);
        $this->_set_restrict($post['restrict'], $post_id);
        $this->_set_bypass($post['bypass'], $post_id);
    }

    /**
     * Filter to ensure all fields get sent to the DB
     * @param  [type] $data [description]
     * @param  [type] $fill [description]
     * @return [type]       [description]
     * @since   2.0.0
     */
    private function _filter_values($data, $fill)
    {
        $empties = array_fill_keys([
      'bypass',
      'restrict',
      'age',
    ], $fill);

        return array_merge($empties, $data);
    }

    /**
     * Set the post meta of the age restrictions
     * @param integer $age [description]
     */
    private function _set_age($age, $id)
    {
        if (!current_user_can(AGE_GATE_CAP_SET_CUSTOM_AGE)) {
            return false;
        }

        if (!$this->settings['restrictions']['multi_age']) {
            return false;
        }

        if (!$age || $age == $this->settings['restrictions']['min_age']) {
            delete_post_meta($id, '_age_gate-age');
        } else {
            update_post_meta($id, '_age_gate-age', $age);
        }
    }

    /**
     * Set the post meta of the age restrictions
     * @param integer $age [description]
     */
    private function _set_restrict($restrict, $id)
    {
        if (!current_user_can(AGE_GATE_CAP_SET_CONTENT)) {
            return false;
        }

        if ($this->settings['restrictions']['restriction_type'] !== 'selected') {
            return false;
        }

        if (!$restrict) {
            delete_post_meta($id, '_age_gate-restrict');
        } else {
            update_post_meta($id, '_age_gate-restrict', 1);
        }
        // _age_gate-restrict
    }


    /**
     * Set the post meta of the age restrictions
     * @param integer $age [description]
     */
    private function _set_bypass($bypass, $id)
    {
        if (!current_user_can(AGE_GATE_CAP_SET_BYPASS)) {
            return false;
        }

        if ($this->settings['restrictions']['restriction_type'] !== 'all') {
            return false;
        }

        if (!$bypass) {
            delete_post_meta($id, '_age_gate-bypass');
        } else {
            update_post_meta($id, '_age_gate-bypass', 1);
        }
        // all
    // _age_gate-bypass
    }

    /**
     * Add quick edit options
     * @return [type] [description]
     */
    public function age_gate_quick_edit($column_name, $post_type)
    {
        if ('age_gate' === $column_name) {
            static $printNonce = true;
            if ($printNonce) {
                $printNonce = false;
                wp_nonce_field('ag_save_post', 'ag_settings[ag_nonce]');
            } ?>
      <fieldset class="inline-edit-col-right inline-edit-age-gate">
        <div class="inline-edit-col column-<?php echo $column_name; ?>">

          <?php
           switch ($column_name) {
           case 'age_gate':
             include AGE_GATE_PATH . 'admin/partials/parts/quick-edit.php';
             break;
          } ?>
        </div>
      </fieldset>
      <?php
        }
    }

    public function age_gate_columns($columns)
    {
        global $typenow;
        if (!isset($this->settings['access'][$typenow]) || !$this->settings['access'][$typenow]) {
            $columns['age_gate'] = '<span data-ag-tooltip="'.__('Age Gate').'"><i class="wp-menu-image dashicons-before dashicons-lock"></i></span> <span class="screen-reader-text">Age Gate</span>';
        }
        return $columns;
    }

    public function age_gate_columns_data($column, $post_id)
    {
        switch ($column) {
      case 'age_gate':
        $post_meta = array(
          'age' => get_post_meta($post_id, '_age_gate-age', true),
          'bypass' => get_post_meta($post_id, '_age_gate-bypass', true),
          'restrict' => get_post_meta($post_id, '_age_gate-restrict', true),
        );

        if (empty($post_meta['age'])) {
            $post_meta['age'] = $this->settings['restrictions']['min_age'];
        }

        $post_meta['is_restricted'] = $this->settings['restrictions']['restriction_type'] === 'selected' && $post_meta['restrict'] || $this->settings['restrictions']['restriction_type'] !== 'selected' && !$post_meta['bypass'];
        include AGE_GATE_PATH . 'admin/partials/parts/list-table.php';

        break;
    }
    }

    /**
     * Adds a new item into the Bulk Actions dropdown.
     */
    public function register_age_gate_bulk_actions()
    {
        $pts = get_post_types(['public' => true]);
        $skip = $this->settings['access'];

        foreach ($pts as $key => $value) {
            if (!isset($skip[$key])) {
                $skip[$key] = 0;
            }
        }

        foreach ($pts as $pt) {
            if (!$skip[$pt]) {
                add_filter('bulk_actions-edit-' . $pt, function ($bulk_actions) {
                    $bulk_actions['age_gate_restrict'] = __('Age Gate: Restrict', 'age-gate');
                    $bulk_actions['age_gate_unrestrict'] = __('Age Gate: Unrestrict', 'age-gate');


                    return $bulk_actions;
                });

                // now add the handler
                add_filter('handle_bulk_actions-edit-' . $pt, [$this, 'age_gate_action_handler'], 10, 3);
            }
        }
    }


    /**
     * Handle the bulk update
     * @param  [type] $redirect_to [description]
     * @param  [type] $action      [description]
     * @param  [type] $post_ids    [description]
     * @return [type]              [description]
     */
    public function age_gate_action_handler($redirect_to, $action, $post_ids)
    {
        if ('age_gate_restrict' === $action) {
            $this->_bulk_restrict($post_ids);
        } elseif ('age_gate_unrestrict' === $action) {
            $this->_bulk_unrestrict($post_ids);
        } else {
            return $redirect_to;
        }

        $redirect_to = add_query_arg(['bulk_age_gate' => count($post_ids)], $redirect_to);
        return $redirect_to;
    }

    /**
     * Shows a notice in the admin once the bulk action is completed.
     */
    public function age_gate_bulk_action_admin_notice()
    {
        if (! empty($_REQUEST['bulk_age_gate'])) {
            $drafts_count = intval($_REQUEST['bulk_age_gate']);

            printf(
                '<div id="message" class="notice notice-success fade"><p>' .
            _n('%s items have been updated.', '%s items have been updated.', $drafts_count, 'age-gate')
            . '</p></div>',
                $drafts_count
            );
        }
    }

    private function _bulk_restrict($posts = array())
    {
        foreach ($posts as $key => $post_id) {
            switch ($this->settings['restrictions']['restriction_type']) {
        case 'selected':
          update_post_meta($post_id, '_age_gate-restrict', 1);
          break;

        default:
          delete_post_meta($post_id, '_age_gate-bypass');
      }
        }
    }
    private function _bulk_unrestrict($posts = array())
    {
        foreach ($posts as $key => $post_id) {
            switch ($this->settings['restrictions']['restriction_type']) {
        case 'selected':
          delete_post_meta($post_id, '_age_gate-restrict');
          break;
        default:
          update_post_meta($post_id, '_age_gate-bypass', 1);


      }
        }
    }
}
