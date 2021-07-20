<?php

/**
 * VtOmniOrderHis form base class.
 *
 * @method VtOmniOrderHis getObject() Returns the current form's model object
 *
 * @package    cms_ctt
 * @subpackage form
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseVtOmniOrderHisForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'order_code'    => new sfWidgetFormInputText(),
      'msisdn'        => new sfWidgetFormInputText(),
      'error_code'    => new sfWidgetFormInputText(),
      'response'      => new sfWidgetFormTextarea(),
      'order_content' => new sfWidgetFormTextarea(),
      'source'        => new sfWidgetFormInputText(),
      'order_type'    => new sfWidgetFormInputText(),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'order_code'    => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'msisdn'        => new sfValidatorInteger(array('required' => false)),
      'error_code'    => new sfValidatorInteger(array('required' => false)),
      'response'      => new sfValidatorString(array('required' => false)),
      'order_content' => new sfValidatorString(array('required' => false)),
      'source'        => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'order_type'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('vt_omni_order_his[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VtOmniOrderHis';
  }

}
