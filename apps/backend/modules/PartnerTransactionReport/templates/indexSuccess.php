<?php use_helper('I18N', 'Date') ?>
<?php include_partial('PartnerTransactionReport/assets') ?>
<?php include_partial('PartnerTransactionReport/header') ?>



<div class="container-fluid">
    <div class="row-fluid">
        <?php if ($sidebar_status): ?>
            <?php include_partial('PartnerTransactionReport/list_sidebar', array('configuration' => $configuration)) ?>
        <?php endif; ?>

        <div class="span<?php echo $sidebar_status ? '10' : '12'; ?>">
            
            <div class="span12">
            <h1 style="display: inline"><?php echo __('Báo cáo hoàn tiền giao dịch merchant', array(), 'messages') ?></h1>
            </div>
            <div class="row-fluid">
                <div class="span9">
                                  <?php include_partial('PartnerTransactionReport/filters', array('form' => $filters, 'configuration' => $configuration, 'importForm' => $importForm)) ?>
                                </div>
                <div class="span3">
                    <div class="pull-right"><?php include_partial('PartnerTransactionReport/list_actions', array('helper' => $helper)) ?></div>
                </div>
            </div>
            

            <?php include_partial('PartnerTransactionReport/flashes') ?>
            
            <div id="sf_admin_header">
                <?php include_partial('PartnerTransactionReport/list_header', array('pager' => $pager)) ?>
            </div>

            <div id="sf_admin_content">
                
                <?php include_partial('PartnerTransactionReport/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>

                <div>
                    <?php include_partial('PartnerTransactionReport/list_batch_actions', array('helper' => $helper)) ?>
                </div>
                
                <form class="form-inline" method="get" action="<?php echo url_for('partner_transaction_PartnerTransactionReport') ?>">
                    <div class="well pull-right">
                      <?php echo __('Số bản ghi/trang: ')?>
                        <?php $select = new sfWidgetFormChoice(array(
                                        'multiple' => false,
                                        'expanded' => false,
                                        'choices' => array('20' => __('20', null, 'tmcTwitterBootstrapPlugin'), 25 => 25, 30 => 30, 50 => 50, 100 => 100)
                                    ),
                                    array('class' => 'input-medium')); echo $select->render('max_per_page', $sf_user->getAttribute('PartnerTransactionReport.max_per_page', null, 'admin_module')) ?>
                        <input type="submit" class="btn btn-inverse btn-small" value="<?php echo __('Go', array(), 'tmcTwitterBootstrapPlugin') ?>" />
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>

            <?php include_partial('PartnerTransactionReport/list_footer', array('pager' => $pager)) ?>
        </div>
    </div>
</div>
<?php include_partial('PartnerTransactionReport/popupViewDetail', array('formId' => $formId))?>
<?php include_partial('PartnerTransactionReport/footer') ?>