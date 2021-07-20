<?php
include_partial('tmcTwitterBootstrap/assets');
include_component('tmcTwitterBootstrap', 'header');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="span12">
      <h1><?php echo __('Đổi mật khẩu') ?></h1>
      <?php include_partial('flashes') ?>
      <div id="sf_admin_content" style="margin-top: 10px">
        <?php
        if ($form->hasGlobalErrors())
        {
          echo $form->renderGlobalErrors();
        }
        ?>
        <div class="sf_admin_form">
          <?php echo $form->renderFormTag(url_for('@sf_guard_change_password'), array('class' => 'form-horizontal form-inline')) . "\n" ?>
          <?php echo $form->renderHiddenFields() ?>

          <div class="control-group">
            <fieldset id="sf_fieldset_none">
              <?php
              $fields_name = array_keys($form->getFormFieldSchema()->getWidget()->getFields());
              array_pop($fields_name);
              foreach ($fields_name as $field_name):
                $field = $form[$field_name];
                ?>
                <?php ?>
                <div class="control-group sf_admin_form_row sf_admin_text sf_admin_form_field_<?php echo $field_name ?><?php if ($field->hasError()) echo ' error' ?>">
                  <?php echo $field->renderLabel(null, array('class' => 'control-label')) ?>
                  <div class="controls">
                    <div class="field-content">
                      <?php
                      echo $field->render();
                      if ($field->hasError())
                      {
                        echo '<span class="help-inline">' . $field->renderError() . '</span>';
                      }
                      ?>     
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </fieldset>           
          </div>

          <div class="actions">
            <div class="btn-toolbar">
              <?php echo link_to('<i class="icon-arrow-left icon-black"></i> ' . __('Quay lại trang chủ'), '@homepage', array('class' => 'btn btn-small')) ?>
              <input type="submit" name="submit" value="<?php echo __('Save') ?>" class="btn btn-primary">
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_component('tmcTwitterBootstrap', 'footer') ?>