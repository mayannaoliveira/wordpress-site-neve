<?php
  switch ($this->settings['restrictions']['restriction_type']) {
    case 'selected':
      $checked = ($post_meta['restrict']) ? 'checked' : 'unchecked';
      break;

    default:
      $checked = ($post_meta['bypass']) ? 'checked' : 'unchecked';
      break;
  }
?>

<?php if ($post_meta['is_restricted']): ?>
  <span data-ag-tooltip="<?php _e('Restricted to', 'age-gate'); ?>: <?php echo $post_meta['age']; ?>" data-quick-checked="<?php echo $checked; ?>">
    <i class="dashicons dashicons-lock"></i>
  </span>
<span class="screen-reader-text"><?php _e('Restricted to', 'age-gate'); ?>: <strong class="age-display"><?php echo $post_meta['age']; ?></strong></span>
<?php else: ?>
  <span data-ag-tooltip="<?php _e('Unrestricted', 'age-gate'); ?>" data-quick-checked="<?php echo $checked; ?>">
    <i class="dashicons dashicons-unlock"></i>
  </span>
<span class="screen-reader-text"><?php _e('Unrestricted', 'age-gate'); ?></span>
<?php endif; ?>