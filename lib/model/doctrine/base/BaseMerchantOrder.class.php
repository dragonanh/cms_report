<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('MerchantOrder', 'doctrine');

/**
 * BaseMerchantOrder
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $transaction_id
 * @property string $sub_id
 * @property string $merchant_code
 * @property string $myvt_account
 * @property datetime $order_time
 * @property string $status
 * @property string $payment_status
 * @property string $order_code
 * @property string $customer_name
 * @property string $customer_phone
 * @property integer $base_price
 * @property integer $price
 * @property string $product_name
 * @property string $quantity
 * @property string $product_price
 * @property string $category
 * @property string $discount
 * @property integer $discount_price
 * @property integer $is_done
 * @property integer $processed
 * @property string $trans_type_id
 * @property string $hold_fee
 * @property string $pay_gate_fee
 * @property string $discount_real
 * @property clob $content
 * 
 * @method string        getTransactionId()  Returns the current record's "transaction_id" value
 * @method string        getSubId()          Returns the current record's "sub_id" value
 * @method string        getMerchantCode()   Returns the current record's "merchant_code" value
 * @method string        getMyvtAccount()    Returns the current record's "myvt_account" value
 * @method datetime      getOrderTime()      Returns the current record's "order_time" value
 * @method string        getStatus()         Returns the current record's "status" value
 * @method string        getPaymentStatus()  Returns the current record's "payment_status" value
 * @method string        getOrderCode()      Returns the current record's "order_code" value
 * @method string        getCustomerName()   Returns the current record's "customer_name" value
 * @method string        getCustomerPhone()  Returns the current record's "customer_phone" value
 * @method integer       getBasePrice()      Returns the current record's "base_price" value
 * @method integer       getPrice()          Returns the current record's "price" value
 * @method string        getProductName()    Returns the current record's "product_name" value
 * @method string        getQuantity()       Returns the current record's "quantity" value
 * @method string        getProductPrice()   Returns the current record's "product_price" value
 * @method string        getCategory()       Returns the current record's "category" value
 * @method string        getDiscount()       Returns the current record's "discount" value
 * @method integer       getDiscountPrice()  Returns the current record's "discount_price" value
 * @method integer       getIsDone()         Returns the current record's "is_done" value
 * @method integer       getProcessed()      Returns the current record's "processed" value
 * @method string        getTransTypeId()    Returns the current record's "trans_type_id" value
 * @method string        getHoldFee()        Returns the current record's "hold_fee" value
 * @method string        getPayGateFee()     Returns the current record's "pay_gate_fee" value
 * @method string        getDiscountReal()   Returns the current record's "discount_real" value
 * @method clob          getContent()        Returns the current record's "content" value
 * @method MerchantOrder setTransactionId()  Sets the current record's "transaction_id" value
 * @method MerchantOrder setSubId()          Sets the current record's "sub_id" value
 * @method MerchantOrder setMerchantCode()   Sets the current record's "merchant_code" value
 * @method MerchantOrder setMyvtAccount()    Sets the current record's "myvt_account" value
 * @method MerchantOrder setOrderTime()      Sets the current record's "order_time" value
 * @method MerchantOrder setStatus()         Sets the current record's "status" value
 * @method MerchantOrder setPaymentStatus()  Sets the current record's "payment_status" value
 * @method MerchantOrder setOrderCode()      Sets the current record's "order_code" value
 * @method MerchantOrder setCustomerName()   Sets the current record's "customer_name" value
 * @method MerchantOrder setCustomerPhone()  Sets the current record's "customer_phone" value
 * @method MerchantOrder setBasePrice()      Sets the current record's "base_price" value
 * @method MerchantOrder setPrice()          Sets the current record's "price" value
 * @method MerchantOrder setProductName()    Sets the current record's "product_name" value
 * @method MerchantOrder setQuantity()       Sets the current record's "quantity" value
 * @method MerchantOrder setProductPrice()   Sets the current record's "product_price" value
 * @method MerchantOrder setCategory()       Sets the current record's "category" value
 * @method MerchantOrder setDiscount()       Sets the current record's "discount" value
 * @method MerchantOrder setDiscountPrice()  Sets the current record's "discount_price" value
 * @method MerchantOrder setIsDone()         Sets the current record's "is_done" value
 * @method MerchantOrder setProcessed()      Sets the current record's "processed" value
 * @method MerchantOrder setTransTypeId()    Sets the current record's "trans_type_id" value
 * @method MerchantOrder setHoldFee()        Sets the current record's "hold_fee" value
 * @method MerchantOrder setPayGateFee()     Sets the current record's "pay_gate_fee" value
 * @method MerchantOrder setDiscountReal()   Sets the current record's "discount_real" value
 * @method MerchantOrder setContent()        Sets the current record's "content" value
 * 
 * @package    cms_ctt
 * @subpackage model
 * @author     viettel
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseMerchantOrder extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('merchant_order');
        $this->hasColumn('transaction_id', 'string', 255, array(
             'type' => 'string',
             'comment' => 'Ma don hang',
             'length' => 255,
             ));
        $this->hasColumn('sub_id', 'string', 255, array(
             'type' => 'string',
             'comment' => 'Ma dinh danh khach hang',
             'length' => 255,
             ));
        $this->hasColumn('merchant_code', 'string', 50, array(
             'type' => 'string',
             'comment' => 'Ma doi tac',
             'length' => 50,
             ));
        $this->hasColumn('myvt_account', 'string', 50, array(
             'type' => 'string',
             'comment' => 'tai khoan dang nhap myviettel',
             'length' => 50,
             ));
        $this->hasColumn('order_time', 'datetime', null, array(
             'type' => 'datetime',
             'comment' => 'thoi gian dat hang',
             ));
        $this->hasColumn('status', 'string', 50, array(
             'type' => 'string',
             'comment' => 'trang thai don hang',
             'length' => 50,
             ));
        $this->hasColumn('payment_status', 'string', 50, array(
             'type' => 'string',
             'comment' => 'trang thai thanh toan',
             'length' => 50,
             ));
        $this->hasColumn('order_code', 'string', 255, array(
             'type' => 'string',
             'comment' => 'Ma don hang',
             'length' => 255,
             ));
        $this->hasColumn('customer_name', 'string', 50, array(
             'type' => 'string',
             'comment' => 'Ten Khach hang',
             'length' => 50,
             ));
        $this->hasColumn('customer_phone', 'string', 50, array(
             'type' => 'string',
             'comment' => 'SDT khach hang',
             'length' => 50,
             ));
        $this->hasColumn('base_price', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'Gia goc',
             'length' => 20,
             ));
        $this->hasColumn('price', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'Gia thuc te thanh toan',
             'length' => 20,
             ));
        $this->hasColumn('product_name', 'string', 255, array(
             'type' => 'string',
             'comment' => 'Ten san pham',
             'length' => 255,
             ));
        $this->hasColumn('quantity', 'string', 50, array(
             'type' => 'string',
             'comment' => 'so luong',
             'length' => 50,
             ));
        $this->hasColumn('product_price', 'string', 255, array(
             'type' => 'string',
             'comment' => 'Gia san pham',
             'length' => 255,
             ));
        $this->hasColumn('category', 'string', 255, array(
             'type' => 'string',
             'comment' => 'Hinh thuc thanh toan',
             'length' => 255,
             ));
        $this->hasColumn('discount', 'string', 50, array(
             'type' => 'string',
             'comment' => 'Hinh thuc thanh toan',
             'length' => 50,
             ));
        $this->hasColumn('discount_price', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'Hinh thuc thanh toan',
             'length' => 20,
             ));
        $this->hasColumn('is_done', 'integer', 4, array(
             'type' => 'integer',
             'comment' => 'Hinh thuc thanh toan',
             'length' => 4,
             ));
        $this->hasColumn('processed', 'integer', 4, array(
             'type' => 'integer',
             'comment' => 'Hinh thuc thanh toan',
             'length' => 4,
             ));
        $this->hasColumn('trans_type_id', 'string', 50, array(
             'type' => 'string',
             'comment' => 'Hinh thuc thanh toan',
             'length' => 50,
             ));
        $this->hasColumn('hold_fee', 'string', 50, array(
             'type' => 'string',
             'comment' => 'Hinh thuc thanh toan',
             'length' => 50,
             ));
        $this->hasColumn('pay_gate_fee', 'string', 50, array(
             'type' => 'string',
             'comment' => 'Hinh thuc thanh toan',
             'length' => 50,
             ));
        $this->hasColumn('discount_real', 'string', 50, array(
             'type' => 'string',
             'comment' => 'Hinh thuc thanh toan',
             'length' => 50,
             ));
        $this->hasColumn('content', 'clob', null, array(
             'type' => 'clob',
             'comment' => 'Hinh thuc thanh toan',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}