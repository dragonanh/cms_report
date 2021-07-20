<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('PartnerRefundLog', 'doctrine');

/**
 * BasePartnerRefundLog
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $tran_id
 * @property integer $refund_amount
 * @property integer $refund_type
 * @property clob $reason
 * @property string $username
 * @property string $ip
 * @property string $file_path
 * @property string $viettelid_point
 * @property string $pay_code
 * 
 * @method string           getTranId()          Returns the current record's "tran_id" value
 * @method integer          getRefundAmount()    Returns the current record's "refund_amount" value
 * @method integer          getRefundType()      Returns the current record's "refund_type" value
 * @method clob             getReason()          Returns the current record's "reason" value
 * @method string           getUsername()        Returns the current record's "username" value
 * @method string           getIp()              Returns the current record's "ip" value
 * @method string           getFilePath()        Returns the current record's "file_path" value
 * @method string           getViettelidPoint()  Returns the current record's "viettelid_point" value
 * @method string           getPayCode()         Returns the current record's "pay_code" value
 * @method PartnerRefundLog setTranId()          Sets the current record's "tran_id" value
 * @method PartnerRefundLog setRefundAmount()    Sets the current record's "refund_amount" value
 * @method PartnerRefundLog setRefundType()      Sets the current record's "refund_type" value
 * @method PartnerRefundLog setReason()          Sets the current record's "reason" value
 * @method PartnerRefundLog setUsername()        Sets the current record's "username" value
 * @method PartnerRefundLog setIp()              Sets the current record's "ip" value
 * @method PartnerRefundLog setFilePath()        Sets the current record's "file_path" value
 * @method PartnerRefundLog setViettelidPoint()  Sets the current record's "viettelid_point" value
 * @method PartnerRefundLog setPayCode()         Sets the current record's "pay_code" value
 * 
 * @package    cms_ctt
 * @subpackage model
 * @author     viettel
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePartnerRefundLog extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('partner_refund_log');
        $this->hasColumn('tran_id', 'string', 255, array(
             'type' => 'string',
             'comment' => 'Ma don hang',
             'length' => 255,
             ));
        $this->hasColumn('refund_amount', 'integer', 20, array(
             'type' => 'integer',
             'comment' => 'So tien hoan',
             'length' => 20,
             ));
        $this->hasColumn('refund_type', 'integer', 2, array(
             'type' => 'integer',
             'comment' => '(0: hoan toan phan, 1: hoan 1 phan)',
             'length' => 2,
             ));
        $this->hasColumn('reason', 'clob', null, array(
             'type' => 'clob',
             'comment' => 'ly do hoan tien',
             ));
        $this->hasColumn('username', 'string', 255, array(
             'type' => 'string',
             'comment' => 'usser hoan tien',
             'length' => 255,
             ));
        $this->hasColumn('ip', 'string', 50, array(
             'type' => 'string',
             'comment' => 'ip thuc hien hoan tien',
             'length' => 50,
             ));
        $this->hasColumn('file_path', 'string', 255, array(
             'type' => 'string',
             'comment' => 'dương dan file bb xac nhan hoan tien',
             'length' => 255,
             ));
        $this->hasColumn('viettelid_point', 'string', 100, array(
             'type' => 'string',
             'comment' => 'diem viettel ++',
             'length' => 100,
             ));
        $this->hasColumn('pay_code', 'string', 50, array(
             'type' => 'string',
             'comment' => 'Hinh thuc thanh toan',
             'length' => 50,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}