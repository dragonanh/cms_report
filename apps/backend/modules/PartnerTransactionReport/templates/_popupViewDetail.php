<div class="modal fade viewDetail" id="modalViewDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="width: 900px; left: 37%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="" id="createcontact" method="post" enctype="multipart/form-data">
                    <?php if(!empty($formId)) echo $formId->renderHiddenFields() ?>
                    <div class="error uk-alert"></div>
                    <table style="margin-top: 5px !important; max-width: 800px" id="tblDetailReport"
                           class="datatable table table-bordered table-striped">
                        <tbody>
                        <tr class="sf_admin_row row_originalRequestId">
                            <td class="label-detail">Mã giao dịch thanh toán phía ViettelPay *</td>
                            <td><input class="form-control originalRequestId" type="text"
                                       placeholder="Mã giao dịch thanh toán phía ViettelPay"
                                       name="originalRequestId"></td>
                        </tr>
                        <tr class="sf_admin_row">
                            <td class="label-detail">Hình thức thanh toán
                            </td>
                            <td>
                                <select class="refundType12 refundType" name="refundType">
                                    <option value="0">Hoàn toàn phần</option>
                                    <option value="1">Hoàn một phần</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="sf_admin_row hidden-row hidden">
                            <td class="label-detail">Số tiền *</td>
                            <td><input name="trans_amount" class="field__input form-control trans_amount" placeholder="Số tiền"/>
                            </td>
                        </tr>
                        <tr class="sf_admin_row">
                            <td class="label-detail">Lý do hoàn tiền *</td>
                            <td><input name="trans_content" class="field__input form-control trans_content"
                                       placeholder="Lý do hoàn tiền"/></td>
                        </tr>
                        <tr class="sf_admin_row">
                            <td class="label-detail">Uploadfile</td>
                            <td><input type="file" name="fileUpload" id="fileUpload" class="fileUpload"></td>
                        </tr>
                        </tbody>
                        <tr class="sf_admin_row">
                            <td colspan="2" style="text-align: center"><button type="submit" class="btn btn-primary">Xác nhận
                                </button></td>
                        </tr>

                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade viewDetail" id="modalViewPayment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="width: 900px; left: 37%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="" id="createcontact_2" method="post" enctype="multipart/form-data">
                    <?php if(!empty($formId)) echo $formId->renderHiddenFields() ?>
                    <div class="error uk-alert"></div>
                    <table style="margin-top: 5px !important; max-width: 800px" id="tblDetailReport"
                           class="datatable table table-bordered table-striped">
                        <tbody>
<!--                        <tr class="sf_admin_row row_originalRequestId">-->
<!--                            <td class="label-detail">Mã giao dịch thanh toán phía ViettelPay *</td>-->
<!--                            <td><input class="form-control originalRequestId" type="text"-->
<!--                                       placeholder="Mã giao dịch thanh toán phía ViettelPay"-->
<!--                                       name="originalRequestId"></td>-->
<!--                        </tr>-->
                        <tr class="sf_admin_row">
                            <td class="label-detail">Hình thức thanh toán
                            </td>
                            <td>
                                <select class="refundType123 refundType" name="refundType">
                                    <option value="0">Hoàn toàn phần</option>
                                    <option value="1">Hoàn một phần</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="sf_admin_row hidden-row hidden">
                            <td class="label-detail">Số điểm *</td>
                            <td><input name="trans_amount" class="field__input form-control trans_amount" placeholder="Số điểm"/>
                            </td>
                        </tr>
                        <tr class="sf_admin_row">
                            <td class="label-detail">Lý do hoàn điểm *</td>
                            <td><input name="trans_content" class="field__input form-control trans_content"
                                       placeholder="Lý do hoàn điểm"/></td>
                        </tr>
                        <tr class="sf_admin_row">
                            <td class="label-detail">Uploadfile</td>
                            <td><input type="file" name="fileUpload" id="fileUpload" class="fileUpload"></td>
                        </tr>
                        </tbody>
                        <tr class="sf_admin_row">
                            <td colspan="2" style="text-align: center"><button type="submit" class="btn btn-primary">Xác nhận
                                </button></td>
                        </tr>

                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade viewDetail" id="modalViewDetailTransaction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="width: 900px; left: 37%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo __("Chi tiết đơn hàng") ?></h4>
            </div>
            <div class="modal-body">
                <div id="ajaxListResult">
                    <div class="error uk-alert"></div>
                </div>
            </div>
        </div>
    </div>
</div>