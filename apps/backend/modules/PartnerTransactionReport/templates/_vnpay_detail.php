<table style="margin-top: 5px !important; max-width: 800px" id="tblDetailReport" class="datatable table table-bordered table-striped">
  <tbody>
    <tr class="sf_admin_row">
      <td>Mã giao dịch</td>
      <td><?= $resultTrans->vnp_TxnRef ?></td>
    </tr>
    <tr class="sf_admin_row">
      <td>Mã giao dịch phía VNPAY</td>
      <td><?= $resultTrans->vnp_TransactionNo ?></td>
    </tr>
    <tr class="sf_admin_row">
      <td>Số tiền</td>
      <td><?= ($resultTrans->vnp_Amount/100) ?></td>
    </tr>
    <tr class="sf_admin_row">
      <td>Nội dung thanh toán</td>
      <td><?= $resultTrans->vnp_OrderInfo ?></td>
    </tr>
    <tr class="sf_admin_row">
      <td>Kết quả thanh toán</td>
      <td><?= $vnp_TransactionStatus?></td>
    </tr>
    <tr class="sf_admin_row">
      <td>Mã ngân hàng</td>
      <td><?= $resultTrans->vnp_BankCode?></td>
    </tr>
    <tr class="sf_admin_row">
      <td>Ngày thanh toán</td>
      <td><?= $resultTrans->vnp_PayDate?></td>
    </tr>
    <tr class="sf_admin_row">
      <td>Loại giao dịch bên hệ thống VNPAY</td>
      <td><?= $vnp_TransactionType ?></td>
    </tr>
  </tbody>
</table>