<?php

/**
 * VtCttTransaction filter form.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VtRefundReportAdminFormFilter extends BaseVtCttTransactionFormFilter
{
  public function configure()
  {
    $i18n = sfContext::getInstance()->getI18N();
    $this->useFields(['isdn','order_type', 'omni_error_code','status','tran_id','ctt_id','refund_status','channel']);

    $omniErrorCodeArr = array('' => $i18n->__('Tất cả'), 0 => $i18n->__('Thành công'), 1 => $i18n->__('Thất bại'));
    $this->widgetSchema['omni_error_code'] =  new sfWidgetFormChoice(array(
      'choices' => $omniErrorCodeArr
    ));
    $this->validatorSchema['omni_error_code'] =  new sfValidatorChoice(array(
      'required' => false, 'choices' => array_keys($omniErrorCodeArr)
    ));

    $statusArr = array('' => $i18n->__('Tất cả'), 1 => $i18n->__('Thành công'), 2 => $i18n->__('Thất bại'), 3 => $i18n->__('Đang xử lý'));
    $this->widgetSchema['status'] =  new sfWidgetFormChoice(array(
      'choices' => $statusArr
    ));
    $this->validatorSchema['status'] =  new sfValidatorChoice(array(
      'required' => false, 'choices' => array_keys($statusArr)
    ));

    $this->widgetSchema['isdn'] = new sfWidgetFormInputText();
    $this->validatorSchema['isdn'] = new sfValidatorString([
      'required' => false
    ]);

    $this->widgetSchema['tran_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['tran_id'] = new sfValidatorString([
      'required' => false
    ]);

    $this->widgetSchema['ctt_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['ctt_id'] = new sfValidatorString([
      'required' => false
    ]);

    $orderTypeArr = array('' => $i18n->__('Tất cả')) + OrderTypeEnum::getArr();
    $this->widgetSchema['order_type'] =  new sfWidgetFormChoice(array(
      'choices' => $orderTypeArr
    ));
    $this->validatorSchema['order_type'] =  new sfValidatorChoice(array(
      'required' => false, 'choices' => array_keys($orderTypeArr)
    ));

      $refundStatusArr = array('' => $i18n->__('Tất cả'),0 => $i18n->__('Chờ xác nhận hoàn tiền'), 1 => $i18n->__('Hoàn tiền thành công'), 2 => $i18n->__('Hoàn tiền thất bại'), 3 => $i18n->__('Gửi hoàn tiền thành công'), 4 => $i18n->__('Gửi hoàn tiền thất bại'));
      $this->widgetSchema['refund_status'] =  new sfWidgetFormChoice(array(
          'choices' => $refundStatusArr
      ));
      $this->validatorSchema['refund_status'] =  new sfValidatorChoice(array(
          'required' => false, 'choices' => array_keys($refundStatusArr)
      ));


    $this->widgetSchema['tran_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['tran_id'] = new sfValidatorString([
      'required' => false
    ]);
	


    $this->widgetSchema['process_time'] = new sfWidgetFormFilterDate(array(
      'from_date' => new sfWidgetFormVnDatePicker(array('jq_picker_options' => array('changeMonth' => true, 'changeYear' => true)), array('readonly' => 'readonly')),
      'to_date' => new sfWidgetFormVnDatePicker(array('jq_picker_options' => array('changeMonth' => true, 'changeYear' => true)), array('readonly' => 'readonly')),
      'template' => '%from_date% '. $i18n->__('đến ngày') .' %to_date%',
      'with_empty' => false,
    ));
    $this->validatorSchema['process_time'] = new sfValidatorDateRange(
      array('required' => false,
        'from_date' => new sfValidatorVnDateTime(
          array(
            'required' => false,
            'datetime_output' => 'Y-m-d 00:00:00'
          ), array(
            'required' => $i18n->__('Vui lòng chọn khoảng thời gian muốn tra cứu')
          )
        ),
        'to_date' => new sfValidatorVnDateTime(
          array(
            'required' => false,
            'datetime_output' => 'Y-m-d 23:59:59'
          ), array(
            'required' => $i18n->__('Vui lòng chọn khoảng thời gian muốn tra cứu')
          )
        )),
      array(
        'invalid' => $i18n->__('Từ ngày phải nhỏ hơn hoặc bằng đến ngày')
      )
    );
      if (!$this->getOption('is_import')) {

      } else {
          $this->disableCSRFProtection();
      }

      $channelArr = ["" => "Tất cả"] + VtCttChannelEnum::getArr();
      $this->widgetSchema['channel'] =  new sfWidgetFormChoice(array(
          'choices' => $channelArr
      ));
      $this->validatorSchema['channel'] =  new sfValidatorChoice(array(
          'required' => false, 'choices' => array_keys($channelArr)
      ));

    $this->widgetSchema->setLabels([
      'refund_status' => $i18n->__('Trạng thái hoàn tiền'),
      'isdn' => $i18n->__('SĐT được cung cấp dịch vụ'),
      'omni_error_code' => $i18n->__('Trạng thái cung cấp dịch vụ'),
      'tran_id' => $i18n->__('Mã thanh toán (mã sinh ra từ My Viettel)'),
      'order_type' => $i18n->__('Loại giao dịch'),
      'ctt_id' => $i18n->__('Mã giao dịch CTT/VTPAY/VnPay'),
      'channel' => $i18n->__('Nguồn tiền'),
      'status' => $i18n->__('Trạng thái trừ tiền'),
    ]);
  }

  protected function doBuildQuery(array $values) {
    $query = parent::doBuildQuery($values);

    $alias = $query->getRootAlias();
    $query->select($alias.'.tran_id transaction_id, *')
          ->leftJoin($alias.'.VtVpgTransaction vpg');
    $fromTime = !empty($values['process_time']['from']) ? $values['process_time']['from'] : date('Y-m-d', strtotime('now'));
//    if (isset($values['process_time']['from']))
      $query->andWhere($alias.'.created_at >= ?', $fromTime);

    if (isset($values['process_time']['to']))
      $query->andWhere($alias.'.created_at <= ?', $values['process_time']['to']);

    if (isset($values['omni_error_code']) && $values['omni_error_code'] !== '') {
      if($values['omni_error_code'] == 0)
        $query->andWhere('(omni_error_code = "0" OR vpg.status = 1)');
      else
        $query->andWhere(sprintf('((%s.status = 1 OR %s.status = 3) AND (omni_error_code <> "0" OR omni_error_code is null)) and (vpg.error_code is null or vpg.status <> 1)', $alias, $alias));
    }
      if (isset($values['channel']) && $values['channel'] !== '') {
          if($values['channel'] == VtCttChannelEnum::OTHER){
              $query->andWhere(sprintf('(%s.channel is null OR %s.channel = "")', $alias, $alias));
          }else{
              $query->andWhere($alias.'.channel = ?', $values['channel']);
          }
      }

    if (isset($values['isdn']) && $values['isdn'] !== '') {
      $query->andWhere($alias.'.isdn like ?', '%'.VtHelper::getMobileNumber($values['isdn'], VtHelper::MOBILE_NOTPREFIX).'%');
    }

    if (isset($values['tran_id']) && $values['tran_id'] !== '') {
      $query->andWhere($alias.'.tran_id = ?', $values['tran_id']);
    }

    if (isset($values['ctt_id']) && $values['ctt_id'] !== '') {
      $query->andWhere($alias.'.ctt_id = ?', $values['ctt_id']);
    }

    if (isset($values['status']) && $values['status'] !== '') {
      switch ($values['status']){
        case 1:
          $query->andWhereIn($alias.'.status', [1,3]); break;
        case 2:
          $query->andWhere($alias.'.status = 0'); break;
        default:
          $query->andWhere($alias.'.status is null'); break;
      }
    }

    if (isset($values['order_type']) && $values['order_type'] !== '') {
      $query->andWhere($alias.'.order_type = ?', $values['order_type']);
    }

      if (isset($values['refund_status']) && $values['refund_status'] !== '') {
          if($values['refund_status'] == 0)
              $query->andWhere('refund_status is null');
          else
              $query->andWhere('refund_status = ?', $values['refund_status']);
      }


    $query->orderBy('created_at DESC');
    return $query;
  }
}
