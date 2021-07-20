<?php

/**
 * PartnerTransaction filter form base class.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePartnerTransactionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'transaction_id'    => new sfWidgetFormFilterInput(),
      'description'       => new sfWidgetFormFilterInput(),
      'myviettel_account' => new sfWidgetFormFilterInput(),
      'msisdn'            => new sfWidgetFormFilterInput(),
      'status'            => new sfWidgetFormFilterInput(),
      'merchant_code'     => new sfWidgetFormFilterInput(),
      'order_code'        => new sfWidgetFormFilterInput(),
      'amount'            => new sfWidgetFormFilterInput(),
      'base_price'        => new sfWidgetFormFilterInput(),
      'pay_code'          => new sfWidgetFormFilterInput(),
      'vt_transaction_id' => new sfWidgetFormFilterInput(),
      'sub_id'            => new sfWidgetFormFilterInput(),
      'url_redirect'      => new sfWidgetFormFilterInput(),
      'request_id'        => new sfWidgetFormFilterInput(),
      'refund_status'     => new sfWidgetFormFilterInput(),
      'refund_reason'     => new sfWidgetFormFilterInput(),
      'refund_time'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'refund_amount'     => new sfWidgetFormFilterInput(),
      'ip'                => new sfWidgetFormFilterInput(),
      'status_call_api'   => new sfWidgetFormFilterInput(),
      'payment_source'    => new sfWidgetFormFilterInput(),
      'viettelid_point'   => new sfWidgetFormFilterInput(),
      'status_viettel_id' => new sfWidgetFormFilterInput(),
      'refund_viettel_id' => new sfWidgetFormFilterInput(),
      'created_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'transaction_id'    => new sfValidatorPass(array('required' => false)),
      'description'       => new sfValidatorPass(array('required' => false)),
      'myviettel_account' => new sfValidatorPass(array('required' => false)),
      'msisdn'            => new sfValidatorPass(array('required' => false)),
      'status'            => new sfValidatorPass(array('required' => false)),
      'merchant_code'     => new sfValidatorPass(array('required' => false)),
      'order_code'        => new sfValidatorPass(array('required' => false)),
      'amount'            => new sfValidatorPass(array('required' => false)),
      'base_price'        => new sfValidatorPass(array('required' => false)),
      'pay_code'          => new sfValidatorPass(array('required' => false)),
      'vt_transaction_id' => new sfValidatorPass(array('required' => false)),
      'sub_id'            => new sfValidatorPass(array('required' => false)),
      'url_redirect'      => new sfValidatorPass(array('required' => false)),
      'request_id'        => new sfValidatorPass(array('required' => false)),
      'refund_status'     => new sfValidatorPass(array('required' => false)),
      'refund_reason'     => new sfValidatorPass(array('required' => false)),
      'refund_time'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'refund_amount'     => new sfValidatorPass(array('required' => false)),
      'ip'                => new sfValidatorPass(array('required' => false)),
      'status_call_api'   => new sfValidatorPass(array('required' => false)),
      'payment_source'    => new sfValidatorPass(array('required' => false)),
      'viettelid_point'   => new sfValidatorPass(array('required' => false)),
      'status_viettel_id' => new sfValidatorPass(array('required' => false)),
      'refund_viettel_id' => new sfValidatorPass(array('required' => false)),
      'created_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('partner_transaction_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PartnerTransaction';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'transaction_id'    => 'Text',
      'description'       => 'Text',
      'myviettel_account' => 'Text',
      'msisdn'            => 'Text',
      'status'            => 'Text',
      'merchant_code'     => 'Text',
      'order_code'        => 'Text',
      'amount'            => 'Text',
      'base_price'        => 'Text',
      'pay_code'          => 'Text',
      'vt_transaction_id' => 'Text',
      'sub_id'            => 'Text',
      'url_redirect'      => 'Text',
      'request_id'        => 'Text',
      'refund_status'     => 'Text',
      'refund_reason'     => 'Text',
      'refund_time'       => 'Date',
      'refund_amount'     => 'Text',
      'ip'                => 'Text',
      'status_call_api'   => 'Text',
      'payment_source'    => 'Text',
      'viettelid_point'   => 'Text',
      'status_viettel_id' => 'Text',
      'refund_viettel_id' => 'Text',
      'created_at'        => 'Date',
      'updated_at'        => 'Date',
    );
  }
}
