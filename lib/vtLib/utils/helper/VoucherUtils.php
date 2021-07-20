<?php
/**
 * Created by PhpStorm.
 * User: tiennx6
 * Date: 22/04/2017
 * Time: 3:24 CH
 */

class VoucherUtils {

  public static function isSatisfy($code, $tId, $pId, $sType) {
    if ($code) {
      $vbCode = VtBuyCodeTable::getActiveCode($code);
      if ($vbCode) {
        $campIdArr = explode(',', $vbCode->getCampaignId());
        return VtVoucherCampaignTable::checkSatisfy($campIdArr, $tId, $pId, $sType);
      }
    }
    return false;
  }

  public static function isReturnCode($tId, $pId, $sType, $rType = VtReturnTypeEnum::NEW_ORDER) {
    return VtVoucherCampaignTable::isReturnCode($tId, $pId, $sType, $rType);
  }

  public static function decreaseCodeNumber($code) {
    return VtBuyCodeTable::decreaseCodeNumber($code);
  }

  public static function checkDisplayVoucherCampaign()
  {
    $setting = VtHelper::getSystemSetting('DISPLAY_VOUCHER_CAMPAIGN_DEVICE');
    $settingArr = explode('|', $setting);
    if (count($settingArr) >= 3) {
      $startTime = strtotime($settingArr[1]);
      $endTime = strtotime($settingArr[2]);
      $curTime = strtotime('now');
      if ($curTime < $startTime || $curTime > $endTime) {
        return false;
      }
      return $settingArr[0];
    } else {
      return false;
    }
  }

  public static function checkDisplayVoucherCampaignFtth()
  {
    $setting = VtHelper::getSystemSetting('DISPLAY_VOUCHER_CAMPAIGN_FTTH');
    $settingArr = explode('|', $setting);
    if (count($settingArr) >= 3) {
      $startTime = strtotime($settingArr[1]);
      $endTime = strtotime($settingArr[2]);
      $curTime = strtotime('now');
      if ($curTime < $startTime || $curTime > $endTime) {
        return false;
      }
      return $settingArr[0];
    } else {
      return false;
    }
  }

  public static function getContentVoucherCampaignFtth()
  {
    if (self::checkDisplayVoucherCampaignFtth()) {
      $content = VtHelper::getSystemSetting('CONTENT_VOUCHER_CAMPAIGN_FTTH');
      if ($content) {
        $content = VtHelper::strip_html_tags_and_decode($content);
        return $content;
      }
    }
    return '';
  }

}