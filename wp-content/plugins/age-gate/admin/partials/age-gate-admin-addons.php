<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://agegate.io
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin/partials
 */


?>

<div class="wrap">
  <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

  <?php //include AGE_GATE_PATH . 'admin/partials/parts/tabs.php'; ?>

  <h3><?php echo esc_html( $this->addon['name'] ); ?></h3>

  <?php if($this->addon):?>
  <?php do_action("age_gate_custom_tab_{$this->addon['id']}"); ?>
  <?php else: ?>

  <ul class="age-gate-addons installed">
  <?php foreach ($this->addons as $key => $addon): ?>
    <?php if(isset($addon['cap']) && current_user_can($addon['cap'])): ?>
    <li>
      <div class="logo-wrapper">
        <?php echo $this->_addon_icon($addon); ?>
      </div>
      <div class="options">
        <?php echo $addon['name']; ?>
      </div>
      <?php if (isset($addon['has_options']) && isset($addon['has_options'])): ?>
      <a class="button button-primary addon-settings" href="<?php echo add_query_arg( 'addon', $key ); ?>">Settings</a>
      <?php else: ?>
      <span class="addon-settings">This addon has no options</span>
      <?php endif; ?>

    </li>
    <?php endif; ?>
  <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</div>
