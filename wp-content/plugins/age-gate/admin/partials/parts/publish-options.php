<?php wp_nonce_field( 'ag_save_post', 'ag_settings[ag_nonce]' ); ?>

<?php include AGE_GATE_PATH . 'admin/partials/parts/custom-age.php'; ?>

<?php if($this->settings['restrictions']['restriction_type'] !== 'selected'): ?>
  <?php if(current_user_can(AGE_GATE_CAP_SET_BYPASS)): ?>
		<div class="misc-pub-section verify-restrict">
	    <label class="selectit">
	      <?php echo form_checkbox(
	        array(
	          'name' => "ag_settings[bypass]",
	          'id' => "age-bypass"
	        ),
	        1, // value
	        checked( 1, get_post_meta( get_the_ID(), '_age_gate-bypass', true ), false ),
					array('class' => 'age-gate-toggle', 'data-type' => 'bypass') // checked
	      ); ?> <?php esc_html_e( 'Do not age restrict this content', 'age-gate' ); ?>
	  	</label>
		</div>
  <?php endif; ?>

<?php else: ?>
  <?php if(current_user_can(AGE_GATE_CAP_SET_CONTENT)): ?>
		<div class="misc-pub-section verify-restrict">
	    <label class="selectit">
	      <?php echo form_checkbox(
	        array(
	          'name' => "ag_settings[restrict]",
	          'id' => "age-restricted"
	        ),
	        1, // value
	        checked( 1, get_post_meta( get_the_ID(), '_age_gate-restrict', true ), false ),
					array('class' => 'age-gate-toggle', 'data-type' => 'restrict') // checked
	      ); ?> <?php _e('Age Gate this content', 'age-gate'); ?>
	  	</label>
		</div>
  <?php endif; ?>

<?php endif; ?>