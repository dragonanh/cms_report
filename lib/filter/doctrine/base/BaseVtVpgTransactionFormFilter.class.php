<?php

/**
 * VtVpgTransaction filter form base class.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseVtVpgTransactionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'calling'           => new sfWidgetFormFilterInput(),
      'isdn'              => new sfWidgetFormFilterInput(),
      'tran_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('VtCttTransaction'), 'add_empty' => true)),
      'amount'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'ctt_id'            => new sfWidgetFormFilterInput(),
      'status'            => new sfWidgetFormFilterInput(),
      'error_code'        => new sfWidgetFormFilterInput(),
      'error_message'     => new sfWidgetFormFilterInput(),
      'is_refund'         => new sfWidgetFormFilterInput(),
      'retry'             => new sfWidgetFormFilterInput(),
      'channel'           => new sfWidgetFormFilterInput(),
      'isdn_login'        => new sfWidgetFormFilterInput(),
      'order_time'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'service_indicator' => new sfWidgetFormFilterInput(),
      'created_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'calling'           => new sfValidatorPass(array('required' => false)),
      'isdn'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'tran_id'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('VtCttTransaction'), 'column' => 'id')),
      'amount'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ctt_id'            => new sfValidatorPass(array('required' => false)),
      'status'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'error_code'        => new sfValidatorPass(array('required' => false)),
      'error_message'     => new sfValidatorPass(array('required' => false)),
      'is_refund'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'retry'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'channel'           => new sfValidatorPass(array('required' => false)),
      'isdn_login'        => new sfValidatorPass(array('required' => false)),
      'order_time'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'service_indicator' => new sfValidatorPass(array('required' => false)),
      'created_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('vt_vpg_transaction_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VtVpgTransaction';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'calling'           => 'Text',
      'isdn'              => 'Number',
      'tran_id'           => 'ForeignKey',
      'amount'            => 'Number',
      'ctt_id'            => 'Text',
      'status'            => 'Number',
      'error_code'        => 'Text',
      'error_message'     => 'Text',
      'is_refund'         => 'Number',
      'retry'             => 'Number',
      'channel'           => 'Text',
      'isdn_login'        => 'Text',
      'order_time'        => 'Date',
      'service_indicator' => 'Text',
      'created_at'        => 'Date',
      'updated_at'        => 'Date',
    );
  }
}
