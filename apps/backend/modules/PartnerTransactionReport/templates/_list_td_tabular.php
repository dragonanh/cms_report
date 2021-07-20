<td class="sf_admin_text sf_admin_list_td_order_no">
    <?php echo $orderNo; ?>
</td>

<td class="sf_admin_text sf_admin_list_td_transaction_id" field="transaction_id"><?php echo  VtHelper::truncate($partner_transaction->getTransactionId(), 50, '...', true)  ?></td>
<td class="sf_admin_text sf_admin_list_td_msisdn" field="msisdn"><?php echo  VtHelper::truncate($partner_transaction->getMsisdn(), 50, '...', true)  ?></td>
<td class="sf_admin_text sf_admin_list_td_amount" field="amount"><?php echo  VtHelper::truncate($partner_transaction->getAmount(), 50, '...', true)  ?></td>
<td class="sf_admin_text sf_admin_list_td_viettelid_point" field="viettelid_point"><?php echo  VtHelper::truncate($partner_transaction->getViettelidPoint(), 50, '...', true)  ?></td>
<td class="sf_admin_text sf_admin_list_td_description" field="description"><?php echo  VtHelper::truncate($partner_transaction->getDescription(), 50, '...', true)  ?></td>
<td class="sf_admin_text sf_admin_list_td_pay_code" field="pay_code"><?php echo  VtHelper::truncate($partner_transaction->getPayCode(), 50, '...', true)  ?></td>
<td class="sf_admin_text sf_admin_list_td_status" field="status"><?php echo  VtHelper::truncate($partner_transaction->getStatusName(), 50, '...', true)  ?></td>
<td class="sf_admin_text sf_admin_list_td_status_viettel_id" field="status_viettel_id"><?php echo  VtHelper::truncate($partner_transaction->getStatusViettelIdName(), 50, '...', true)  ?></td>
<td class="sf_admin_text sf_admin_list_td_refund_status" field="refund_status"><?php echo  VtHelper::truncate($partner_transaction->getRefundStatusName(), 50, '...', true)  ?></td>
<td class="sf_admin_text sf_admin_list_td_refund_viettel_id" field="refund_viettel_id"><?php echo  VtHelper::truncate($partner_transaction->getRefundViettelIdName(), 50, '...', true)  ?></td>