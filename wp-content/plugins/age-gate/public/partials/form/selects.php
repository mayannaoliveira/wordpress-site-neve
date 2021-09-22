<ol class="age-gate-form-elements">
  <li class="age-gate-form-section">
    <?php include AGE_GATE_PATH . 'public/partials/form/sections/' . ($this->restrictions->date_format === 'mmddyyyy' ? 'select-month.php' : 'select-day.php'); ?>
  </li>
  <li class="age-gate-form-section">
    <?php include AGE_GATE_PATH . 'public/partials/form/sections/' . ($this->restrictions->date_format === 'mmddyyyy' ? 'select-day.php' : 'select-month.php'); ?>
  </li>
  <li class="age-gate-form-section">
    <label class="age-gate-label" for="age-gate-y"><?php echo ($this->messages->labels->year) ? $this->messages->labels->year : __('Year', 'age-gate'); ?></label>
    <select name="age_gate[y]" id="age-gate-y" class="age-gate-select" required>
      <?php
        $min_year = 1900;
        $min_year = apply_filters('age_gate_select_years', $min_year);
      ?>
      <option value=""><?php _e('YYYY', 'age-gate'); ?></option>
      <?php for ($i = date('Y'); $i >= $min_year; $i--): ?>
      <option value="<?php echo $i; ?>"<?php echo (isset($age['y']) && $age['y'] == $i) ? ' selected' : '' ?>><?php echo $i; ?></option>
      <?php endfor; ?>
    </select>
  </li>
</ol>
<?php echo age_gate_error('date'); ?>
