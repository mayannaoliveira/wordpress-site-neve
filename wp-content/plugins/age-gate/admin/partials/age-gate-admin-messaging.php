<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://agegate.io
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/public/partials
 */
?>
<div class="wrap">
  <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

  <?php //include AGE_GATE_PATH . 'admin/partials/parts/tabs.php';?>

  <form class="custom-form-fields" action="admin-post.php" method="post">
    <input type="hidden" name="action" value="age_gate_messages">
    <?php wp_nonce_field('age_gate_update_messages', 'nonce'); ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row"><label for="wp_age_gate_instruction"><?php _e("Headline", 'age-gate') ;?></label></th>
          <td>
            <?php $this->render_language_input('instruction', $values); ?>
            <p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_messaging"><?php _e("Sub headline", 'age-gate') ;?></label></th>
          <td>
            <?php $this->render_language_input('messaging', $values); ?>
            <p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>
          </td>
        </tr>

        <tr>
          <th scope="row"><label for="wp_age_gate_remember_me_text"><?php _e("Remember me text", 'age-gate') ;?></label></th>
          <td>
            <?php if (!$this->settings['restrictions']['remember']): ?>
              <?php _e("Only applicable if Remember me is enabled", 'age-gate'); ?>

              <?php $this->render_language_input('remember_me_text', $values, 'settings', 'hidden'); ?>
            <?php else: ?>
              <?php $this->render_language_input('remember_me_text', $values); ?>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_yes_no_message"><?php _e("Yes/No sub question", 'age-gate') ;?></label></th>
          <td>
            <?php if ($this->settings['restrictions']['input_type'] !== 'buttons'): ?>
              <?php _e("Only applicable if using yes/no buttons", 'age-gate'); ?>

              <?php $this->render_language_input('yes_no_message', $values, 'settings', 'hidden'); ?>
            <?php else: ?>

              <?php $this->render_language_input('yes_no_message', $values); ?>
              <p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_yes_text"><?php _e("Yes button text", 'age-gate') ;?></label></th>
          <td>
            <?php if ($this->settings['restrictions']['input_type'] !== 'buttons'): ?>
              <?php _e("Only applicable if using yes/no buttons", 'age-gate'); ?>

              <?php $this->render_language_input('yes_text', $values, 'settings', 'hidden'); ?>
            <?php else: ?>

              <?php $this->render_language_input('yes_text', $values); ?>

          <?php endif; ?>
          </td>

        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_no_text"><?php _e("No button text", 'age-gate') ;?></label></th>
          <td>
            <?php if ($this->settings['restrictions']['input_type'] !== 'buttons'): ?>
              <?php _e("Only applicable if using yes/no buttons", 'age-gate'); ?>

              <?php $this->render_language_input('no_text', $values, 'settings', 'hidden'); ?>

            <?php else: ?>

              <?php $this->render_language_input('no_text', $values); ?>


            <?php endif; ?>
          </td>

        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_text_day"><?php _e('Day label', 'age-gate'); ?></label></th>
          <td>
            <?php $this->render_language_input('text_day', $values); ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_text_month"><?php _e('Month label', 'age-gate'); ?></label></th>
          <td>
            <?php $this->render_language_input('text_month', $values); ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_text_year"><?php _e('Year label', 'age-gate'); ?></label></th>
          <td>
            <?php $this->render_language_input('text_year', $values); ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_button_text"><?php _e('Submit button text', 'age-gate'); ?></label></th>
          <td>

            <?php $this->render_language_input('button_text', $values); ?>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_cookie_message"><?php _e("No cookies message", 'age-gate'); ?></label>
          </th>
          <td>

            <?php $this->render_language_input('cookie_message', $values); ?>

          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_additional"><?php _e('Additional content', 'age-gate'); ?></label>
          </th>
          <td>
            <p><?php _e('Use this area to add an addtional info or terms of entry.', 'age-gate'); ?></p><br />

            <?php $this->render_language_input('additional', $values, 'settings', 'wysiwyg'); ?>
          </td>
        </tr>
      </tbody>
    </table>

    <h3><?php _e('Validation messages', 'age-gate') ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row"><label for="wp_age_gate_invalid_input_msg"><?php _e("Invalid inputs", 'age-gate') ;?></label></th>
          <td>

            <?php $this->render_language_input('invalid_input_msg', $values); ?>

            <?php /*<p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>*/ ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_under_age_msg"><?php _e("Under age", 'age-gate') ;?></label></th>
          <td>

            <?php $this->render_language_input('under_age_msg', $values); ?>


            <?php /*<p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>*/ ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_generic_error_msg"><?php _e("Generic error", 'age-gate') ;?></label></th>
          <td>

            <?php $this->render_language_input('generic_error_msg', $values); ?>

            <?php /*<p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>*/ ?>
          </td>
        </tr>
      </tbody>
    </table>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_date"><?php _e("Invalid date message", 'age-gate'); ?></label>
          </th>
          <td>
            <?php $this->render_language_input('validate_date', $validation, 'validation'); ?>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_required"><?php _e("Required field message", 'age-gate'); ?></label>
          </th>
          <td>

            <?php $this->render_language_input('validate_required', $validation, 'validation'); ?>
            <p class="note"><?php _e("Adding “{field}” will output the field name.", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_numeric"><?php _e("Numeric field message", 'age-gate'); ?></label>
          </th>
          <td>

            <?php $this->render_language_input('validate_numeric', $validation, 'validation'); ?>
            <p class="note"><?php _e("Adding “{field}” will output the field name.", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_min_len"><?php _e("Minimum length message", 'age-gate'); ?></label>
          </th>
          <td>

            <?php $this->render_language_input('validate_min_len', $validation, 'validation'); ?>

            <p class="note"><?php _e("Adding “{field}” will output the field name. Adding “{param}” will output the required length.", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_max_len"><?php _e("Maximum length message", 'age-gate'); ?></label>
          </th>
          <td>

            <?php $this->render_language_input('validate_max_len', $validation, 'validation'); ?>

            <p class="note"><?php _e("Adding “{field}” will output the field name. Adding “{param}” will output the required length.", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_max_numeric"><?php _e("Maximum numeric message", 'age-gate'); ?></label>
          </th>
          <td>

            <?php $this->render_language_input('validate_max_numeric', $validation, 'validation'); ?>

            <p class="note"><?php _e("Adding “{field}” will output the field name. Adding “{param}” will output the required length.", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_max_numeric"><?php _e("Minimum numeric message", 'age-gate'); ?></label>
          </th>
          <td>

            <?php $this->render_language_input('validate_min_numeric', $validation, 'validation'); ?>

            <p class="note"><?php _e("Adding “{field}” will output the field name. Adding “{param}” will output the required length.", 'age-gate'); ?></p>
          </td>
        </tr>

      </tbody>
    </table>
    <p><?php _e("More validators are available for custom fields.", 'age-gate'); ?> <a href="https://agegate.io/docs/guides/custom-form-fields/available-validators" target="_blank"><?php _e("See the documentation", 'age-gate'); ?> <i aria-hidden="true" class="dashicons dashicons-external"></i></a></p>
    <?php submit_button(); ?>
  </form>
</div>
