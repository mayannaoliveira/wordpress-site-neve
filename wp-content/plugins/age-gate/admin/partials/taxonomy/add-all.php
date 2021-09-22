<h2><?php _e('Age restriction', 'age-gate'); ?></h2>

<p><?php _e('Update the settings for the term\'s archive page', 'age-gate'); ?></p>

<div class="form-field term-age-gate-wrap">

  <div class="verify-age">
    <?php if(current_user_can(AGE_GATE_CAP_SET_CUSTOM_AGE) && $this->settings['restrictions']['multi_age']): ?>
    <span class="info">
      <?php _e('Restricted to', 'age-gate'); ?>: <strong class="age-display"><?php echo $this->settings['restrictions']['min_age']; ?></strong> <button type="button" class="customise-age button-link"><?php _e('Change', 'age-gate'); ?></button>
    </span>

    <div class="custom-age hide-if-js">
      <label><?php _e('Set age to', 'age-gate'); ?>: <input type="number" autocomplete="off" name="ag_settings[age]" value="21" id="wp_age_gate_min_age" class="small-text ltr">
      </label> <button type="button" class="save-custom-age hide-if-no-js button"><?php _e('OK', 'age-gate'); ?></button>
    </div>
    <?php else: ?>
      <span class="info" data-age="<?php echo $this->settings['restrictions']['min_age']; ?>">
        <?php _e('Restricted to', 'age-gate'); ?>: <strong class="age-display"><?php echo $this->settings['restrictions']['min_age']; ?></strong>
      </span>
    <?php endif; ?>
  </div>
  <?php if(current_user_can(AGE_GATE_CAP_SET_BYPASS)): ?>
  <div class="verify-restrict">
    <label><input type="checkbox" data-type="bypass" name="ag_settings[bypass]" value="1"> <?php _e('Do not age restrict this content', 'age-gate'); ?></label>
  </div>
  <?php endif; ?>

</div>