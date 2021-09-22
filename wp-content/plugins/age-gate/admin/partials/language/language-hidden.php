<?php echo form_hidden('ag_'. $prefix .'['.$field.']', $values[$field]); ?>
<?php if (self::$language): ?>
  <?php foreach (self::$language->languages as $code => $lang): ?>
    <?php echo form_hidden('ag_'. $prefix .'[lang]['.$code .']['.$field.']', $values['lang'][$code][$field]); ?>
  <?php endforeach; ?>
<?php endif; ?>
