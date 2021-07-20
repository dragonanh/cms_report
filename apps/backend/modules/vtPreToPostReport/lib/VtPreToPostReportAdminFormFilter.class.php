<?php

/**
 * VtVpgTransaction filter form.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VtPreToPostReportAdminFormFilter extends BaseVtCttTransactionFormFilter
{
  public function configure()
  {
    $i18n = sfContext::getInstance()->getI18N();
    $this->useFields(['isdn', 'omni_error_code']);

//    $serviceType = array('' => $i18n->__('Tất cả'), 1 => $i18n->__('Di động/dcom/homephone'), 2 => $i18n->__('Cố định'));
//    $this->widgetSchema['service_type'] =  new sfWidgetFormChoice(array(
//      'choices' => $serviceType
//    ));
//    $this->validatorSchema['service_type'] =  new sfValidatorChoice(array(
//      'required' => false, 'choices' => array_keys($serviceType)
//    ));

    $omniErrorCodeArr = array('' => $i18n->__('Tất cả'), 0 => $i18n->__('Thành công'), 1 => $i18n->__('Thất bại'));
    $this->widgetSchema['omni_error_code'] =  new sfWidgetFormChoice(array(
      'choices' => $omniErrorCodeArr
    ));
    $this->validatorSchema['omni_error_code'] =  new sfValidatorChoice(array(
      'required' => false, 'choices' => array_keys($omniErrorCodeArr)
    ));

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
      'isdn' => $i18n->__('SĐT chuyển đổi'),
      'omni_error_code' => $i18n->__('Trạng thái chuyển đổi'),
//      'service_type' => $i18n->__('Dịch vụ'),
    ]);
  }

  protected function doBuildQuery(array $values) {
    $query = parent::doBuildQuery($values);
    $alias = $query->getRootAlias();

    $query->select($alias.'.tran_id transaction_id, *')
      ->leftJoin($alias.'.VtVpgTransaction v')
      ->andWhere('order_type = ?', OrderTypeEnum::PRE_TO_POST);

    if (isset($values['process_time']['from']))
      $query->andWhere('created_at >= ?', $values['process_time']['from']);

    if (isset($values['process_time']['to']))
      $query->andWhere('created_at <= ?', $values['process_time']['to']);

    if (isset($values['status']) && $values['status'] !== '') {
      if($values['status'] == VtCttStatusEnum::PROCESSING)
        $query->andWhere('v.status is null');
      else
        $query->andWhere('v.status = ?', $values['status']);
    }

//    if (!empty($values['service_type'])) {
//      if($values['service_type'] == 1)
//        $query->andWhereIn($alias.'.service_indicator', ["100000","200000"]);
//      else
//        $query->andWhereNotIn($alias.'.service_indicator', ["100000","200000"]);
//    }

    if (isset($values['omni_error_code']) && $values['omni_error_code'] !== '') {
      if($values['omni_error_code'] == 0)
        $query->andWhere('(omni_error_code = 0 OR v.error_code = "00")');
      else
        $query->andWhere('(omni_error_code <> 0 AND (v.error_code is null OR v.error_code <> "00"))');
    }

    if (isset($values['isdn']) && $values['isdn'] !== '') {
      $query->andWhere('isdn = ?', $values['isdn']);
    }

    $query->orderBy('created_at DESC');

    return $query;
  }
}
