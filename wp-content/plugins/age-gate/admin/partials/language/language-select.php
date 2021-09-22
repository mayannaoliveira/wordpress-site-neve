<?php
$flag = false;
$default_lang = false;
$current_lang = false;

if(self::$language){
  $flag = '<img src="'. self::$language->default['country_flag_url'] . '" alt="'. strtoupper(self::$language->default['language_code']) .'" />';
  $default_lang = self::$language->default['language_code'];
  $current_lang = (self::$language->current ? self::$language->current['language_code'] : false);
}

?>
<?php if(!$current_lang || $default_lang === $current_lang): ?>
<div class="input-wrapper <?php echo $default_lang; ?>">
  <?php echo $flag; ?>
  <?php echo form_dropdown(
    array(
      'name' => 'ag_settings['.$field.']',
      'id' => 'wp_age_gate_date_format'
    ),
    array(
      'ddmmyyyy' => __("DD MM YYYY", 'age-gate'),
      'mmddyyyy' => __("MM DD YYYY", 'age-gate')
    ),
    $values[$field]
  ); ?>
</div>
<?php else: ?>
  <?php echo form_hidden('ag_'. $prefix .'['.$field.']', $values[$field]); ?>
<?php endif; ?>

<?php if (self::$language): ?>
  <?php foreach (self::$language->languages as $code => $lang): ?>
    <?php if(!$current_lang || $code === $current_lang): ?>
      <div class="input-wrapper <?php echo $code ?>">
        <img src="<?php echo $lang['country_flag_url']; ?>" alt="<?php echo strtoupper($lang['language_code']); ?>" />

        <?php echo form_dropdown(
          array(
            'name' => 'ag_'. $prefix .'[lang]['. $code .']['. $field .']',
            'id' => 'wp_age_gate_' . $field . '_' . $code
          ),
          array(
            'ddmmyyyy' => __("DD MM YYYY", 'age-gate'),
            'mmddyyyy' => __("MM DD YYYY", 'age-gate')
          ),
          $values['lang'][$code][$field]
        ); ?>
      </div>
    <?php else: ?>
      <?php echo form_hidden('ag_'. $prefix .'[lang]['. $code .']['. $field .']', $values['lang'][$code][$field]); ?>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>