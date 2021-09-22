<label class="age-gate-label" for="age-gate-m"><?php echo ($this->messages->labels->month) ? $this->messages->labels->month : __('Month', 'age-gate'); ?></label>
<select name="age_gate[m]" id="age-gate-m" class="age-gate-select" required>
  <option value=""><?php _e('MM', 'age-gate'); ?></option>
  <option value="01"<?php echo (isset($age['m']) && $age['m'] == '01') ? ' selected' : '' ?>><?php _e('Jan', 'age-gate'); ?></option>
  <option value="02"<?php echo (isset($age['m']) && $age['m'] == '02') ? ' selected' : '' ?>><?php _e('Feb', 'age-gate'); ?></option>
  <option value="03"<?php echo (isset($age['m']) && $age['m'] == '03') ? ' selected' : '' ?>><?php _e('Mar', 'age-gate'); ?></option>
  <option value="04"<?php echo (isset($age['m']) && $age['m'] == '04') ? ' selected' : '' ?>><?php _e('Apr', 'age-gate'); ?></option>
  <option value="05"<?php echo (isset($age['m']) && $age['m'] == '05') ? ' selected' : '' ?>><?php _e('May', 'age-gate'); ?></option>
  <option value="06"<?php echo (isset($age['m']) && $age['m'] == '06') ? ' selected' : '' ?>><?php _e('Jun', 'age-gate'); ?></option>
  <option value="07"<?php echo (isset($age['m']) && $age['m'] == '07') ? ' selected' : '' ?>><?php _e('Jul', 'age-gate'); ?></option>
  <option value="08"<?php echo (isset($age['m']) && $age['m'] == '08') ? ' selected' : '' ?>><?php _e('Aug', 'age-gate'); ?></option>
  <option value="09"<?php echo (isset($age['m']) && $age['m'] == '09') ? ' selected' : '' ?>><?php _e('Sep', 'age-gate'); ?></option>
  <option value="10"<?php echo (isset($age['m']) && $age['m'] == '10') ? ' selected' : '' ?>><?php _e('Oct', 'age-gate'); ?></option>
  <option value="11"<?php echo (isset($age['m']) && $age['m'] == '11') ? ' selected' : '' ?>><?php _e('Nov', 'age-gate'); ?></option>
  <option value="12"<?php echo (isset($age['m']) && $age['m'] == '12') ? ' selected' : '' ?>><?php _e('Dec', 'age-gate'); ?></option>
</select>