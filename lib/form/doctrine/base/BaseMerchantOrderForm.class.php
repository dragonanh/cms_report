<?php

/**
 * MerchantOrder form base class.
 *
 * @method MerchantOrder getObject() Returns the current form's model object
 *
 * @package    cms_ctt
 * @subpackage form
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMerchantOrderForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'transaction_id' => new sfWidgetFormInputText(),
      'sub_id'         => new sfWidgetFormInputText(),
      'merchant_code'  => new sfWidgetFormInputText(),
      'myvt_account'   => new sfWidgetFormInputText(),
      'order_time'     => new sfWidgetFormInputText(),
      'status'         => new sfWidgetFormInputText(),
      'payment_status' => new sfWidgetFormInputText(),
      'order_code'     => new sfWidgetFormInputText(),
      'customer_name'  => new sfWidgetFormInputText(),
      'customer_phone' => new sfWidgetFormInputText(),
      'base_price'     => new sfWidgetFormInputText(),
      'price'          => new sfWidgetFormInputText(),
      'product_name'   => new sfWidgetFormInputText(),
      'quantity'       => new sfWidgetFormInputText(),
      'product_price'  => new sfWidgetFormInputText(),
      'category'       => new sfWidgetFormInputText(),
      'discount'       => new sfWidgetFormInputText(),
      'discount_price' => new sfWidgetFormInputText(),
      'is_done'        => new sfWidgetFormInputText(),
      'processed'      => new sfWidgetFormInputText(),
      'trans_type_id'  => new sfWidgetFormInputText(),
      'hold_fee'       => new sfWidgetFormInputText(),
      'pay_gate_fee'   => new sfWidgetFormInputText(),
      'discount_real'  => new sfWidgetFormInputText(),
      'content'        => new sfWidgetFormTextarea(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'transaction_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sub_id'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'merchant_code'  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'myvt_account'   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'order_time'     => new sfValidatorPass(array('required' => false)),
      'status'         => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'payment_status' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'order_code'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'customer_name'  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'customer_phone' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'base_price'     => new sfValidatorInteger(array('required' => false)),
      'price'          => new sfValidatorInteger(array('required' => false)),
      'product_name'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'quantity'       => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'product_price'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'category'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'discount'       => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'discount_price' => new sfValidatorInteger(array('required' => false)),
      'is_done'        => new sfValidatorInteger(array('required' => false)),
      'processed'      => new sfValidatorInteger(array('required' => false)),
      'trans_type_id'  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'hold_fee'       => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'pay_gate_fee'   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'discount_real'  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'content'        => new sfValidatorString(array('required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('merchant_order[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MerchantOrder';
  }

}
