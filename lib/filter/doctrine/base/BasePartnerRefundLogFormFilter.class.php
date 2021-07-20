<?php

/**
 * PartnerRefundLog filter form base class.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePartnerRefundLogFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'tran_id'         => new sfWidgetFormFilterInput(),
      'refund_amount'   => new sfWidgetFormFilterInput(),
      'refund_type'     => new sfWidgetFormFilterInput(),
      'reason'          => new sfWidgetFormFilterInput(),
      'username'        => new sfWidgetFormFilterInput(),
      'ip'              => new sfWidgetFormFilterInput(),
      'file_path'       => new sfWidgetFormFilterInput(),
      'viettelid_point' => new sfWidgetFormFilterInput(),
      'pay_code'        => new sfWidgetFormFilterInput(),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'tran_id'         => new sfValidatorPass(array('required' => false)),
      'refund_amount'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'refund_type'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'reason'          => new sfValidatorPass(array('required' => false)),
      'username'        => new sfValidatorPass(array('required' => false)),
      'ip'              => new sfValidatorPass(array('required' => false)),
      'file_path'       => new sfValidatorPass(array('required' => false)),
      'viettelid_point' => new sfValidatorPass(array('required' => false)),
      'pay_code'        => new sfValidatorPass(array('required' => false)),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('partner_refund_log_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PartnerRefundLog';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'tran_id'         => 'Text',
      'refund_amount'   => 'Number',
      'refund_type'     => 'Number',
      'reason'          => 'Text',
      'username'        => 'Text',
      'ip'              => 'Text',
      'file_path'       => 'Text',
      'viettelid_point' => 'Text',
      'pay_code'        => 'Text',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
    );
  }
}
