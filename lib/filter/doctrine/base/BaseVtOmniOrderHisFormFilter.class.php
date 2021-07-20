<?php

/**
 * VtOmniOrderHis filter form base class.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseVtOmniOrderHisFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'order_code'    => new sfWidgetFormFilterInput(),
      'msisdn'        => new sfWidgetFormFilterInput(),
      'error_code'    => new sfWidgetFormFilterInput(),
      'response'      => new sfWidgetFormFilterInput(),
      'order_content' => new sfWidgetFormFilterInput(),
      'source'        => new sfWidgetFormFilterInput(),
      'order_type'    => new sfWidgetFormFilterInput(),
      'created_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'order_code'    => new sfValidatorPass(array('required' => false)),
      'msisdn'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'error_code'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'response'      => new sfValidatorPass(array('required' => false)),
      'order_content' => new sfValidatorPass(array('required' => false)),
      'source'        => new sfValidatorPass(array('required' => false)),
      'order_type'    => new sfValidatorPass(array('required' => false)),
      'created_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('vt_omni_order_his_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VtOmniOrderHis';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'order_code'    => 'Text',
      'msisdn'        => 'Number',
      'error_code'    => 'Number',
      'response'      => 'Text',
      'order_content' => 'Text',
      'source'        => 'Text',
      'order_type'    => 'Text',
      'created_at'    => 'Date',
      'updated_at'    => 'Date',
    );
  }
}
