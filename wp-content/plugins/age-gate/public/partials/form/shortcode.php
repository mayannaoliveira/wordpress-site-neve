<div class="age-gate-sc-wrapper">
    <div class="age-gate-loader">
      <?php $loader = '<svg version="1.1" class="age-gate-loading-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
        <path opacity="0.2" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946 s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634 c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
        <path d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0 C22.32,8.481,24.301,9.057,26.013,10.047z">
          <animateTransform attributeType="xml"
            attributeName="transform"
            type="rotate"
            from="0 20 20"
            to="360 20 20"
            dur="0.5s"
            repeatCount="indefinite"/>
        </path>
      </svg>';

      $loader = apply_filters('age_gate_loading_icon', $loader);
      echo $loader;
      ?>
    </div>
    <div class="age-gate">
      <form method="post" action="" class="age-gate-sc-form" data-action="<?php echo get_rest_url(null, '/age-gate/v1/'); ?>">
        
        <?php if ($atts['title']) : ?>
            <p class="age-gate-inline-message"><?php echo $atts['title']; ?></p>
        <?php endif; ?>
        <?php
        /*
         * Include the relevant form elements
         */

        include AGE_GATE_PATH . "public/partials/form/{$this->settings['restrictions']['input_type']}.php" ?>        

        <?php if ($this->settings['restrictions']['input_type'] !== 'buttons'): ?>
            <input type="submit" value="<?php echo __($this->messages->submit) ?>" class="age-gate-submit">
        <?php endif; ?>

      
        <?php
          // user set "additional content"
         

          // base 64 encode the age just to be a little obsure
          // not really a security thing, just to stop people easily changing
          // it in devtools
          echo form_hidden('age_gate[age]', base64_encode(base64_encode($this->age)));

          echo form_hidden('action', 'age_gate_submit');
          echo form_hidden('ag_sc', '1');

          echo str_replace('id="age_gate[nonce]"', '', wp_nonce_field('age_gate_form', 'age_gate[nonce]', true, false));

          if ($this->settings['restrictions']['input_type'] === 'buttons') {
              echo form_hidden('confirm_action', 0);
          }
        ?>
      </form>
    </div>
    <script type="text/template">
        <?php echo do_shortcode(trim($content)); ?>
    </script>
</div>



