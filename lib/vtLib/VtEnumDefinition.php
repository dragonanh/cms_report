<?php

// VasBuEnum 0: Mới tạo, 1: Phê duyệt, 2: Tạm dừng
abstract class VtUserStatusEnum
{
  const DEACTIVE = 0; #huy kich hoat
  const ACTIVE = 1; #kich hoat
}


//trang thai don hang
abstract class VtUserStatus
{
  const ACTIVE = 1; #dat hang
  const INACTIVE = 0; #dat hang
}
//trang thai giao dich
abstract class VtVpgStatusEnum
{
  const ORDER_FAIL = 0; #that bai
  const ORDER_SUCCESS = 1; #thanh cong

  public static function getArr(){
    $i18n = sfContext::getInstance()->getI18N();
    return [
      self::ORDER_FAIL => $i18n->__('Giao dịch thất bại'),
      self::ORDER_SUCCESS => $i18n->__('Giao dịch thành công'),
    ];
  }

  public static function getStatusName($status){
    $arr = self::getArr();
    return $arr[$status];
  }
}

//trang thai giao dich
abstract class VtCttStatusEnum
{
  const PROCESSING = -1; #đang xu ly
  const FAIL = 0; #that bai
  const SUCCESS = 1; #thanh cong

  public static function getArr(){
    $i18n = sfContext::getInstance()->getI18N();
    return [
      self::PROCESSING => $i18n->__('Đang xử lý'),
      self::FAIL => $i18n->__('Trừ tiền thất bại'),
      self::SUCCESS => $i18n->__('Trừ tiền thành công'),
    ];
  }

  public static function getStatusName($status, $amount, $isDataCM = false)
    {
        if ($amount == 0) {
            return 'Không trừ tiền';
        }

        $arr = self::getArr();
        if (is_null($status) || $status == self::PROCESSING) {
            if (!$isDataCM) {
                return '';
            } else {
                return 'Đang xử lý';
            }
        }

        return $arr[$status];
    }
}

abstract class OrderTypeEnum
{
  const PREPAID = 1;
  const POSTPAID = 2;
  const REGISTER_VAS = 21;
  const REGISTER_DATA = 22;
  const REGISTER_VTFREE = 23;
  const TOPUP = 24;
  const CDT = 8;
  const MNP = 25;
  const PRE_TO_POST = 26;
  const MM = 28;
  const PINCODE = 29;
  const URBOX = 30;
  const DEVICE = 34;
  const DEBT = 35;

  public static function getArr(){
    return [
      self::PREPAID => "Mua sim trả trước",
      self::POSTPAID => "Mua sim trả sau",
      self::REGISTER_VAS => "Mua gói vas",
      self::REGISTER_DATA => "Mua gói data",
      self::REGISTER_VTFREE => "Mua gói vtfree",
      self::TOPUP => "Topup",
      self::CDT => "Cước đóng trước",
      self::MNP => "Chuyển mạng giữ số",
      self::PRE_TO_POST => "Chuyển TT sang TS online",
      self::MM => "Mobile Money",
      self::PINCODE => "Mua thẻ cào",
      self::URBOX => "Mua voucher",
      self::DEVICE => "Mua thiết bị",
      self::DEBT => "Gạch nợ - đóng cước trước",
    ];
  }
}

//trang thai giao dich
abstract class VtCttChannelEnum
{
  const OTHER = 'OTHER';
  const VTPAY = 'VTP';
  const VNPAY_ATM = 'VNPAY_ATM';
  const VNPAY_VISA = 'VNPAY_VISA';

  public static function getArr(){
    $i18n = sfContext::getInstance()->getI18N();
    return [
      self::OTHER => $i18n->__('Cổng thanh toán'),
      self::VTPAY => $i18n->__('ViettelPay'),
      self::VNPAY_ATM => $i18n->__('VNPAY_ATM'),
      self::VNPAY_VISA => $i18n->__('VNPAY_Thẻ quốc tế'),
    ];
  }

  public static function listChannelVnpay(){
    return [self::VTPAY, self::VNPAY_ATM, self::VNPAY_VISA];
  }

  public static function getName($channel){
    $i18n = sfContext::getInstance()->getI18N();
    $arr = self::getArr();
    return isset($arr[$channel]) ? $arr[$channel] : $i18n->__('Cổng thanh toán');
  }
}

//trang thai giao dich
abstract class PartnerRefundStatusEnum
{
  const PARTNER_SEND_REFUND = 1;
  const SUCCESS = 2;
  const FAIL = 3;
  const REJECT = 4;
  const APPROVE = 5;
  const CALL_REFUND_FAIL = 6;

  public static function getArr(){
    $i18n = sfContext::getInstance()->getI18N();
    return [
        self::PARTNER_SEND_REFUND => $i18n->__('Đã gửi yêu cầu hoàn tiền, đang chờ xác nhận hoàn tiền'),
        self::SUCCESS => $i18n->__('Hoàn tiền thành công'),
        self::FAIL => $i18n->__('Hoàn tiền thất bại'),
        self::REJECT => $i18n->__('Đã từ chối'),
        self::APPROVE => $i18n->__('Đã duyệt yêu cầu hoàn tiền'),
        self::CALL_REFUND_FAIL => $i18n->__('Gửi yêu cầu hoàn tiền thất bại')
    ];
  }

  public static function getStatusCanRefund(){
    return ["",self::PARTNER_SEND_REFUND, self::FAIL, self::CALL_REFUND_FAIL];
  }

  public static function getName($status){
    $arr = self::getArr();
    return isset($arr[$status]) ? $arr[$status] : "";
  }
}
abstract class PartnerRefundPointStatusEnum
{
    const SUCCESS = 1;
    const FAIL = 0;


    public static function getArr(){
        $i18n = sfContext::getInstance()->getI18N();
        return [
            self::SUCCESS => $i18n->__('Hoàn điểm thành công'),
            self::FAIL => $i18n->__('Hoàn điểm thất bại'),
        ];
    }

    public static function getName($status){
        $arr = self::getArr();
        return isset($arr[$status]) ? $arr[$status] : "";
    }
}

abstract class VnPayPayCodeEnum
{
  const VNPAY_QRCODE = 'VNPQR';
  const VNPAY_ATM = 'VNPATM';
  const VNPAY_VISACARD = 'VNPINTCARD';

  public static function getListPayCodeVnPay(){
    return [self::VNPAY_VISACARD, self::VNPAY_QRCODE, self::VNPAY_ATM];
  }
}







