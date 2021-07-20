<?php

/**
 * VtVpgTransaction filter form.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VtTopupRevenueReportAdminFormFilter extends BaseVtVpgTransactionFormFilter
{
  public function configure()
  {
    $i18n = sfContext::getInstance()->getI18N();
    $this->useFields(['status','isdn','calling','ctt_id','tran_id']);

    $statusArr = array('' => $i18n->__('Tất cả')) + VtVpgStatusEnum::getArr();
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

    $this->widgetSchema['calling'] = new sfWidgetFormInputText();
    $this->validatorSchema['calling'] = new sfValidatorString([
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
      'ctt_id' => $i18n->__('Mã giao dịch'),
      'calling' => $i18n->__('Thuê bao thanh toán'),
      'isdn' => $i18n->__('Thuê bao thụ hưởng'),
    ]);
  }

  protected function doBuildQuery(array $values) {
    $query = parent::doBuildQuery($values);

    if (isset($values['process_time']['from']))
      $query->andWhere('created_at >= ?', $values['process_time']['from']);

    if (isset($values['process_time']['to']))
      $query->andWhere('created_at <= ?', $values['process_time']['to']);

    if (isset($values['status']) && $values['status'] !== '') {
      $query->andWhere('status = ?', $values['status']);
    }

    if (isset($values['ctt_id']) && $values['ctt_id'] !== '') {
      $query->andWhere('ctt_id = ?', $values['ctt_id']);
    }

    if (isset($values['tran_id']) && $values['tran_id'] !== '') {
      $query->andWhere('tran_id = ?', $values['tran_id']);
    }

    if (isset($values['isdn']) && $values['isdn'] !== '') {
      $query->andWhere('isdn = ?', VtHelper::getMobileNumber($values['isdn'], VtHelper::MOBILE_GLOBAL));
    }

    if (isset($values['calling']) && $values['calling'] !== '') {
      $query->andWhere('calling = ?', VtHelper::getMobileNumber($values['calling'], VtHelper::MOBILE_GLOBAL));
    }

    $query->orderBy('created_at DESC');

    return $query;
  }
}
