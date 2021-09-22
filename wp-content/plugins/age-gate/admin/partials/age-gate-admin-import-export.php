<div class="wrap">
  <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

  <?php //include AGE_GATE_PATH . 'admin/partials/parts/tabs.php'; ?>
  <div id="normal-sortables" class="meta-box-sortables ui-sortable">
    <div class="ag-grid">
      <div class="column">
        <h3><?php _e('Export Settings', 'age-gate'); ?></h3>

        <form method="post">
          <input type="hidden" name="ag_import_action" value="export" />
          <?php wp_nonce_field( 'ag_export', 'ag_export' ) ?>
          <fieldset>
              <table class="form-table">
                  <tr>
                      <th><?php _e('All settings', 'age-gate') ?></th>
                      <td>
                        <label class="ag-switch">
                            <input type="checkbox" name="ag_settings[pass]" class="ag-toggle-all">
                            <span class="ag-switch__slider"></span>
                        </label>
                      </td>
                  </tr>
                  <?php foreach ($this->_exportOptions as $key => $value): ?>
                  <tr>
                      <th><?php echo $value['label'] ?></th>
                      <td>
                        <label class="ag-switch">
                            <input type="checkbox" name="ag_setting[]" value="<?php echo $key ?>" />
                            <span class="ag-switch__slider"></span>
                        </label>
                        </td>
                    </tr>
                  <?php endforeach; ?>
                </table>
            

            
            
          </fieldset>
          <button type="submit" class="button" name="ag_post"><?php _e('Export', 'age-gate'); ?></button>
        </form>


      </div>
      <div class="column">
        <h3><?php _e('Import Settings', 'age-gate'); ?></h3>
        <form method="post" enctype="multipart/form-data">
          <?php wp_nonce_field( 'ag_import', 'ag_import' ) ?>
          <input type="hidden" name="ag_import_action" value="import" />
          <input type="file" name="ag_json" value="">
          <button type="submit" class="button" name="ag_post"><?php _e('Import', 'age-gate'); ?></button>

        </form>
      </div>
    </div>
  </div>
</div>
