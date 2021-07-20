<?php

/**
 * VtCttTransaction filter form.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VtOmniRevenueReportAdminFormFilter extends BaseVtCttTransactionFormFilter
{
  public function configure()
  {
    $i18n = sfContext::getInstance()->getI18N();
    $this->useFields(['status','tran_id','isdn','channel']);

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

    $this->widgetSchema['tran_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['tran_id'] = new sfValidatorString([
      'required' => false
    ]);

    $this->widgetSchema['sim_number'] = new sfWidgetFormInputText();
    $this->validatorSchema['sim_number'] = new sfValidatorString([
      'required' => false
    ]);

    $this->widgetSchema['isdn'] = new sfWidgetFormInputText();
    $this->validatorSchema['isdn'] = new sfValidatorString([
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
      'tran_id' => $i18n->__('Mã thanh toán'),
      'calling' => $i18n->__('Thuê bao thanh toán'),
      'isdn' => $i18n->__('Thuê bao đăng nhập'),
    ]);
  }

  protected function doBuildQuery(array $values) {
    $query = parent::doBuildQuery($values)
      ->andWhere('status = 1')
      ->andWhere('service_pay = "omi"');

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

    if (isset($values['isdn']) && $values['isdn'] !== '') {
      $query->andWhere('isdn = ?', VtHelper::getMobileNumber($values['isdn'], VtHelper::MOBILE_NOTPREFIX));
    }

    if (isset($values['sim_number']) && $values['sim_number'] !== '') {
      $condition = sprintf('"isdn":"%s"',VtHelper::getMobileNumber($values['sim_number'], VtHelper::MOBILE_NOTPREFIX));
      $query->andWhere('content like ?', '%'.VtHelper::translateQuery($condition).'%');
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
