<?php

/**
 * MerchantOrder filter form base class.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseMerchantOrderFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'transaction_id' => new sfWidgetFormFilterInput(),
      'sub_id'         => new sfWidgetFormFilterInput(),
      'merchant_code'  => new sfWidgetFormFilterInput(),
      'myvt_account'   => new sfWidgetFormFilterInput(),
      'order_time'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'status'         => new sfWidgetFormFilterInput(),
      'payment_status' => new sfWidgetFormFilterInput(),
      'order_code'     => new sfWidgetFormFilterInput(),
      'customer_name'  => new sfWidgetFormFilterInput(),
      'customer_phone' => new sfWidgetFormFilterInput(),
      'base_price'     => new sfWidgetFormFilterInput(),
      'price'          => new sfWidgetFormFilterInput(),
      'product_name'   => new sfWidgetFormFilterInput(),
      'quantity'       => new sfWidgetFormFilterInput(),
      'product_price'  => new sfWidgetFormFilterInput(),
      'category'       => new sfWidgetFormFilterInput(),
      'discount'       => new sfWidgetFormFilterInput(),
      'discount_price' => new sfWidgetFormFilterInput(),
      'is_done'        => new sfWidgetFormFilterInput(),
      'processed'      => new sfWidgetFormFilterInput(),
      'trans_type_id'  => new sfWidgetFormFilterInput(),
      'hold_fee'       => new sfWidgetFormFilterInput(),
      'pay_gate_fee'   => new sfWidgetFormFilterInput(),
      'discount_real'  => new sfWidgetFormFilterInput(),
      'content'        => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'transaction_id' => new sfValidatorPass(array('required' => false)),
      'sub_id'         => new sfValidatorPass(array('required' => false)),
      'merchant_code'  => new sfValidatorPass(array('required' => false)),
      'myvt_account'   => new sfValidatorPass(array('required' => false)),
      'order_time'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'status'         => new sfValidatorPass(array('required' => false)),
      'payment_status' => new sfValidatorPass(array('required' => false)),
      'order_code'     => new sfValidatorPass(array('required' => false)),
      'customer_name'  => new sfValidatorPass(array('required' => false)),
      'customer_phone' => new sfValidatorPass(array('required' => false)),
      'base_price'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'price'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'product_name'   => new sfValidatorPass(array('required' => false)),
      'quantity'       => new sfValidatorPass(array('required' => false)),
      'product_price'  => new sfValidatorPass(array('required' => false)),
      'category'       => new sfValidatorPass(array('required' => false)),
      'discount'       => new sfValidatorPass(array('required' => false)),
      'discount_price' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_done'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'processed'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'trans_type_id'  => new sfValidatorPass(array('required' => false)),
      'hold_fee'       => new sfValidatorPass(array('required' => false)),
      'pay_gate_fee'   => new sfValidatorPass(array('required' => false)),
      'discount_real'  => new sfValidatorPass(array('required' => false)),
      'content'        => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('merchant_order_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MerchantOrder';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'transaction_id' => 'Text',
      'sub_id'         => 'Text',
      'merchant_code'  => 'Text',
      'myvt_account'   => 'Text',
      'order_time'     => 'Date',
      'status'         => 'Text',
      'payment_status' => 'Text',
      'order_code'     => 'Text',
      'customer_name'  => 'Text',
      'customer_phone' => 'Text',
      'base_price'     => 'Number',
      'price'          => 'Number',
      'product_name'   => 'Text',
      'quantity'       => 'Text',
      'product_price'  => 'Text',
      'category'       => 'Text',
      'discount'       => 'Text',
      'discount_price' => 'Number',
      'is_done'        => 'Number',
      'processed'      => 'Number',
      'trans_type_id'  => 'Text',
      'hold_fee'       => 'Text',
      'pay_gate_fee'   => 'Text',
      'discount_real'  => 'Text',
      'content'        => 'Text',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
    );
  }
}
