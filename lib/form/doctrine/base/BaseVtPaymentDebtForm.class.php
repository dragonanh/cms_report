<?php

/**
 * VtPaymentDebt form base class.
 *
 * @method VtPaymentDebt getObject() Returns the current form's model object
 *
 * @package    cms_ctt
 * @subpackage form
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseVtPaymentDebtForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'base_price'     => new sfWidgetFormInputText(),
      'status'         => new sfWidgetFormInputText(),
      'title'          => new sfWidgetFormInputText(),
      'channel_code'   => new sfWidgetFormInputText(),
      'utm_source'     => new sfWidgetFormInputText(),
      'utm_medium'     => new sfWidgetFormInputText(),
      'aff_sid'        => new sfWidgetFormInputText(),
      'channel_id'     => new sfWidgetFormInputText(),
      'channel_name'   => new sfWidgetFormInputText(),
      'channel_type'   => new sfWidgetFormInputText(),
      'staff_code'     => new sfWidgetFormInputText(),
      'hotline'        => new sfWidgetFormInputText(),
      'fb_app_id'      => new sfWidgetFormInputText(),
      'price'          => new sfWidgetFormInputText(),
      'transaction_id' => new sfWidgetFormInputText(),
      'contract_id'    => new sfWidgetFormInputText(),
      'payment_status' => new sfWidgetFormInputText(),
      'paid_status'    => new sfWidgetFormInputText(),
      'debt_begin'     => new sfWidgetFormInputText(),
      'service_type'   => new sfWidgetFormInputText(),
      'customer_name'  => new sfWidgetFormInputText(),
      'content'        => new sfWidgetFormInputText(),
      'msisdn'         => new sfWidgetFormInputText(),
      'order_type'     => new sfWidgetFormInputText(),
      'register_url'   => new sfWidgetFormInputText(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'base_price'     => new sfValidatorInteger(array('required' => false)),
      'status'         => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'title'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'channel_code'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'utm_source'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'utm_medium'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'aff_sid'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'channel_id'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'channel_name'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'channel_type'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'staff_code'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'hotline'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'fb_app_id'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'price'          => new sfValidatorInteger(array('required' => false)),
      'transaction_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'contract_id'    => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'payment_status' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'paid_status'    => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'debt_begin'     => new sfValidatorInteger(array('required' => false)),
      'service_type'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'customer_name'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'content'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'msisdn'         => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'order_type'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'register_url'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('vt_payment_debt[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VtPaymentDebt';
  }

}
