<td nowrap="nowrap" style="text-align: center; vertical-align: middle;">
    <?php if( $vt_ctt_transaction->getRefundStatusFormat() != 1 && $vt_ctt_transaction->getRefundStatusFormat() != 3 ){ ?>
    <div class="btn-group btn btn-primary btn-test btnViewDetail" data-id="<?= $vt_ctt_transaction->getId(); ?>"
         data-pay_code = "<?= in_array($vt_ctt_transaction->getChannel(), VtCttChannelEnum::listChannelVnpay()) ? 'VNPAY' : 'CTT'; ?>"
         data-url="<?php echo url_for('vt_refund_report_refund_per_id') ?>" data-tran_id ="<?php echo $vt_ctt_transaction->getTransactionId(); ?>">
        <a href="javascript:void(0)"
        >Hoàn tiền</a>
<!--        --><?php //echo link_to(__('Refund', array(), 'messages'), 'vtRefundReport/List_refund?id=' . $vt_ctt_transaction->getId(), array()) ?>
    </div>
    <?php } ?>
</td>
<style>
    .btn-test a {
        color: #fff;
    }
    th.sf_admin_text {
        text-align: center;
    }
</style>