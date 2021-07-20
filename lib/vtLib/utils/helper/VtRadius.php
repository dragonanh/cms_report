<?php

/**
 * Created by PhpStorm.
 * User: tuanbm2
 * Date: 27/10/2015
 * Time: 2:32 CH
 */
class VtRadius
{


  public static function getIsdn()
  {
    $isdn = self::getMsisdn();
    if ($isdn != "unknown") {
      return VtHelper::getMobileNumber($isdn, VtHelper::MOBILE_NOTPREFIX);
    }
    return "unknown";
  }

  public static function getMsisdnFromHeaderAndRadius()
  {

//        $ip = self::getAgentIp(); Loi qua haproxy
    $loggerRadius = VtHelper::getLogger4Php("radius");
    $ip = self::getRealIpAddr();
    if (self::isV_wapIp($ip)) {
      // Redirect sang HTTP de nhan dien 3G
      sfContext::getInstance()->getUser()->redirectToHttp();

      $msisdn2 = self::getMsisdnFromHeader();
      if ($msisdn2 != "unknown") {

          $keyRadius = "Radius_" . session_id() . "_" . VtRadius::getRealIpAddr();
        $msisdn = VtHelper::getKeyCache($keyRadius);
        $loggerRadius->info("GET_KEY_RADIUS|" . $keyRadius . "|VALUE:" . var_export($msisdn, true));
        if ($msisdn === false) {//neu ko ton tai keyRadius
          $msisdn = self::getMsisdnFromIp($ip);
          $loggerRadius->info("==>SET_KEY_RADIUS|" . $keyRadius . "|VALUE:" . var_export($msisdn, true));
          VtHelper::setKeyCache($keyRadius, $msisdn, 120);
        }
        //$msisdn =  self::getMsisdnFromIp($ip);
        $loggerRadius->info($ip . "|From Radius:" . $msisdn . "|From Header:" . $msisdn2);
        if ($msisdn != "unknown" && $msisdn != $msisdn2) {
          $loggerRadius->info($ip . "|ERROR|RADIUS|" . $msisdn . "|HEADER:" . $msisdn2);
          return null;
        }
        if ($msisdn == "unknown") {
          return null;
        }


        return $msisdn;
      }
    }
    //$loggerRadius->info("ip is not in ip_range allow: ".$ip);
    return null;
  }

  public static function getMsisdnFromRadius()
  {
    $loggerRadius = VtHelper::getLogger4Php("radius");
    $ip = self::getRealIpAddr();
    if (self::isV_wapIp($ip)) {
      $keyRadius = "Radius_" . session_id() . "_" . $ip;
      $msisdn = VtHelper::getKeyCache($keyRadius);
      $loggerRadius->info("GET_KEY_RADIUS|" . $keyRadius . "|VALUE:" . var_export($msisdn, true));

      if ($msisdn === false) {//neu ko ton tai keyRadius
        $msisdn = self::getMsisdnFromIp($ip);
        $loggerRadius->info("==>SET_KEY_RADIUS|" . $keyRadius . "|VALUE:" . var_export($msisdn, true));
        VtHelper::setKeyCache($keyRadius, $msisdn, 120);
      }

      if ($msisdn == "unknown") {
        return null;
      }

      return $msisdn;
    }
    return null;
  }

  /**
   * Tra ve so dien thoai thue bao (Su dung 3G Header va Radius)
   * @created on 10 08, 2012
   * @author
   * @return string|unknown
   */
  public static function getMsisdn()
  {
//        $ip = self::getAgentIp(); Loi qua haproxy
    $loggerAll = VtHelper::getLogger4Php("radius");
    $ip = self::getRealIpAddr();
    if (self::isV_wapIp($ip)) {
      $isdn = self::getMsisdnFromIp($ip);
      $loggerAll->info("From IP:" . $ip . "=> isdn=" . $isdn);
      return $isdn;
    }
    $loggerAll->info("ip is not in ip_range allow: " . $ip);
    return 'unknown';
  }

  public static function getMsisdnFromHeader()
  {
//        $loggerAll = VtHelper::getLogger4Php("radius");
    $ip = self::getRealIpAddr();
    if (self::isV_wapIp($ip)) {
      if (isset($_SERVER['MSISDN'])) {
//                $loggerAll->info('IP='.$ip.'&SERVER["MSISDN"]='.$_SERVER['MSISDN']);
        return $_SERVER['MSISDN'];
      }
      if (isset($_SERVER['HTTP_MSISDN'])) {
//                $loggerAll->info('ip='.$ip.'&SERVER["HTTP_MSISDN"]='.$_SERVER['HTTP_MSISDN']);
        return $_SERVER['HTTP_MSISDN'];
      }
//            $loggerAll->debug("Header is not exist isdn");
      $loggerRadius = VtHelper::getLogger4Php("radius");
      $loggerRadius->info($ip . "|Header is not exist isdn");
    } else {
//            $loggerAll->debug("getMsisdnFromHeader:ip is not in ip_range allow: ".$ip);
    }
    return "unknown";
  }

  public static function getIsdnFromHeader()
  {
    $loggerAll = VtHelper::getLogger4Php("radius");
    $ip = self::getRealIpAddr();
    if (self::isV_wapIp($ip)) {
      if (isset($_SERVER['MSISDN'])) {
        $loggerAll->info('IP=' . $ip . '&SERVER["MSISDN"]=' . $_SERVER['MSISDN']);
        return VtHelper::getMobileNumber($_SERVER['MSISDN'], VtHelper::MOBILE_NOTPREFIX);
      }
      if (isset($_SERVER['HTTP_MSISDN'])) {
        $loggerAll->info('ip=' . $ip . '&SERVER["HTTP_MSISDN"]=' . $_SERVER['HTTP_MSISDN']);
        return VtHelper::getMobileNumber($_SERVER['HTTP_MSISDN'], VtHelper::MOBILE_NOTPREFIX);
      }
      $loggerAll->debug("Header is not exist isdn");
      $loggerRadius = VtHelper::getLogger4Php("radius");
      $loggerRadius->info($ip . "|Header is not exist isdn");
    } else {
      $loggerAll->debug("getIsdnFromHeader:ip is not in ip_range allow: " . $ip);
    }
    return "unknown";
  }

  /**
   * Ham kiem tra Ip co nam trong dai ip opera cua viettel khong
   * @author dungld5
   * @created on 17/01/2013
   * @param $ip
   * @return bool
   */
  public static function isV_opreraIp($ip)
  {
    $vInternetRange = sfConfig::get('app_ip_opera_mini_v_ip');
    if (!empty($vInternetRange)) {
      foreach ($vInternetRange as $range) {
        $netArr = explode("/", $range);
        if (self::ipInNetwork($ip, $netArr[0], $netArr[1])) {
          return true;
        }
      }
    }
    return false;
  }

  /**
   * @author: dungld5
   * @created at : 10/02/2015
   * Kiem tra thue bao truy cap bang duong 3G hay wifi
   * @return boolean
   */
  public static function checkDCN()
  {
    if (sfConfig::get('app_check_3g') == 1) { // Kiem tra dai dcn
      $ip = self::getAgentIp();
      if (self::isV_wapIp($ip)) { // nam trong dai 3G
        return true;
      } else {
        if (self::isV_opreraIp($ip)) {
          return true;
        } else {
          return false; // Khong nam trong dai ip pool
        }
      }
    } else {
      return false; // khong check dai ip pool
    }
  }

//  public static function getRealIpAddr()
//  {
//    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//      $ip = $_SERVER['HTTP_CLIENT_IP'];
//    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip passed from proxy
//    {
//      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//    } else {
//      $ip = $_SERVER['REMOTE_ADDR'];
//    }
//    return $ip;
//  }

  // Using if forwarded from WAF
  public static function getRealIpAddr()
  {
    if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
      $ip = $_SERVER['HTTP_X_REAL_IP'];
    }elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip passed from proxy
    {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }
  
  // Using if forwarded from WAF
  public static function getRealIpAddrForward()
  {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) //to check ip passed from proxy
    {
      $ip = $_SERVER['HTTP_X_REAL_IP'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  /**
   * Tra ve IP cua thue bao
   * @author ducda2@viettel.com.vn
   * @return IP
   */
  public static function getAgentIp()
  {
    // $ipString = @getenv("HTTP_X_FORWARDED_FOR");
    $ipString = @getenv("HTTP_X_REAL_IP");
    if (!empty($ipString)) {
      $addr = explode(",", $ipString);
      return $addr[0];
    } else {
      return $_SERVER['REMOTE_ADDR'];
    }
  }

  /**
   * Ham kiem tra Ip co nam trong dai V-internet cua viettel khong
   * @author NamDT5
   * @created on 17/01/2013
   * @param $ip
   * @return bool
   */
  public static function isV_wapIp($ip)
  {
    $vInternetRange = sfConfig::get('app_ip_pool_v_wap');
    if (!empty($vInternetRange)) {
      foreach ($vInternetRange as $range) {
        $netArr = explode("/", $range);
        if (self::ipInNetwork($ip, $netArr[0], $netArr[1])) {
          return true;
        }
      }
    }
    return false;
  }

  /**
   * Ham kiem tra IP co nam trong dai IP cho phep khong
   * Tham khao: http://php.net/manual/en/function.ip2long.php
   * @author NamDT5
   * @created on 17/01/2013
   * @param $ip
   * @param $netAddr
   * @param $netMask
   * @return bool
   */
  public static function ipInNetwork($ip, $netAddr, $netMask)
  {
    if ($netMask <= 0) {
      return false;
    }
    $ipBinaryString = sprintf("%032b", ip2long($ip));
    $netBinaryString = sprintf("%032b", ip2long($netAddr));
    return (substr_compare($ipBinaryString, $netBinaryString, 0, $netMask) === 0);
  }

  public static function getDeviceIp()
  {
    $ipString = @getenv("HTTP_X_FORWARDED_FOR");

    if (!empty($ipString)) {
      $addr = explode(",", $ipString);
      return $addr[0];
    } else {
      return $_SERVER['REMOTE_ADDR'];
    }
  }

  public static function getMsisdnFromIp($ip)
  {
    $loggerRadius = VtHelper::getLogger4Php("radius");
    $start = date('YmdHis');
    $startTime = VtRadius::current_millis();
    $messageLogTime = '';
    if (!$ip) {
      $loggerRadius->info("Cannot get IP: " . $ip);
      $response = 'unknown';
      $errorCodeLogTime = VtLogKpiStatus::ERROR;
    } else {
      $link_ws = sfConfig::get('app_vaaa_ws_url');
      $username = sfConfig::get('app_vaaa_username');
      $password = sfConfig::get('app_vaaa_password');
      $encryption = new vtEncryption();
      $password = $encryption->decode($password);
      try {
        //$wsdl = sfConfig::get('sf_config_dir') . '/radius.wsdl';
        $wsdl = $link_ws;
//        $options = array('connect_timeout' => 1, 'timeout' => 2);
//        $client = new TimeoutSoapClient($wsdl, $options);
        $client = new SoapClientTimeout($wsdl);
        $client->__setConnectTimeout(sfConfig::get('app_vaaa_ws_conn_timeout', 1));
        $client->__setTimeout(sfConfig::get('app_vaaa_ws_read_timeout', 1));
        $result = $client->__soapCall('getMSISDN', array(array(
          'username' => $username,
          'password' => $password,
          'ip' => $ip)));
        if ($result == false) {
          $loggerRadius->info('Radius error, result=false ');
          $response = 'unknown';
          $errorCodeLogTime = VtLogKpiStatus::TIMEOUT;
        }else {
          $errorCodeLogTime = VtLogKpiStatus::OK;
          if (is_object($result)) {
            if ($result->return->code == 0) {
              $ipPool = $ip . str_repeat(" ", 15 - strlen($ip));
              $msisdn = $result->return->desc . str_repeat(" ", 12 - strlen($result->return->desc));
              $loggerRadius->info('{SUCCESS}|' . $ipPool . '|' . $msisdn . '|' . (self::current_millis() - $startTime));
              $response = $result->return->desc;
            } else {
              $loggerRadius->info('{RADIUS:ERROR2}|' . $ip . ' -> ' . $result->return->desc);
              $response = 'unknown';
            }
          } else {
            $loggerRadius->info('{RADIUS:ERROR3}|' . $ip . " -> response no object!");
            $response = 'unknown';
          }
        }
      } catch (Exception $e) {
        $loggerRadius->info('{RADIUS:ERR1} Lay MSISDN gap ngoai le: ' . $e->getMessage());
        $response = 'unknown';
        $errorCodeLogTime = VtLogKpiStatus::ERROR;
        $messageLogTime = $e->getMessage();
      }
    }
    //ghi log thoi gian goi ws
    VtHelper::writeLogKpi('vtshopWs',sprintf('%s/%s',__CLASS__,__FUNCTION__), VtLogKpiType::WS,$errorCodeLogTime,$start, VtHelper::getMilliTime() - $startTime,$messageLogTime);
    return $response;
  }

  public static function current_millis()
  {
    list($usec, $sec) = explode(" ", microtime());
    return round(((float)$usec + (float)$sec) * 1000);
  }

  public static function getMsisdnFromHeaderAndRadiusForWeb4G()
  {

//        $ip = self::getAgentIp(); Loi qua haproxy
    $loggerRadius = VtHelper::getLogger4Php("radius");
    $ip = self::getRealIpAddr();
    if (self::isV_wapIp($ip)) {
      $msisdn2 = self::getMsisdnFromHeader();
      if ($msisdn2 != "unknown") {

        $keyRadius = "Radius_" . session_id() . "_" . VtRadius::getRealIpAddr();
        $msisdn = VtHelper::getKeyCache($keyRadius);
        $loggerRadius->info("GET_KEY_RADIUS_WEB_4G|" . $keyRadius . "|VALUE:" . var_export($msisdn, true));
        if ($msisdn === false) {//neu ko ton tai keyRadius
          $msisdn = self::getMsisdnFromIp($ip);
          $loggerRadius->info("==>SET_KEY_RADIUS_WEB_4G|" . $keyRadius . "|VALUE:" . var_export($msisdn, true));
          VtHelper::setKeyCache($keyRadius, $msisdn, 120);
        }
        //$msisdn =  self::getMsisdnFromIp($ip);
        $loggerRadius->info($ip . "|From Radius:" . $msisdn . "|From Header:" . $msisdn2);
        if ($msisdn != "unknown" && $msisdn != $msisdn2) {
          $loggerRadius->info($ip . "|ERROR|RADIUS_WEB_4G|" . $msisdn . "|HEADER:" . $msisdn2);
          return null;
        }
        if ($msisdn == "unknown") {
          return null;
        }


        return $msisdn;
      }
    }
    //$loggerRadius->info("ip is not in ip_range allow: ".$ip);
    return null;
  }

}
