<?php
$flag = false;
$default_lang = false;
$current_lang = false;

if (self::$language) {
    $flag = '<img src="'. self::$language->default['country_flag_url'] . '" alt="'. strtoupper(self::$language->default['language_code']) .'" />';
    $default_lang = self::$language->default['language_code'];
    $current_lang = (self::$language->current ? self::$language->current['language_code'] : false);
}

switch ($field) {
  case 'yes_text':
  case 'no_text':
    $className = 'small-text';
  break;
  case 'button_text':
    $className = 'medium-text';
  break;
  default:
    $className = 'regular-text';
  break;
}


?>

<?php if (!$current_lang || $default_lang === $current_lang): ?>
  <div class="input-wrapper">
    <?php echo $flag; ?>
    <div class="wysiwyg-wrapper">
       <?php
       $wysiwyg = array(
         'media_buttons' => false,
         'quicktags' => $this->settings['advanced']['enable_quicktags'],
         'tinymce' => array(
           'wp_autoresize_on' => false,
           'resize' => false,
           'statusbar' => false,
           'mce_buttons' => 'bold, italic'
         ),
         'textarea_name' => 'ag_settings[additional]'
       );

       wp_editor(str_replace("\\", "", html_entity_decode($values['additional'])), 'additional', $wysiwyg);
      ?>
    </div>
  </div>
<?php else: ?>
  <?php echo form_hidden('ag_'. $prefix .'['.$field.']', $values[$field]); ?>
<?php endif; ?>

<?php if (self::$language): ?>
  <?php foreach (self::$language->languages as $code => $lang): ?>
    <?php if (!$current_lang || $code === $current_lang): ?>

    <div class="input-wrapper <?php echo $code ?>">
      <img src="<?php echo $lang['country_flag_url']; ?>" alt="<?php echo strtoupper($lang['language_code']) ?>" />
      <div class="wysiwyg-wrapper">
         <?php
         $wysiwyg = array(
           'media_buttons' => false,
           'quicktags' => $this->settings['advanced']['enable_quicktags'],
           'tinymce' => array(
             'wp_autoresize_on' => false,
             'resize' => false,
             'statusbar' => false,
             'mce_buttons' => 'bold, italic'
           ),
           'textarea_name' => 'ag_settings[lang]['. $code .'][additional]'
         );

         wp_editor(str_replace("\\", "", html_entity_decode($values['lang'][$code]['additional'])), 'additional_' . $code, $wysiwyg);
        ?>
      </div>
    </div>
    <?php else: ?>
      <?php echo form_hidden('ag_'. $prefix .'[lang]['. $code .']['. $field .']', $values['lang'][$code][$field]); ?>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>
