<?php
$flag = false;
$default_lang = false;
$current_lang = false;

if(self::$language){
  $flag = '<img src="'. self::$language->default['country_flag_url'] . '" alt="'. strtoupper(self::$language->default['language_code']) .'" />';
  $default_lang = self::$language->default['language_code'];
  $current_lang = (self::$language->current ? self::$language->current['language_code'] : false);
}

switch($field){
  case 'yes_text':
  case 'no_text':
    $className = 'small-text';
    $field_type = 'text';
    $atts = [];
  break;
  case 'button_text':
  case 'text_day':
  case 'text_month':
  case 'text_year':  
    $className = 'medium-text';
    $field_type = 'text';
    $atts = [];
  break;
  case 'min_age':
    $className = 'small-text';
    $field_type = 'number';
    $atts = ['required' => 'required'];
  break;
  default:
    $className = 'regular-text';
    $field_type = 'text';
    $atts = [];
  break;
}


?>

<?php if(!$current_lang || $default_lang === $current_lang): ?>
<div class="input-wrapper <?php echo $default_lang; ?>">
  <?php echo $flag; ?>
  <?php echo form_input(array(
    'name' => 'ag_'. $prefix .'['.$field.']',
    'type' => $field_type,
    'id' => 'wp_age_gate_' . $field
  ), $values[$field], array_merge(array('class' => $className . ' ltr'), $atts));
  ?>
</div>
<?php else: ?>
  <?php echo form_hidden('ag_'. $prefix .'['.$field.']', $values[$field]); ?>
<?php endif; ?>

<?php if (self::$language): ?>
  <?php foreach (self::$language->languages as $code => $lang): ?>
    <?php if(!$current_lang || $code === $current_lang): ?>
      <div class="input-wrapper <?php echo $code ?>">
        <img src="<?php echo $lang['country_flag_url']; ?>" alt="<?php echo strtoupper($lang['language_code']); ?>" />
        <?php echo form_input(array(
          'name' => 'ag_'. $prefix .'[lang]['. $code .']['. $field .']',
          'type' => $field_type,
          'id' => 'wp_age_gate_' . $field . '_' . $code
        ), $values['lang'][$code][$field], array_merge(array('class' => $className . ' ltr'), $atts));
        ?>
      </div>
    <?php else: ?>
      <?php echo form_hidden('ag_'. $prefix .'[lang]['. $code .']['. $field .']', $values['lang'][$code][$field]); ?>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>