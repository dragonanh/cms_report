<?php

/**
 * FlashSaleLog form base class.
 *
 * @method FlashSaleLog getObject() Returns the current form's model object
 *
 * @package    cms_ctt
 * @subpackage form
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseFlashSaleLogForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'msisdn'          => new sfWidgetFormInputText(),
      'app_code'        => new sfWidgetFormInputText(),
      'flash_sale_id'   => new sfWidgetFormInputText(),
      'pack_code'       => new sfWidgetFormInputText(),
      'serial'          => new sfWidgetFormInputText(),
      'tran_id'         => new sfWidgetFormInputText(),
      'register_status' => new sfWidgetFormInputText(),
      'processed'       => new sfWidgetFormInputText(),
      'card_price'      => new sfWidgetFormInputText(),
      'amount'          => new sfWidgetFormInputText(),
      'base_price'      => new sfWidgetFormInputText(),
      'error_code'      => new sfWidgetFormInputText(),
      'error_message'   => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'msisdn'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_code'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'flash_sale_id'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'pack_code'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'serial'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'tran_id'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'register_status' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'processed'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'card_price'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'amount'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'base_price'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'error_code'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'error_message'   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('flash_sale_log[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'FlashSaleLog';
  }

}
