<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<div class="row-fluid">
    <div class="span9">

      <div class="control-group">
              <?php if ($form->hasGlobalErrors()): ?>
              <?php echo $form->renderGlobalErrors() ?>
              <?php endif; ?>
      
              <form action="<?php echo url_for('vt_ctt_transaction_vtRefundReport_collection', array('action' => 'filter')) ?>" method="post">
                  <div class="span11">
                      <?php $count = count($configuration->getFormFilterFields($form));?>
                      <?php $i = 1;?>
                      <?php echo $form->renderHiddenFields() ?>
                      <?php foreach ($configuration->getFormFilterFields($form) as $name => $field): ?>
                      <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>
                      <?php include_partial('vtRefundReport/filters_field', array(
                      'name'       => $name,
                      'attributes' => $field->getConfig('attributes', array()),
                      'label'      => $field->getConfig('label'),
                      'help'       => $field->getConfig('help'),
                      'form'       => $form,
                      'field'      => $field,
                      'class'      => 'sf_admin_form_row sf_admin_'.strtolower($field->getType()).' sf_admin_filter_field_'.$name, 'filter_text'
                      )) ?>

                      <?php if ($i % 2 == 1): ?>
                        <?php $count = $count - 1; ?>
                      <?php else:?>
                        <div class="clearfix"></div>
                      <?php endif; ?>
                      <?php $i = $i +1?>

                      <?php endforeach; ?>
                    <div class="clearfix"></div>
                    <div class="span2 button" style="height:50px">
                      <input class="btn" type="submit" value="<?php echo __('Tìm kiếm', array(), 'tmcTwitterBootstrapPlugin') ?>" />
                      <?php echo link_to('<i class="icon-remove icon-black"></i> '. __('Hủy', array(), 'tmcTwitterBootstrapPlugin'), 'vt_ctt_transaction_vtRefundReport_collection', array('action' => 'filter'), array('query_string' => '_reset', 'method' => 'post', 'class' => 'btn')) ?></li>

                        <input class="btn btn-primary" type="submit" name="export" value="<?php echo __('Xuất Excel', array(), 'tmcTwitterBootstrapPlugin') ?>"/>

                    </div>
                      
                  </div> 

              </form>
          <form action="<?php echo url_for('@vt_refund_report_import_excel') ?>" method="post" enctype="multipart/form-data">
              <div class="span11">
                  <?php echo $importForm->renderHiddenFields() ?>
                  <table>
                      <tr>
                          <td><b>File giao dịch hoàn tiền (*) <br><?php echo $importForm['file']->render() ?></td>
                          <td><b>File văn bản phê duyệt hoàn tiền </b><br> <?php echo $importForm['image']->render() ?></td>
                          <td><br><button class="btn btn-warning" type="submit" name="_import"><i class="icon-upload icon-black"></i> Hoàn tiền </button></td>
                      </tr>
                      <tr>
                          <td>
                              <?php if ($importForm->hasGlobalErrors()): ?>
                                  <?php echo $importForm->renderGlobalErrors() ?>
                              <?php endif; ?>
                              <?php echo $importForm['file']->hasError() ? $importForm['file']->renderError() : '' ?></td>
                          <td><?php echo $importForm['image']->hasError() ? $importForm['image']->renderError() : '' ?></td>
                          <td>
                          </td>
                      </tr>
                      <tr>
                          <td><a href="<?php echo url_for("@vt_refund_report_download_sample")?>">Tải file mẫu</a></td>
                          <td></td>
                      </tr>
                  </table>
              </div>
          </form>
      </div>
    </div>
</div>
