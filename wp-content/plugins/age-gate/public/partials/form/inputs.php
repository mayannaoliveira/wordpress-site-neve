<ol class="age-gate-form-elements">
  <li class="age-gate-form-section">
    <?php include AGE_GATE_PATH . 'public/partials/form/sections/' . ($this->restrictions->date_format === 'mmddyyyy' ? 'input-month.php' : 'input-day.php'); ?>
  </li>
  <li class="age-gate-form-section">
    <?php include AGE_GATE_PATH . 'public/partials/form/sections/' . ($this->restrictions->date_format === 'mmddyyyy' ? 'input-day.php' : 'input-month.php'); ?>
  </li>
  <li class="age-gate-form-section">
    <label class="age-gate-label" for="age-gate-y"><?php echo ($this->messages->labels->year) ? $this->messages->labels->year : __('Year', 'age-gate'); ?></label>
    <input type="text" name="age_gate[y]" class="age-gate-input" id="age-gate-y" value="<?php echo(isset($age['y']) ? $age['y'] : '') ?>" placeholder="<?php _e('YYYY', 'age-gate'); ?>" required minlength="4" maxlength="4" pattern="[0-9]*" inputmode="numeric" autocomplete="off"<?php echo(isset($age['atts']['y']) ? $age['atts']['y'] : '') ?>>
  </li>
</ol>
<?php if ($this->restrictions->date_format === 'mmddyyyy'): ?>
  <?php echo age_gate_error('age_gate_m'); ?>
  <?php echo age_gate_error('age_gate_d'); ?>
<?php else: ?>
  <?php echo age_gate_error('age_gate_d'); ?>
  <?php echo age_gate_error('age_gate_m'); ?>
<?php endif; ?>
<?php echo age_gate_error('age_gate_y'); ?>
<?php echo age_gate_error('date'); ?>
