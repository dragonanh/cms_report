<?php

/**
 * VtVpgTransaction filter form.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VtPackageRevenueReportAdminFormFilter extends BaseVtCttTransactionFormFilter
{
  public function configure()
  {
    $i18n = sfContext::getInstance()->getI18N();
    $this->useFields(['status','isdn','ctt_package','order_type','channel']);

    $channelArr = array('' => $i18n->__('Tất cả')) + VtCttChannelEnum::getArr();
    $this->widgetSchema['channel'] =  new sfWidgetFormChoice(array(
      'choices' => $channelArr
    ));
    $this->validatorSchema['channel'] =  new sfValidatorChoice(array(
      'required' => false, 'choices' => array_keys($channelArr)
    ));

    $statusArr = array('' => $i18n->__('Tất cả'), 0 => $i18n->__('Thành công'), 1 => $i18n->__('Thất bại'));
    $this->widgetSchema['status'] =  new sfWidgetFormChoice(array(
      'choices' => $statusArr
    ));
    $this->validatorSchema['status'] =  new sfValidatorChoice(array(
      'required' => false, 'choices' => array_keys($statusArr)
    ));
    $orderTypeArr = array('' => $i18n->__('Tất cả'), OrderTypeEnum::REGISTER_VAS => 'Vas', OrderTypeEnum::REGISTER_DATA => 'Data');
    $this->widgetSchema['order_type'] = new sfWidgetFormChoice(array(
      'choices' => $orderTypeArr
    ));
    $this->validatorSchema['order_type'] = new sfValidatorChoice([
      'required' => false, 'choices' => array_keys($orderTypeArr)
    ]);

    $this->widgetSchema['isdn'] = new sfWidgetFormInputText();
    $this->validatorSchema['isdn'] = new sfValidatorString([
      'required' => false
    ]);

    $this->widgetSchema['ctt_package'] = new sfWidgetFormInputText();
    $this->validatorSchema['ctt_package'] = new sfValidatorString([
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

    $this->widgetSchema->setLabels([
      'isdn' => $i18n->__('SĐT đăng ký'),
      'order_type' => $i18n->__('Loại dịch vụ'),
    ]);
  }

  protected function doBuildQuery(array $values) {
    $query = parent::doBuildQuery($values)
      ->where('status is not null')
      ->andWhereIn('order_type', [OrderTypeEnum::REGISTER_VAS,OrderTypeEnum::REGISTER_DATA]);

    if (isset($values['process_time']['from']))
      $query->andWhere('updated_at >= ?', $values['process_time']['from']);

    if (isset($values['process_time']['to']))
      $query->andWhere('updated_at <= ?', $values['process_time']['to']);

    if (isset($values['status']) && $values['status'] !== '') {
      if($values['status'] == 0)
        $query->andWhere('omni_error_code = 0');
      else
        $query->andWhere('omni_error_code <> 0');
    }

    if (isset($values['tran_id']) && $values['tran_id'] !== '') {
      $query->andWhere('tran_id = ?', $values['tran_id']);
    }

    if (isset($values['order_type']) && $values['order_type'] !== '') {
      $query->andWhere('order_type = ?', $values['order_type']);
    }

    if (isset($values['ctt_package']) && $values['ctt_package'] !== '') {
      $query->andWhere('ctt_package = ?', $values['ctt_package']);
    }

    if (isset($values['isdn']) && $values['isdn'] !== '') {
      $query->andWhere('isdn = ?', VtHelper::getMobileNumber($values['isdn'], VtHelper::MOBILE_NOTPREFIX));
    }

    if (isset($values['channel']) && $values['channel'] !== '') {
      if($values['channel'] == VtCttChannelEnum::VTPAY)
        $query->andWhere('channel = ?', VtCttChannelEnum::VTPAY);
      else
        $query->andWhere('(channel <> ? OR channel is null)', VtCttChannelEnum::VTPAY);
    }

    $query->orderBy('updated_at DESC');

    return $query;
  }
}
