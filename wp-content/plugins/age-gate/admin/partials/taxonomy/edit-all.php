<?php if (!$this->settings['restrictions']['multi_age']){
	$term_meta['age'] = $this->settings['restrictions']['min_age'];
}
?>

<tr class="form-field term-age-gate-wrap">
	<th scope="row"><?php _e('Age Restriction', 'age-gate'); ?></th>
	<td>
    <p class="description"><?php _e('Update the settings for the term\'s archive page', 'age-gate'); ?></p>
		<input type="hidden" name="ag_settings[]" />
    <?php if(current_user_can(AGE_GATE_CAP_SET_CUSTOM_AGE) && $this->settings['restrictions']['multi_age']): ?>
    	<div class="verify-age<?php if (!$term_meta['is_restricted']): ?> unrestricted<?php endif; ?>">
        <span class="info">
          <?php if ($term_meta['is_restricted']): ?>
          <?php _e('Restricted to', 'age-gate'); ?>: <strong class="age-display"><?php echo $term_meta['age']; ?></strong> <button type="button" class="customise-age button-link"><?php _e('Change', 'age-gate'); ?></button>
          <?php else: ?>
          <?php _e('Unrestricted', 'age-gate'); ?>
          <?php endif; ?>
        </span>

    	  <div class="custom-age hide-if-js">
    	    <label><?php _e('Set age to:', 'age-gate'); ?> <?php echo form_input(array(
    	       'name' => 'ag_settings[age]',
    	       'type' => 'number',
    	       'id' => 'wp_age_gate_min_age'
    	     ), $term_meta['age'],
    	     array('class' => 'small-text ltr'));
    	     ?></label> <button type="button" class="save-custom-age hide-if-no-js button"><?php _e('OK', 'age-gate'); ?></button>
    	  </div>
    	</div>
    <?php else: ?>

      <div class="verify-age<?php if (!$term_meta['is_restricted']): ?> unrestricted<?php endif; ?>">
        <span class="info" data-age="<?php echo $term_meta['age']; ?>">
          <?php if ($term_meta['is_restricted']): ?>
          <?php _e('Restricted to', 'age-gate'); ?>: <strong class="age-display"><?php echo $term_meta['age']; ?></strong>
          <?php else: ?>
          <?php _e('Unrestricted', 'age-gate'); ?>
          <?php endif; ?>
        </span>


      </div>
    <?php endif; ?>





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
    	        checked( 1, get_term_meta( $id, '_age_gate-bypass', true ), false ),
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
    	        checked( 1, get_term_meta( $id, '_age_gate-restrict', true ), false ),
    					array('class' => 'age-gate-toggle', 'data-type' => 'restrict') // checked
    	      ); ?> <?php _e('Age Gate this content', 'age-gate'); ?>
    	  	</label>
    		</div>
      <?php endif; ?>

    <?php endif; ?>


  </td>
</tr>


