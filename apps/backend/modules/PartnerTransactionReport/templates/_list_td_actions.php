<td nowrap="nowrap" style="">
    <?php if ($partner_transaction->getAmount() > 0 && $partner_transaction->getRefundStatus() != 2 && $partner_transaction->getRefundStatus() != 4 && $partner_transaction->getRefundStatus() != 5) { ?>
        <div class="btn-group btn btn-primary btn-test btnViewDetail" data-id="<?= $partner_transaction->getId(); ?>"
             data-url="<?php echo url_for('partner_transaction_report_refund_money_per_id') ?>"
             data-pay_code="<?php echo $partner_transaction->getPayCode(); ?>"
             data-tran_id="<?php echo $partner_transaction->getTransactionId(); ?>">
            <a href="javascript:void(0)"
            >Hoàn tiền</a>
        </div>
    <?php } ?>
    <?php if ($partner_transaction->getRefundStatus() == 1) { ?>
        <div class="btn-group btn btn-danger btn-test btn-cancel-refund" id="cancelRefund"
             data-id="<?= $partner_transaction->getId(); ?>"
             data-url="<?php echo url_for('partner_transaction_report_cancel_refund') ?>">
            <a href="javascript:void(0)"
            >Từ chối</a>
        </div>
    <?php } ?>
    <?php if($partner_transaction->getViettelidPoint() > 0 && $partner_transaction->getRefundViettelId() != 1){ ?>
    <div class="btn-group btn btn-primary btn-test btnViewPayment" data-id="<?= $partner_transaction->getId(); ?>"
         data-url="<?php echo url_for('partner_transaction_report_refund_point_per_id') ?>"
         data-pay_code="<?php echo $partner_transaction->getPayCode(); ?>"
         data-tran_id="<?php echo $partner_transaction->getTransactionId(); ?>">
        <a href="javascript:void(0)"
        >Hoàn điểm</a>
    </div>
    <?php } ?>
    <?php if($partner_transaction->isVnPayMethod()){ ?>
        <div class="btn-group btn btn-primary btn-test btnShowTrans" data-id="<?= $partner_transaction->getId(); ?>"
             data-url="<?php echo url_for('partner_transaction_view_detail_transaction') ?>"
             data-tran_id="<?php echo $partner_transaction->getTransactionId(); ?>">
            <a href="javascript:void(0)"
            >Tra cứu</a>
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
<script>
    // function myFunction() {
    //     if (confirm("Bạn có chắc chắn muốn từ chối")) {
    //         var postData = $('#cancelRefund').serializeArray();
    //         var uri = $('#cancelRefund').attr('data-url');
    //         var id = $('#cancelRefund').attr('data-id');
    //         alert(id);
    //         $.post(uri, {
    //                 post: postData,
    //                 id: id,
    //             },
    //             function (data) {
    //                 var json = JSON.parse(data);
    //                 if (json.error.length) {
    //                     alert(json.error);
    //                 } else {
    //                     location.reload();
    //                 }
    //             });
    //         return false;
    //     }
    // }
    // $('.btn-cancel-refund').on('click', function () {
    //     var id = $(this).attr('data-id');
    //     alert(id);
    //     $('#modalViewDetail').modal();
    // });
</script>