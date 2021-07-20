<?php

/**
 * VtPaymentDebt filter form base class.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseVtPaymentDebtFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'base_price'     => new sfWidgetFormFilterInput(),
      'status'         => new sfWidgetFormFilterInput(),
      'title'          => new sfWidgetFormFilterInput(),
      'channel_code'   => new sfWidgetFormFilterInput(),
      'utm_source'     => new sfWidgetFormFilterInput(),
      'utm_medium'     => new sfWidgetFormFilterInput(),
      'aff_sid'        => new sfWidgetFormFilterInput(),
      'channel_id'     => new sfWidgetFormFilterInput(),
      'channel_name'   => new sfWidgetFormFilterInput(),
      'channel_type'   => new sfWidgetFormFilterInput(),
      'staff_code'     => new sfWidgetFormFilterInput(),
      'hotline'        => new sfWidgetFormFilterInput(),
      'fb_app_id'      => new sfWidgetFormFilterInput(),
      'price'          => new sfWidgetFormFilterInput(),
      'transaction_id' => new sfWidgetFormFilterInput(),
      'contract_id'    => new sfWidgetFormFilterInput(),
      'payment_status' => new sfWidgetFormFilterInput(),
      'paid_status'    => new sfWidgetFormFilterInput(),
      'debt_begin'     => new sfWidgetFormFilterInput(),
      'service_type'   => new sfWidgetFormFilterInput(),
      'customer_name'  => new sfWidgetFormFilterInput(),
      'content'        => new sfWidgetFormFilterInput(),
      'msisdn'         => new sfWidgetFormFilterInput(),
      'order_type'     => new sfWidgetFormFilterInput(),
      'register_url'   => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'base_price'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'status'         => new sfValidatorPass(array('required' => false)),
      'title'          => new sfValidatorPass(array('required' => false)),
      'channel_code'   => new sfValidatorPass(array('required' => false)),
      'utm_source'     => new sfValidatorPass(array('required' => false)),
      'utm_medium'     => new sfValidatorPass(array('required' => false)),
      'aff_sid'        => new sfValidatorPass(array('required' => false)),
      'channel_id'     => new sfValidatorPass(array('required' => false)),
      'channel_name'   => new sfValidatorPass(array('required' => false)),
      'channel_type'   => new sfValidatorPass(array('required' => false)),
      'staff_code'     => new sfValidatorPass(array('required' => false)),
      'hotline'        => new sfValidatorPass(array('required' => false)),
      'fb_app_id'      => new sfValidatorPass(array('required' => false)),
      'price'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'transaction_id' => new sfValidatorPass(array('required' => false)),
      'contract_id'    => new sfValidatorPass(array('required' => false)),
      'payment_status' => new sfValidatorPass(array('required' => false)),
      'paid_status'    => new sfValidatorPass(array('required' => false)),
      'debt_begin'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'service_type'   => new sfValidatorPass(array('required' => false)),
      'customer_name'  => new sfValidatorPass(array('required' => false)),
      'content'        => new sfValidatorPass(array('required' => false)),
      'msisdn'         => new sfValidatorPass(array('required' => false)),
      'order_type'     => new sfValidatorPass(array('required' => false)),
      'register_url'   => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('vt_payment_debt_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VtPaymentDebt';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'base_price'     => 'Number',
      'status'         => 'Text',
      'title'          => 'Text',
      'channel_code'   => 'Text',
      'utm_source'     => 'Text',
      'utm_medium'     => 'Text',
      'aff_sid'        => 'Text',
      'channel_id'     => 'Text',
      'channel_name'   => 'Text',
      'channel_type'   => 'Text',
      'staff_code'     => 'Text',
      'hotline'        => 'Text',
      'fb_app_id'      => 'Text',
      'price'          => 'Number',
      'transaction_id' => 'Text',
      'contract_id'    => 'Text',
      'payment_status' => 'Text',
      'paid_status'    => 'Text',
      'debt_begin'     => 'Number',
      'service_type'   => 'Text',
      'customer_name'  => 'Text',
      'content'        => 'Text',
      'msisdn'         => 'Text',
      'order_type'     => 'Text',
      'register_url'   => 'Text',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
    );
  }
}
