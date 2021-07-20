<?php

/**
 * MerchantOrder filter form.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MerchantOrderAdminFormFilter extends BaseMerchantOrderFormFilter
{
  public function configure()
  {
    $i18n = sfContext::getInstance()->getI18N();
    $this->useFields(['transaction_id','merchant_code', 'order_code']);

    $this->widgetSchema['transaction_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['transaction_id'] = new sfValidatorString([
      'required' => false
    ]);

    $this->widgetSchema['order_code'] = new sfWidgetFormInputText();
    $this->validatorSchema['order_code'] = new sfValidatorString([
      'required' => false
    ]);

    $this->widgetSchema['merchant_code'] = new sfWidgetFormInputText();
    $this->validatorSchema['merchant_code'] = new sfValidatorString([
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
      'merchant_code' => $i18n->__('Mã đối tác'),
      'order_code' => $i18n->__('Mã đơn hàng'),
      'transaction_id' => $i18n->__('Mã giao dịch (mã sinh ra từ My Viettel)'),
    ]);
  }

  protected function doBuildQuery(array $values) {
    $query = parent::doBuildQuery($values);

    if (isset($values['process_time']['from']))
      $query->andWhere('created_at >= ?', $values['process_time']['from']);

    if (isset($values['process_time']['to']))
      $query->andWhere('created_at <= ?', $values['process_time']['to']);

    if (isset($values['merchant_code']) && $values['merchant_code'] !== '') {
      $query->andWhere('merchant_code = ?', $values['merchant_code']);
    }

    if (isset($values['order_code']) && $values['order_code'] !== '') {
      $query->andWhere('order_code = ?', $values['order_code']);
    }

    if (isset($values['transaction_id']) && $values['transaction_id'] !== '') {
      $query->andWhere('transaction_id = ?', $values['transaction_id']);
    }

    $query->orderBy('updated_at DESC');

    return $query;
  }
}
