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
<div class="input-wrapper<?php echo($default_lang ? ' link-select' : '');?>">
  <?php echo ($flag ? $flag . ' ' : ''); ?>
  <a class="button" data-action="link-modal" href="#" title=""><?php _e("Choose link", 'age-gate'); ?></a>
  <?php if ($values['fail_link_title'] && $values['fail_link']): ?>
  <button type="button" class="button remove" data-action="remove-link"><?php _e('Remove link', 'age-gate'); ?></button>
  <?php endif; ?>

  <div class="link-container link-container_default">
    <?php if ($values['fail_link_title'] && $values['fail_link']): ?>
    <p><strong><?php echo ($values['fail_link_title'] === 'Custom') ? __('Custom', 'age-gate') : $values['fail_link_title'] ?></strong> (<?php echo $values['fail_link']; ?>)</p>
    <?php else: ?>
    <p>&nbsp;</p>
    <?php endif; ?>

  </div>

</div>
<?php endif; ?>
<?php
echo form_input(array(
  'name' => 'ag_settings[fail_link_title]',
  'type' => 'hidden',
  'id' => 'wp_age_gate_fail_link_title'
), $values['fail_link_title']);

echo form_input(array(
  'name' => 'ag_settings[fail_link]',
  'type' => 'hidden',
  'id' => 'wp_age_gate_fail_link'
), $values['fail_link']);
?>


<?php if (self::$language): ?>
  <?php foreach (self::$language->languages as $code => $lang): ?>
    <?php if(!$current_lang || $code === $current_lang): ?>
      <div class="input-wrapper link-select">
        <img src="<?php echo $lang['country_flag_url']; ?>" alt="<?php echo strtoupper($lang['country_flag_url']); ?>" />
        <a class="button" data-action="link-modal" data-lang="<?php echo $code; ?>" href="#" title=""><?php _e("Choose link", 'age-gate'); ?></a>
        <?php if ($values['lang'][$code]['fail_link_title'] && $values['lang'][$code]['fail_link']): ?>
        <button type="button" class="button remove" data-lang="<?php echo $code; ?>" data-action="remove-link"><?php _e('Remove link', 'age-gate'); ?></button>

        <?php endif; ?>
        <div class="link-container link-container_<?php echo $code; ?>" data-lang="<?php echo $code; ?>">
          <?php if ($values['lang'][$code]['fail_link_title'] && $values['lang'][$code]['fail_link']): ?>
          <p><strong><?php echo ($values['lang'][$code]['fail_link_title'] === 'Custom') ? __('Custom', 'age-gate') : $values['lang'][$code]['fail_link_title'] ?></strong> (<?php echo $values['lang'][$code]['fail_link']; ?>)</p>
          <?php else: ?>
          <p>&nbsp;</p>
          <?php endif; ?>
        </div>

      </div>

    <?php endif; ?>
    <?php
    echo form_input(array(
      'name' => 'ag_settings[lang]['. $code .'][fail_link_title]',
      'type' => 'hidden',
      'id' => 'wp_age_gate_fail_link_title_' . $code
    ), $values['lang'][$code]['fail_link_title']);

    echo form_input(array(
      'name' => 'ag_settings[lang]['. $code .'][fail_link]',
      'type' => 'hidden',
      'id' => 'wp_age_gate_fail_link_' . $code
    ), $values['lang'][$code]['fail_link']);
    ?>
  <?php endforeach; ?>
<?php endif; ?>
