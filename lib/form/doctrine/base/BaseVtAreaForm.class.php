<?php

/**
 * VtArea form base class.
 *
 * @method VtArea getObject() Returns the current form's model object
 *
 * @package    cms_ctt
 * @subpackage form
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseVtAreaForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'code'          => new sfWidgetFormInputText(),
      'parent_code'   => new sfWidgetFormInputText(),
      'name'          => new sfWidgetFormInputText(),
      'full_name'     => new sfWidgetFormInputText(),
      'status'        => new sfWidgetFormInputText(),
      'province'      => new sfWidgetFormInputText(),
      'district'      => new sfWidgetFormInputText(),
      'precinct'      => new sfWidgetFormInputText(),
      'street_block'  => new sfWidgetFormInputText(),
      'street'        => new sfWidgetFormInputText(),
      'province_name' => new sfWidgetFormInputText(),
      'district_name' => new sfWidgetFormInputText(),
      'precinct_name' => new sfWidgetFormInputText()
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'code'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'parent_code'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'name'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'full_name'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status'        => new sfValidatorInteger(array('required' => false)),
      'province'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'district'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'precinct'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'street_block'  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'street'        => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'province_name' => new sfValidatorInteger(array('required' => false)),
      'district_name' => new sfValidatorInteger(array('required' => false)),
      'precinct_name' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('vt_area[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VtArea';
  }

}
