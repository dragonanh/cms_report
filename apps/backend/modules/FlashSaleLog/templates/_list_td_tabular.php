        <td class="sf_admin_text sf_admin_list_td_order_no">
        <?php echo $orderNo; ?>
      </td>
    
  <td class="sf_admin_text sf_admin_list_td_msisdn" field="msisdn"><?php echo  VtHelper::truncate($flash_sale_log->getMsisdn(), 50, '...', true)  ?></td>      
  <td class="sf_admin_text sf_admin_list_td_pack_code" field="pack_code"><?php echo  VtHelper::truncate($flash_sale_log->getPackCode(), 50, '...', true)  ?></td>      
  <td class="sf_admin_text sf_admin_list_td_app_code" field="app_code"><?php echo  VtHelper::truncate($flash_sale_log->getAppCode(), 50, '...', true)  ?></td>      
  <td class="sf_admin_text sf_admin_list_td_serial" field="serial"><?php echo  VtHelper::truncate($flash_sale_log->getSerial(), 50, '...', true)  ?></td>      
  <td class="sf_admin_text sf_admin_list_td_register_status" field="register_status"><?php echo  VtHelper::truncate($flash_sale_log->getRegisterStatusName(), 50, '...', true)  ?></td>
  <td class="sf_admin_text sf_admin_list_td_processed" field="processed"><?php echo  VtHelper::truncate($flash_sale_log->getProcessedName(), 50, '...', true)  ?></td>
  <td class="sf_admin_date sf_admin_list_td_created_at" field="created_at"><?php echo  VtHelper::truncate($flash_sale_log->getCreatedAt(), 50, '...', true)  ?></td>    