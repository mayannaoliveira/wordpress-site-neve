<?php if($this->settings['restrictions']['restriction_type'] !== 'selected'): ?>
  <?php if(current_user_can(AGE_GATE_CAP_SET_BYPASS)): ?>
		<div class="verify-restrict">
	    <label class="selectit">
	      <?php echo form_checkbox(
	        array(
	          'name' => "ag_settings[bypass]",
	          'id' => "age-bypass"
	        ),
	        1, // value
	        false,
					array('class' => 'age-gate-toggle', 'data-type' => 'bypass') // checked
	      ); ?> <?php esc_html_e( 'Do not age restrict this content', 'age-gate' ); ?>
	  	</label>
		</div>
  <?php endif; ?>

<?php else: ?>
  <?php if(current_user_can(AGE_GATE_CAP_SET_CONTENT)): ?>
		<div class="verify-restrict">
	    <label class="selectit">
	      <?php echo form_checkbox(
	        array(
	          'name' => "ag_settings[restrict]",
	          'id' => "age-restricted"
	        ),
	        1, // value
	        false,
					array('class' => 'age-gate-toggle', 'data-type' => 'restrict') // checked
	      ); ?> <?php _e('Age Gate this content', 'age-gate'); ?>
	  	</label>
		</div>
  <?php endif; ?>

<?php endif; ?>