<?php

/**
 * VtCttRefundLog form base class.
 *
 * @method VtCttRefundLog getObject() Returns the current form's model object
 *
 * @package    cms_ctt
 * @subpackage form
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseVtCttRefundLogForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'tran_id'       => new sfWidgetFormInputText(),
      'refund_amount' => new sfWidgetFormInputText(),
      'refund_type'   => new sfWidgetFormInputText(),
      'reason'        => new sfWidgetFormTextarea(),
      'username'      => new sfWidgetFormInputText(),
      'ip'            => new sfWidgetFormInputText(),
      'file_path'     => new sfWidgetFormInputText(),
      'status'        => new sfWidgetFormInputText(),
      'message'       => new sfWidgetFormTextarea(),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'tran_id'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'refund_amount' => new sfValidatorInteger(array('required' => false)),
      'refund_type'   => new sfValidatorInteger(array('required' => false)),
      'reason'        => new sfValidatorString(array('required' => false)),
      'username'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'ip'            => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'file_path'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status'        => new sfValidatorPass(array('required' => false)),
      'message'       => new sfValidatorString(array('required' => false)),
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('vt_ctt_refund_log[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VtCttRefundLog';
  }

}
