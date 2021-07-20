<?php

/**
 * VtVpgTransaction form base class.
 *
 * @method VtVpgTransaction getObject() Returns the current form's model object
 *
 * @package    cms_ctt
 * @subpackage form
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseVtVpgTransactionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'calling'           => new sfWidgetFormInputText(),
      'isdn'              => new sfWidgetFormInputText(),
      'tran_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('VtCttTransaction'), 'add_empty' => true)),
      'amount'            => new sfWidgetFormInputText(),
      'ctt_id'            => new sfWidgetFormInputText(),
      'status'            => new sfWidgetFormInputText(),
      'error_code'        => new sfWidgetFormInputText(),
      'error_message'     => new sfWidgetFormInputText(),
      'is_refund'         => new sfWidgetFormInputText(),
      'retry'             => new sfWidgetFormInputText(),
      'channel'           => new sfWidgetFormInputText(),
      'isdn_login'        => new sfWidgetFormInputText(),
      'order_time'        => new sfWidgetFormInputText(),
      'service_indicator' => new sfWidgetFormInputText(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'calling'           => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'isdn'              => new sfValidatorInteger(array('required' => false)),
      'tran_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('VtCttTransaction'), 'required' => false)),
      'amount'            => new sfValidatorInteger(),
      'ctt_id'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status'            => new sfValidatorInteger(array('required' => false)),
      'error_code'        => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'error_message'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'is_refund'         => new sfValidatorInteger(array('required' => false)),
      'retry'             => new sfValidatorInteger(array('required' => false)),
      'channel'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'isdn_login'        => new sfValidatorString(array('max_length' => 15, 'required' => false)),
      'order_time'        => new sfValidatorPass(array('required' => false)),
      'service_indicator' => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('vt_vpg_transaction[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VtVpgTransaction';
  }

}
