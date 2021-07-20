        <td class="sf_admin_text sf_admin_list_td_order_no">
        <?php echo $orderNo; ?>
      </td>
    
  <td class="sf_admin_text sf_admin_list_td_msisdn" field="msisdn"><?php echo  VtHelper::truncate($vt_payment_debt->getMsisdn(), 50, '...', true)  ?></td>      
  <td class="sf_admin_text sf_admin_list_td_order_type" field="order_type"><?php if($vt_payment_debt->getOrderType() == '37'){echo 'Hàm gạch nợ';}elseif ($vt_payment_debt->getOrderType() == '24'){echo 'Hàm topup';}else{echo $vt_payment_debt->getOrderType() == '37';}  ?></td>
  <td class="sf_admin_text sf_admin_list_td_service_type" field="service_type"><?php if($vt_payment_debt->getServiceType() == 1){echo 'Topup';}elseif ($vt_payment_debt->getServiceType() == 2){echo 'Thanh toán cước di động trả sau';}else{echo 'Thanh toán cước di dộng cố định';}  ?></td>
  <td class="sf_admin_text sf_admin_list_td_price" field="price"><?php echo  VtHelper::truncate($vt_payment_debt->getPrice(), 50, '...', true)  ?></td>      
  <td class="sf_admin_text sf_admin_list_td_status" field="status"><?php if($vt_payment_debt->getStatus() === '0'){echo 'Thất bại';}elseif($vt_payment_debt->getStatus() == 1){echo 'Thành công';}else{echo $vt_payment_debt->getStatus();}  ?></td>
  <td class="sf_admin_text sf_admin_list_td_staff_code" field="staff_code"><?php echo  VtHelper::truncate($vt_payment_debt->getStaffCode(), 50, '...', true)  ?></td>      
  <td class="sf_admin_date sf_admin_list_td_created_at" field="created_at"><?php echo  VtHelper::truncate($vt_payment_debt->getCreatedAt(), 50, '...', true)  ?></td>      
  <td class="sf_admin_text sf_admin_list_td_utm_source" field="utm_source"><?php echo  VtHelper::truncate($vt_payment_debt->getUtmSource(), 50, '...', true)  ?></td>      
  <td class="sf_admin_text sf_admin_list_td_aff_sid" field="aff_sid"><?php echo  VtHelper::truncate($vt_payment_debt->getAffSid(), 50, '...', true)  ?></td>      
  <td class="sf_admin_text sf_admin_list_td_utm_medium" field="utm_medium"><?php echo  VtHelper::truncate($vt_payment_debt->getUtmMedium(), 50, '...', true)  ?></td>    