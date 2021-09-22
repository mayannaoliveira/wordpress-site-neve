<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://agegate.io
 * @since      2.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/public/partials
 */

$errors = self::$errors;

?>
<?php if (!$this->js): ?>
<!doctype html>
<html lang="en" class="age-gate-restriced age-gate-standard">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <?php if ($this->settings['appearance']['device_width']): ?>
  <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
  <?php endif; ?>
  <?php if ($this->settings['advanced']['rta_tag']): ?>
    <?php
        $rta_content = apply_filters('age_gate_rta_content', 'RTA-5042-1996-1400-1577-RTA');
        $rta_tag = apply_filters('age_gate_rta_tag', 'rating');
        echo sprintf('<meta name="%s" content="%s" />', $rta_tag, $rta_content);
    ?>

  <?php endif; ?>
  <?php if (!current_theme_supports('title-tag')): ?>
    <?php switch ($this->settings['appearance']['title_format']):
      case 'page-name': ?>
      <title><?php wp_title($this->settings['appearance']['title_separator'], true, 'right'); ?> <?php bloginfo('name'); ?></title>
      <?php break; case 'name-page': ?>
      <title><?php bloginfo('name'); ?> <?php wp_title($this->settings['appearance']['title_separator']); ?></title>
      <?php break; endswitch; ?>
  <?php endif; ?>
  <?php wp_head(); ?>
</head>
<body class="age-restriction<?php echo($errors ? ' age-gate--error' : ''); ?>">
<?php endif; ?>


  <div class="age-gate-wrapper">
    <?php if ($this->settings['appearance']['background_colour']): ?>
    <div class="age-gate-background-colour"></div>

    <?php endif; ?>
    <?php if ($this->settings['appearance']['background_image']): ?>
    <div class="age-gate-background"></div>
    <?php endif; ?>

    <?php
    $before = '';
    $before = apply_filters('age_gate_before', $before);
    echo $before;
    ?>


    <?php if ($this->js): ?>
    <div class="age-gate-loader">
      <?php $loader = '<svg version="1.1" class="age-gate-loading-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
        <path opacity="0.2" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946 s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634 c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
        <path d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0 C22.32,8.481,24.301,9.057,26.013,10.047z">
          <animateTransform attributeType="xml"
            attributeName="transform"
            type="rotate"
            from="0 20 20"
            to="360 20 20"
            dur="0.5s"
            repeatCount="indefinite"/>
        </path>
      </svg>';

      $loader = apply_filters('age_gate_loading_icon', $loader);
      echo $loader;
      ?>
    </div>
    <?php endif; ?>

    <div class="age-gate">
      <form method="post" action="<?php echo $this->post_to; ?>" class="age-gate-form">
        <?php
          $logo = $this->display_logo();
          $logo = apply_filters('age_gate_logo', $logo, $this->appearance->logo);
          echo $logo;
        ?>
        <?php
          $messages = $this->display_messages();
          $messages = apply_filters('age_gate_messaging', $messages, $this->messages, $this->age);
          echo $messages;
        ?>
        <?php if ($this->user_age && $this->user_age < $this->age && !$errors && !isset($_COOKIE['age_gate_failed']) && !$this->js): ?>
          <div class="age-gate-error">
            <p class="age-gate-error-message">
              <?php echo $this->parsedown->line(__($this->messages->errors->failed)); ?>
            </p>
          </div>
        <?php endif; ?>

        <?php if ($this->restrictions->input_type === 'buttons'): ?>

            <?php echo age_gate_error('buttons'); ?>
        <?php else: ?>
          <?php echo age_gate_error('age_gate_failed'); ?>

        <?php endif; ?>

        <?php if ($this->js && !$this->restrictions->rechallenge): ?>
        <div class="age-gate-error" data-error-field="no-rechallenge"></div>
        <?php endif; ?>

        <?php
        /* Contitional for rechallenge */
        if ($this->restrictions->rechallenge || !$this->restrictions->rechallenge && !isset($_COOKIE['age_gate_failed'])): ?>

        <?php $extra = ''; $extra = $this->_check_filtered(apply_filters('pre_age_gate_custom_fields', $extra)); echo $extra; ?>

        <?php
        /*
         * Include the relevant form elements
         */

        include AGE_GATE_PATH . "public/partials/form/{$this->restrictions->input_type}.php" ?>



        <?php if ($this->restrictions->remember): ?>
        <p class="age-gate-remember-wrapper">
          <label class="age-gate-remember">
            <?php echo form_checkbox(
            array(
                'name' => "age_gate[remember]"
              ),
            1, // value
              $this->restrictions->remember_auto_check // checked
        ); ?>
            <?php echo __($this->messages->remember); ?>
          </label>
        </p>
        <?php endif ?>

        <?php
          $extra = '';
          $extra = $this->_check_filtered(apply_filters('post_age_gate_custom_fields', $extra));
          echo $extra;
        ?>

        <?php if ($this->restrictions->input_type !== 'buttons'): ?>
        <input type="submit" value="<?php echo __($this->messages->submit) ?>" class="age-gate-submit">
        <?php endif; ?>

      <?php elseif (!$this->js && !$errors): ?>

            <p class="age-gate-error-message">
            <?php echo $this->parsedown->line(__($this->messages->errors->failed)); ?>
            </p>

        <?php endif; ?>
        <?php
          // user set "additional content"

          if ($this->messages->additional) {
              echo '<div class="age-gate-additional-information">';
              $content = html_entity_decode($this->messages->additional);
              $content = str_replace("\\", "", $this->stripslashes_deep($content));
              $content = urldecode($this->messages->additional);
              $content = html_entity_decode($content);

              $content = wpautop(wptexturize(stripslashes($content)));
              $allowed = include AGE_GATE_PATH . 'admin/config/additional-content.php';

              $allowed = apply_filters('age_gate/presentation/allowed_tags', $allowed);
              $content = wp_kses($content, $allowed);
              echo do_shortcode($content);
              echo "</div>";
          }

          // base 64 encode the age just to be a little obsure
          // not really a security thing, just to stop people easily changing
          // it in devtools
          echo form_hidden('age_gate[age]', base64_encode(base64_encode($this->age)));

          echo form_hidden('action', 'age_gate_submit');

          if (!$this->js) {
              echo str_replace('id="age_gate[nonce]"', '', wp_nonce_field('age_gate_form', 'age_gate[nonce]', true, false));
          }

          if (self::$language && self::$language->current['language_code'] !== self::$language->default['language_code']) {
              echo form_hidden('lang', self::$language->current['language_code']);
          }

          if ($this->restrictions->input_type === 'buttons') {
              echo form_hidden('confirm_action', 0);
          }
        ?>
      </form>
    </div>
    <?php
      $after = '';
      $after = apply_filters('age_gate_after', $after);
      echo $after;
      ?>
  </div>

<?php if (!$this->js): ?>
  <?php wp_footer(); ?>
  </body>
</html>
<?php endif; ?>
