<?php

/**
 * VtCttTransaction filter form base class.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseVtCttTransactionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'calling'             => new sfWidgetFormFilterInput(),
      'isdn'                => new sfWidgetFormFilterInput(),
      'tran_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('VtVpgTransaction'), 'add_empty' => true)),
      'amount'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'version'             => new sfWidgetFormFilterInput(),
      'description'         => new sfWidgetFormFilterInput(),
      'service_pay'         => new sfWidgetFormFilterInput(),
      'ctt_package'         => new sfWidgetFormFilterInput(),
      'command_code'        => new sfWidgetFormFilterInput(),
      'content'             => new sfWidgetFormFilterInput(),
      'omni_order_code'     => new sfWidgetFormFilterInput(),
      'omni_error_code'     => new sfWidgetFormFilterInput(),
      'omni_order_message'  => new sfWidgetFormFilterInput(),
      'ctt_id'              => new sfWidgetFormFilterInput(),
      'status'              => new sfWidgetFormFilterInput(),
      'ctt_pay_update_time' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'source'              => new sfWidgetFormFilterInput(),
      'order_type'          => new sfWidgetFormFilterInput(),
      'ctt_package_name'    => new sfWidgetFormFilterInput(),
      'sim_number'          => new sfWidgetFormFilterInput(),
      'base_price'          => new sfWidgetFormFilterInput(),
      'service_indicator'   => new sfWidgetFormFilterInput(),
      'refund_error_code'   => new sfWidgetFormFilterInput(),
      'channel'             => new sfWidgetFormFilterInput(),
      'refund_status'       => new sfWidgetFormFilterInput(),
      'bank_code'           => new sfWidgetFormFilterInput(),
      'created_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'calling'             => new sfValidatorPass(array('required' => false)),
      'isdn'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'tran_id'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('VtVpgTransaction'), 'column' => 'id')),
      'amount'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'version'             => new sfValidatorPass(array('required' => false)),
      'description'         => new sfValidatorPass(array('required' => false)),
      'service_pay'         => new sfValidatorPass(array('required' => false)),
      'ctt_package'         => new sfValidatorPass(array('required' => false)),
      'command_code'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'content'             => new sfValidatorPass(array('required' => false)),
      'omni_order_code'     => new sfValidatorPass(array('required' => false)),
      'omni_error_code'     => new sfValidatorPass(array('required' => false)),
      'omni_order_message'  => new sfValidatorPass(array('required' => false)),
      'ctt_id'              => new sfValidatorPass(array('required' => false)),
      'status'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ctt_pay_update_time' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'source'              => new sfValidatorPass(array('required' => false)),
      'order_type'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ctt_package_name'    => new sfValidatorPass(array('required' => false)),
      'sim_number'          => new sfValidatorPass(array('required' => false)),
      'base_price'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'service_indicator'   => new sfValidatorPass(array('required' => false)),
      'refund_error_code'   => new sfValidatorPass(array('required' => false)),
      'channel'             => new sfValidatorPass(array('required' => false)),
      'refund_status'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'bank_code'           => new sfValidatorPass(array('required' => false)),
      'created_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('vt_ctt_transaction_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VtCttTransaction';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'calling'             => 'Text',
      'isdn'                => 'Number',
      'tran_id'             => 'ForeignKey',
      'amount'              => 'Number',
      'version'             => 'Text',
      'description'         => 'Text',
      'service_pay'         => 'Text',
      'ctt_package'         => 'Text',
      'command_code'        => 'Number',
      'content'             => 'Text',
      'omni_order_code'     => 'Text',
      'omni_error_code'     => 'Text',
      'omni_order_message'  => 'Text',
      'ctt_id'              => 'Text',
      'status'              => 'Number',
      'ctt_pay_update_time' => 'Date',
      'source'              => 'Text',
      'order_type'          => 'Number',
      'ctt_package_name'    => 'Text',
      'sim_number'          => 'Text',
      'base_price'          => 'Number',
      'service_indicator'   => 'Text',
      'refund_error_code'   => 'Text',
      'channel'             => 'Text',
      'refund_status'       => 'Number',
      'bank_code'           => 'Text',
      'created_at'          => 'Date',
      'updated_at'          => 'Date',
    );
  }
}
