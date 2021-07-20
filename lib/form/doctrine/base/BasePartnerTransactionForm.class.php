<?php

/**
 * PartnerTransaction form base class.
 *
 * @method PartnerTransaction getObject() Returns the current form's model object
 *
 * @package    cms_ctt
 * @subpackage form
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePartnerTransactionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'transaction_id'    => new sfWidgetFormInputText(),
      'description'       => new sfWidgetFormInputText(),
      'myviettel_account' => new sfWidgetFormInputText(),
      'msisdn'            => new sfWidgetFormInputText(),
      'status'            => new sfWidgetFormInputText(),
      'merchant_code'     => new sfWidgetFormInputText(),
      'order_code'        => new sfWidgetFormInputText(),
      'amount'            => new sfWidgetFormInputText(),
      'base_price'        => new sfWidgetFormInputText(),
      'pay_code'          => new sfWidgetFormInputText(),
      'vt_transaction_id' => new sfWidgetFormInputText(),
      'sub_id'            => new sfWidgetFormInputText(),
      'url_redirect'      => new sfWidgetFormTextarea(),
      'request_id'        => new sfWidgetFormInputText(),
      'refund_status'     => new sfWidgetFormInputText(),
      'refund_reason'     => new sfWidgetFormTextarea(),
      'refund_time'       => new sfWidgetFormInputText(),
      'refund_amount'     => new sfWidgetFormInputText(),
      'ip'                => new sfWidgetFormInputText(),
      'status_call_api'   => new sfWidgetFormInputText(),
      'payment_source'    => new sfWidgetFormInputText(),
      'viettelid_point'   => new sfWidgetFormInputText(),
      'status_viettel_id' => new sfWidgetFormInputText(),
      'refund_viettel_id' => new sfWidgetFormInputText(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'transaction_id'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'myviettel_account' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'msisdn'            => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'status'            => new sfValidatorPass(array('required' => false)),
      'merchant_code'     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'order_code'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'amount'            => new sfValidatorPass(array('required' => false)),
      'base_price'        => new sfValidatorPass(array('required' => false)),
      'pay_code'          => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'vt_transaction_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sub_id'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'url_redirect'      => new sfValidatorString(array('required' => false)),
      'request_id'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'refund_status'     => new sfValidatorPass(array('required' => false)),
      'refund_reason'     => new sfValidatorString(array('required' => false)),
      'refund_time'       => new sfValidatorPass(array('required' => false)),
      'refund_amount'     => new sfValidatorPass(array('required' => false)),
      'ip'                => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'status_call_api'   => new sfValidatorString(array('max_length' => 2, 'required' => false)),
      'payment_source'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'viettelid_point'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status_viettel_id' => new sfValidatorPass(array('required' => false)),
      'refund_viettel_id' => new sfValidatorPass(array('required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('partner_transaction[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PartnerTransaction';
  }

}
