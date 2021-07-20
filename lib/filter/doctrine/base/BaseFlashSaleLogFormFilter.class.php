<?php

/**
 * FlashSaleLog filter form base class.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseFlashSaleLogFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'msisdn'          => new sfWidgetFormFilterInput(),
      'app_code'        => new sfWidgetFormFilterInput(),
      'flash_sale_id'   => new sfWidgetFormFilterInput(),
      'pack_code'       => new sfWidgetFormFilterInput(),
      'serial'          => new sfWidgetFormFilterInput(),
      'tran_id'         => new sfWidgetFormFilterInput(),
      'register_status' => new sfWidgetFormFilterInput(),
      'processed'       => new sfWidgetFormFilterInput(),
      'card_price'      => new sfWidgetFormFilterInput(),
      'amount'          => new sfWidgetFormFilterInput(),
      'base_price'      => new sfWidgetFormFilterInput(),
      'error_code'      => new sfWidgetFormFilterInput(),
      'error_message'   => new sfWidgetFormFilterInput(),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'msisdn'          => new sfValidatorPass(array('required' => false)),
      'app_code'        => new sfValidatorPass(array('required' => false)),
      'flash_sale_id'   => new sfValidatorPass(array('required' => false)),
      'pack_code'       => new sfValidatorPass(array('required' => false)),
      'serial'          => new sfValidatorPass(array('required' => false)),
      'tran_id'         => new sfValidatorPass(array('required' => false)),
      'register_status' => new sfValidatorPass(array('required' => false)),
      'processed'       => new sfValidatorPass(array('required' => false)),
      'card_price'      => new sfValidatorPass(array('required' => false)),
      'amount'          => new sfValidatorPass(array('required' => false)),
      'base_price'      => new sfValidatorPass(array('required' => false)),
      'error_code'      => new sfValidatorPass(array('required' => false)),
      'error_message'   => new sfValidatorPass(array('required' => false)),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('flash_sale_log_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'FlashSaleLog';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'msisdn'          => 'Text',
      'app_code'        => 'Text',
      'flash_sale_id'   => 'Text',
      'pack_code'       => 'Text',
      'serial'          => 'Text',
      'tran_id'         => 'Text',
      'register_status' => 'Text',
      'processed'       => 'Text',
      'card_price'      => 'Text',
      'amount'          => 'Text',
      'base_price'      => 'Text',
      'error_code'      => 'Text',
      'error_message'   => 'Text',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
    );
  }
}
