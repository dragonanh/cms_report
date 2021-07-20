<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('VtPaymentDebt', 'doctrine');

/**
 * BaseVtPaymentDebt
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $base_price
 * @property string $status
 * @property string $title
 * @property string $channel_code
 * @property string $utm_source
 * @property string $utm_medium
 * @property string $aff_sid
 * @property string $channel_id
 * @property string $channel_name
 * @property string $channel_type
 * @property string $staff_code
 * @property string $hotline
 * @property string $fb_app_id
 * @property integer $price
 * @property string $transaction_id
 * @property string $contract_id
 * @property string $payment_status
 * @property string $paid_status
 * @property integer $debt_begin
 * @property string $service_type
 * @property string $customer_name
 * @property string $content
 * @property string $msisdn
 * @property string $order_type
 * @property string $register_url
 * 
 * @method integer       get()               Returns the current record's "base_price" value
 * @method string        get()               Returns the current record's "status" value
 * @method string        get()               Returns the current record's "title" value
 * @method string        get()               Returns the current record's "channel_code" value
 * @method string        get()               Returns the current record's "utm_source" value
 * @method string        get()               Returns the current record's "utm_medium" value
 * @method string        get()               Returns the current record's "aff_sid" value
 * @method string        get()               Returns the current record's "channel_id" value
 * @method string        get()               Returns the current record's "channel_name" value
 * @method string        get()               Returns the current record's "channel_type" value
 * @method string        get()               Returns the current record's "staff_code" value
 * @method string        get()               Returns the current record's "hotline" value
 * @method string        get()               Returns the current record's "fb_app_id" value
 * @method integer       get()               Returns the current record's "price" value
 * @method string        get()               Returns the current record's "transaction_id" value
 * @method string        get()               Returns the current record's "contract_id" value
 * @method string        get()               Returns the current record's "payment_status" value
 * @method string        get()               Returns the current record's "paid_status" value
 * @method integer       get()               Returns the current record's "debt_begin" value
 * @method string        get()               Returns the current record's "service_type" value
 * @method string        get()               Returns the current record's "customer_name" value
 * @method string        get()               Returns the current record's "content" value
 * @method string        get()               Returns the current record's "msisdn" value
 * @method string        get()               Returns the current record's "order_type" value
 * @method string        get()               Returns the current record's "register_url" value
 * @method VtPaymentDebt set()               Sets the current record's "base_price" value
 * @method VtPaymentDebt set()               Sets the current record's "status" value
 * @method VtPaymentDebt set()               Sets the current record's "title" value
 * @method VtPaymentDebt set()               Sets the current record's "channel_code" value
 * @method VtPaymentDebt set()               Sets the current record's "utm_source" value
 * @method VtPaymentDebt set()               Sets the current record's "utm_medium" value
 * @method VtPaymentDebt set()               Sets the current record's "aff_sid" value
 * @method VtPaymentDebt set()               Sets the current record's "channel_id" value
 * @method VtPaymentDebt set()               Sets the current record's "channel_name" value
 * @method VtPaymentDebt set()               Sets the current record's "channel_type" value
 * @method VtPaymentDebt set()               Sets the current record's "staff_code" value
 * @method VtPaymentDebt set()               Sets the current record's "hotline" value
 * @method VtPaymentDebt set()               Sets the current record's "fb_app_id" value
 * @method VtPaymentDebt set()               Sets the current record's "price" value
 * @method VtPaymentDebt set()               Sets the current record's "transaction_id" value
 * @method VtPaymentDebt set()               Sets the current record's "contract_id" value
 * @method VtPaymentDebt set()               Sets the current record's "payment_status" value
 * @method VtPaymentDebt set()               Sets the current record's "paid_status" value
 * @method VtPaymentDebt set()               Sets the current record's "debt_begin" value
 * @method VtPaymentDebt set()               Sets the current record's "service_type" value
 * @method VtPaymentDebt set()               Sets the current record's "customer_name" value
 * @method VtPaymentDebt set()               Sets the current record's "content" value
 * @method VtPaymentDebt set()               Sets the current record's "msisdn" value
 * @method VtPaymentDebt set()               Sets the current record's "order_type" value
 * @method VtPaymentDebt set()               Sets the current record's "register_url" value
 * 
 * @package    cms_ctt
 * @subpackage model
 * @author     viettel
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseVtPaymentDebt extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('vt_payment_debt');
        $this->hasColumn('base_price', 'integer', 20, array(
             'type' => 'integer',
             'comment' => '',
             'length' => 20,
             ));
        $this->hasColumn('status', 'string', 50, array(
             'type' => 'string',
             'comment' => '',
             'length' => 50,
             ));
        $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('channel_code', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('utm_source', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('utm_medium', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('aff_sid', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('channel_id', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('channel_name', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('channel_type', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('staff_code', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('hotline', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('fb_app_id', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('price', 'integer', 20, array(
             'type' => 'integer',
             'comment' => '',
             'length' => 20,
             ));
        $this->hasColumn('transaction_id', 'string', 255, array(
             'type' => 'string',
             'comment' => 'Ma giao dich ben myviettel tu sinh',
             'length' => 255,
             ));
        $this->hasColumn('contract_id', 'string', 50, array(
             'type' => 'string',
             'comment' => '',
             'length' => 50,
             ));
        $this->hasColumn('payment_status', 'string', 50, array(
             'type' => 'string',
             'comment' => 'Trang thai thanh toan',
             'length' => 50,
             ));
        $this->hasColumn('paid_status', 'string', 50, array(
             'type' => 'string',
             'comment' => 'Trang thai tra phi BH',
             'length' => 50,
             ));
        $this->hasColumn('debt_begin', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'No đau ky',
             'length' => 20,
             ));
        $this->hasColumn('service_type', 'string', 255, array(
             'type' => 'string',
             'comment' => 'Loai dich vu',
             'length' => 255,
             ));
        $this->hasColumn('customer_name', 'string', 255, array(
             'type' => 'string',
             'comment' => 'Ten KH',
             'length' => 255,
             ));
        $this->hasColumn('content', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('msisdn', 'string', 50, array(
             'type' => 'string',
             'comment' => '',
             'length' => 50,
             ));
        $this->hasColumn('order_type', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
        $this->hasColumn('register_url', 'string', 255, array(
             'type' => 'string',
             'comment' => '',
             'length' => 255,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}