<?php
$config = (object) array();

$config->tabs = array(
  'age-gate' =>
    array(
      'cap' => AGE_GATE_CAP_RESTRICTIONS,
      'title' => _x('Restriction settings', 'Admin tab title', 'age-gate')
  ),
  'age-gate-messaging' =>
    array(
      'cap' => AGE_GATE_CAP_MESSAGING,
      'title' => _x('Messaging', 'Admin tab title', 'age-gate')
  ),
  'age-gate-appearance' =>
    array(
      'cap' => AGE_GATE_CAP_APPEARANCE,
      'title' => _x('Appearance', 'Admin tab title', 'age-gate')
  ),
  'age-gate-advanced' =>
    array(
      'cap' => AGE_GATE_CAP_ADVANCED,
      'title' => _x('Advanced', 'Admin tab title', 'age-gate')
  ),

  'age-gate-access' =>
    array(
      'cap' => AGE_GATE_CAP_ACCESS,
      'title' => _x('Access Settings', 'Admin tab title', 'age-gate')
  )
);

$config->defaults = (object) array(
  'restrictions' => [
    'min_age',
    'restriction_type',
    'multi_age',
    //'restrict_register',
    'input_type',
    'inherit_category',
    'remember',
    'remember_days',
    'remember_timescale',
    'remember_auto_check',
    'date_format',
    'ignore_logged',
    'rechallenge',
    'fail_link_title',
    'fail_link',
    'button_order'
  ],
  'advanced' => [
    'use_js',
    'save_to_file',
    'custom_css',
    'dev_notify',
    'dev_hide_warning',
    'anonymous_age_gate',
    'endpoint',
    'use_default_lang',
    'use_meta_box',
    'inherit_taxonomies',
    'custom_bots',
    'enable_quicktags',
    'full_nav',
    'enable_import_export',
    'filter_qs',
    'post_to_self',
    'js_hooks',
    'cookie_name',
    'rta_tag',
    'munge_options'
  ],
  'appearance' => [
    'logo',
    'background_colour',
    'background_opacity',
    'background_image',
    'background_image_opacity',
    'foreground_colour',
    'foreground_opacity',
    'text_colour',
    'styling',
    'device_width',
    'switch_title',
    'custom_title',
    'auto_tab',
    'title_separator',
    'title_format',
    'transition',
    'background_pos_x',
    'background_pos_y',
  ],
  'messages' => [
    'instruction',
    'messaging',
    'invalid_input_msg',
    'under_age_msg',
    'generic_error_msg',
    'remember_me_text',
    'yes_no_message',
    'yes_text',
    'no_text',
    'additional',
    'button_text',
    'cookie_message',
    'text_day',
    'text_month',
    'text_year'
  ]
);


return $config;
