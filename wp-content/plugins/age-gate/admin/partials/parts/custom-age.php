<?php if(!$this->settings['restrictions']['multi_age']){
	$post_meta['age'] = $this->settings['restrictions']['min_age'];
} ?>
<?php if(current_user_can(AGE_GATE_CAP_SET_CUSTOM_AGE) && $this->settings['restrictions']['multi_age']): ?>
	<div class="misc-pub-section verify-age<?php if (!$post_meta['is_restricted']): ?> unrestricted<?php endif; ?>">
    <span class="info">
      <?php if ($post_meta['is_restricted']): ?>
      <?php _e('Restricted to', 'age-gate'); ?>: <strong class="age-display"><?php echo $post_meta['age']; ?></strong> <button type="button" class="customise-age button-link"><?php _e('Change', 'age-gate'); ?></button>
      <?php else: ?>
      <?php _e('Unrestricted', 'age-gate'); ?>
      <?php endif; ?>
    </span>

	  <div class="custom-age hide-if-js">
	    <label><?php _e('Set age to:', 'age-gate'); ?> <?php echo form_input(array(
	       'name' => 'ag_settings[age]',
	       'type' => 'number',
	       'id' => 'wp_age_gate_min_age'
	     ), $post_meta['age'],
	     array('class' => 'small-text ltr'));
	     ?></label> <button type="button" class="save-custom-age hide-if-no-js button"><?php _e('OK', 'age-gate'); ?></button>
	  </div>
	</div>
<?php else: ?>

  <div class="misc-pub-section verify-age<?php if (!$post_meta['is_restricted']): ?> unrestricted<?php endif; ?>">
    <span class="info" data-age="<?php echo $post_meta['age']; ?>">
      <?php if ($post_meta['is_restricted']): ?>
      <?php _e('Restricted to', 'age-gate'); ?>: <strong class="age-display"><?php echo $post_meta['age']; ?></strong>
      <?php else: ?>
      <?php _e('Unrestricted', 'age-gate'); ?>
      <?php endif; ?>
    </span>


  </div>
<?php endif; ?>