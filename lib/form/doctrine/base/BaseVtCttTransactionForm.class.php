<?php

/**
 * VtCttTransaction form base class.
 *
 * @method VtCttTransaction getObject() Returns the current form's model object
 *
 * @package    cms_ctt
 * @subpackage form
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseVtCttTransactionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'calling'             => new sfWidgetFormInputText(),
      'isdn'                => new sfWidgetFormInputText(),
      'tran_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('VtVpgTransaction'), 'add_empty' => true)),
      'amount'              => new sfWidgetFormInputText(),
      'version'             => new sfWidgetFormInputText(),
      'description'         => new sfWidgetFormInputText(),
      'service_pay'         => new sfWidgetFormInputText(),
      'ctt_package'         => new sfWidgetFormInputText(),
      'command_code'        => new sfWidgetFormInputText(),
      'content'             => new sfWidgetFormTextarea(),
      'omni_order_code'     => new sfWidgetFormInputText(),
      'omni_error_code'     => new sfWidgetFormInputText(),
      'omni_order_message'  => new sfWidgetFormInputText(),
      'ctt_id'              => new sfWidgetFormInputText(),
      'status'              => new sfWidgetFormInputText(),
      'ctt_pay_update_time' => new sfWidgetFormInputText(),
      'source'              => new sfWidgetFormInputText(),
      'order_type'          => new sfWidgetFormInputText(),
      'ctt_package_name'    => new sfWidgetFormInputText(),
      'sim_number'          => new sfWidgetFormInputText(),
      'base_price'          => new sfWidgetFormInputText(),
      'service_indicator'   => new sfWidgetFormInputText(),
      'refund_error_code'   => new sfWidgetFormInputText(),
      'channel'             => new sfWidgetFormInputText(),
      'refund_status'       => new sfWidgetFormInputText(),
      'bank_code'           => new sfWidgetFormInputText(),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'calling'             => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'isdn'                => new sfValidatorInteger(array('required' => false)),
      'tran_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('VtVpgTransaction'), 'required' => false)),
      'amount'              => new sfValidatorInteger(),
      'version'             => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'description'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'service_pay'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'ctt_package'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'command_code'        => new sfValidatorInteger(array('required' => false)),
      'content'             => new sfValidatorString(array('required' => false)),
      'omni_order_code'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'omni_error_code'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'omni_order_message'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'ctt_id'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status'              => new sfValidatorInteger(array('required' => false)),
      'ctt_pay_update_time' => new sfValidatorPass(array('required' => false)),
      'source'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'order_type'          => new sfValidatorInteger(array('required' => false)),
      'ctt_package_name'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sim_number'          => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'base_price'          => new sfValidatorInteger(array('required' => false)),
      'service_indicator'   => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'refund_error_code'   => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'channel'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'refund_status'       => new sfValidatorInteger(array('required' => false)),
      'bank_code'           => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('vt_ctt_transaction[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VtCttTransaction';
  }

}
