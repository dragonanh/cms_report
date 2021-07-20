<?php

/**
 * Created by PhpStorm.
 * User: tuanbm2
 * Date: 27/10/2015
 * Time: 2:32 CH
 */
class VtRegisterAddonWS
{

  //public static function registerAddOn($msisdn, $packageName, $isPromotion = 0, $sendSms = 1) {
  //    $logger = VtHelper::getLogger4Php("all");
  //    $wsdl = sfConfig::get('sf_app_config_dir') . DIRECTORY_SEPARATOR . "register3g.wsdl";
  //    $method = sfConfig::get("app_addon_method", "registerAddOn");
  //    $options = array(
  //        'connect_timeout' => sfConfig::get('app_radius_connect_timeout', 5),
  //        'timeout' => sfConfig::get('app_radius_timeout', 5),
  //        'cache_wsdl' => WSDL_CACHE_NONE,
  //    );
  //    $params = array();
  //    $params['user'] = sfConfig::get('app_addon_username');
  //    $params['password'] = sfConfig::get('app_addon_password');
  //    $params['msisdn'] = $msisdn;
  //    $params['packageName'] = $packageName;
  //    $params['isPromotion'] = $isPromotion;
  //    $params['sendSms'] = $sendSms;
  //    try {
  //        $client = new TimeoutSoapClient($wsdl, $options);
  //        $response = $client->__soapCall($method, array($params));
  //        $stdClass = $response->return;
  //        $params['password'] = "******";
  //        $logger->info("[CALL_WEBSERVICE] method=[" . $method . "] params = [" . var_export($params, true) . "]| result=[" . var_export($stdClass, true) . "]");
  //        return $stdClass;
  //    } catch (Exception $e) {
  //        $params['password'] = "******";
  //        $logger->info("[CALL_WEBSERVICE][ERROR]  method=[" . $method . "] params = [" . var_export($params, true) . "]| exception=[" . var_export($e->getTraceAsString(), true) . "|message=" . $e->getMessage() . "]");
  //        return null;
  //    }
  //}
  public static function current_millis()
  {
    list($usec, $sec) = explode(" ", microtime());
    return round(((float)$usec + (float)$sec) * 1000);
  }


  public static function checkPackage($msisdn)
  {
    $start = date('YmdHis');
    $startTime = VtHelper::getMilliTime();
    $logger = VtHelper::getLogger4Php("all");
    $params = array();
    $params['user'] = sfConfig::get('app_addon_username');
    $vtEncryption = new vtEncryption();

    $params['password'] = $vtEncryption->decode(sfConfig::get('app_addon_password'));
    $params['msisdn'] = $msisdn;
    try {
      $soapClient = new SoapClient(sfConfig::get('app_addon_wsdl'), array(
        'location' => 'http://192.168.176.213:8788/WebServices/DataWS'
      ));
      $response = $soapClient->__soapCall('checkData3gV2', array($params));
      if($response) {
        $stdClass = $response->return;
        $errorCode = VtLogKpiStatus::OK;
        $message = '';
      }else{
        $errorCode = VtLogKpiStatus::TIMEOUT;
        $message = 'Timeout';
        $stdClass = null;
      }
      $params['password'] = "******";
      $logger->info("[checkPackage] params = [" . var_export($params, true) . "]| result=[" . var_export($stdClass, true) . "][Time:" . (self::current_millis() - $startTime) . "]");

    } catch (Exception $e) {
      $errorCode = VtLogKpiStatus::ERROR;
      $message = $e->getMessage();
      $params['password'] = "******";
      $logger->info("[checkPackage] params = [" . var_export($params, true) . "]| exception=[" . var_export($e->getMessage(), true) . "|message=" . $e->getMessage() . "][Time:" . (self::current_millis() - $startTime) . "]");
      $stdClass = null;
    }

    //ghi log thoi gian goi ws
    VtHelper::writeLogKpi('vtshopWs',sprintf('%s/%s',__CLASS__,__FUNCTION__), VtLogKpiType::WS,$errorCode,$start, VtHelper::getMilliTime() - $startTime,$message);
    return $stdClass;
  }

  /***
   * @author: tuanbm2
   * @description: Check dung luong 3G nguoi dung con co the su dung
   * @param $msisdn
   * @return array
   */

  public static function checkRemain3g($msisdn, $accountType = 13)
  {
    $start = date('YmdHis');
    $startTime = VtHelper::getMilliTime();
    $url = sfConfig::get("app_data3g_link");
    $loggerAll = VtHelper::getLogger4Php("all");
    $vtEncryption = new vtEncryption();
    $user = sfConfig::get('app_data3g_username');
    $password = $vtEncryption->decode(sfConfig::get('app_data3g_password'));
    $data = array(
      "username" => $user,
      "password" => $password,
      "msisdn" => $msisdn,
      "type" => "viewData",
      "accountType" => $accountType
    );
    $result = array("errorCode" => -555);
    try {
      $value = self::post_curl($url, $data, 2);
      if($value) {
        $data['password'] = "******";
        $loggerAll->info(var_export($data, true));
        $loggerAll->info("Remain3g:" . $value . "|Time:" . (self::current_millis() - $startTime));
        $array = explode("|", $value);
        if (count($array) >= 3) {
          $result = array("errorCode" => $array[0], "data" => $array[1], "expire" => $array[2]);
        }
        $errorCode = VtLogKpiStatus::OK;
        $message = '';
      }else{
        $errorCode = VtLogKpiStatus::TIMEOUT;
        $message = 'Timeout';
      }
    } catch (Exception $e) {
      $errorCode = VtLogKpiStatus::ERROR;
      $message = $e->getMessage();
      $data['password'] = "******";
      $loggerAll->info("[CALL_WEBSERVICE][ERROR]  method=[viewData] params = [" . var_export($data, true) . "]| exception=[" . var_export($e->getMessage(), true) . "|message=" . $e->getMessage() . "]");
    }

    //ghi log thoi gian goi ws
    VtHelper::writeLogKpi('vtshopWs',sprintf('%s/%s',__CLASS__,__FUNCTION__), VtLogKpiType::WS,$errorCode,$start, VtHelper::getMilliTime() - $startTime,$message);
    return $result;
  }

  /**
   * @author: tuanbm2
   * @description: Gui ma OTP cho nguoi dung
   * @param $msisdn
   * @return array
   */
  public static function sendOTP($msisdn, $content)
  {
    $start = date('YmdHis');
    $startTime = VtHelper::getMilliTime();
    $logger = VtHelper::getLogger4Php("all");
    $wsdl = sfConfig::get('app_otp_link');
    $vtEncryption = new vtEncryption();
    $username = $vtEncryption->decode(sfConfig::get('app_otp_username'));//"khanhlp";
    $password = $vtEncryption->decode(sfConfig::get('app_otp_password'));//"khanhlp";

    $method = "InsertMt";
    $options = array(
      'connect_timeout' => 20,
      'timeout' => 20,
      'cache_wsdl' => WSDL_CACHE_NONE,
    );
    try {
      $params = array();
      $params['User'] = $username;
      $params['Password'] = $password;
      $params['sender'] = "5656";
      $params['isdn'] = $msisdn;
      $params['content'] = $content;
      $client = new TimeoutSoapClient($wsdl, $options);
      $response = $client->__soapCall($method, array($params));
      if($response){
        $stdClass = $response->return;
        $message = '';
        $errorCode = VtLogKpiStatus::OK;
      }else{
        $stdClass = null;
        $message = 'Timeout';
        $errorCode = VtLogKpiStatus::TIMEOUT;
      }
      $params['password'] = "******";
      $logger->info("[CALL_WEBSERVICE] method=[" . $method . "] params = [" . var_export($params, true) . "]| result=[" . var_export($stdClass, true) . "]");

    } catch (Exception $e) {
      $params['password'] = "******";
      $logger->info("[CALL_WEBSERVICE][ERROR]  method=[" . $method . "] params = [" . var_export($params, true) . "]| exception=[" . var_export($e->getMessage(), true) . "|message=" . $e->getMessage() . "]");
      $stdClass = null;
      $errorCode = VtLogKpiStatus::ERROR;
      $message = $e->getMessage();
    }
    //ghi log thoi gian goi ws
    VtHelper::writeLogKpi('vtshopWs',sprintf('%s/%s',__CLASS__,__FUNCTION__), VtLogKpiType::WS,$errorCode,$start, VtHelper::getMilliTime() - $startTime,$message);
    return $stdClass;
  }

  /**
   * @author: tuanbm2
   * @description: Lay Ma OTP
   * @return string
   */
  public static function getOTPNumber()
  {
    $otp = "";
    $length = sfConfig::get('app_otp_length', 6);
    for ($i = 0; $i < $length; $i++)
      $otp .= rand(0, 9);
    return $otp;
  }




  //public static function register3g($msisdn, $packageName, $sendSms = 1) {
  //    $logger = VtHelper::getLogger4Php("all");
  //    $wsdl = sfConfig::get('sf_app_config_dir') . DIRECTORY_SEPARATOR . "register3g.wsdl";
  //    $method = "registerData";
  //        //sfConfig::get("app_addon_method", "registerAddOn");
  //    $location = "http://192.168.176.213:8788/WebServices/DataWS";
  //    $options = array(
  //        'connect_timeout' => 20,
  //        'timeout' => 20,
  //        'cache_wsdl' => WSDL_CACHE_NONE,
  //        'location' => $location,
  //    );
  //    try {
  //        $params = array();
  //        $params['user'] = sfConfig::get('app_addon_username');
  //        $params['password'] = sfConfig::get('app_addon_password');
  //        $params['msisdn'] = $msisdn;
  //        $params['pkgName'] = $packageName;
  //        $params['sendMt'] = $sendSms;
  //        $params['requestId'] = uniqid();
  //        $client = new TimeoutSoapClient($wsdl, $options);
  //        $response = $client->__soapCall($method, array($params));
  //        $stdClass = $response->return;
  //        $params['password'] = "******";
  //        $logger->info("[CALL_WEBSERVICE] method=[" . $method . "] params = [" . var_export($params, true) . "]| result=[" . var_export($stdClass, true) . "]");
  //        return $stdClass;
  //    } catch (Exception $e) {
  //        $params['password'] = "******";
  //        $logger->info("[CALL_WEBSERVICE][ERROR]  method=[" . $method . "] params = [" . var_export($params, true) . "]| exception=[" . var_export($e->getMessage(), true) . "|message=" . $e->getMessage() . "]");
  //        return null;
  //    }
  //}
  //
  //public static function scratchCardAPI($transId,$cardSerial,$pinCard) {
  //    $logger = VtHelper::getLogger4Php("provisioning");
  //    //$wsdl = sfConfig::get('sf_app_config_dir') . DIRECTORY_SEPARATOR . "ScratchCardAPI.wsdl";
  //    $method = "topupCard";
  //    $wsdl =  sfConfig::get('app_scratchCard_link','https://125.235.33.166:8443/ScratchCardAPI/ScratchCardAPI?WSDL');
  //
  //    $vtEncryption = new vtEncryption();
  //    $parnerID = $vtEncryption->decode(sfConfig::get('app_scratchCard_username'));//"1000000090";
  //    $passphase = $vtEncryption->decode(sfConfig::get('app_scratchCard_password'));//"DiGital080616Gt";
  //    $provider = sfConfig::get('app_scratchCard_provider');
  //    $serviceName = sfConfig::get('app_scratchCard_serviceName');
  //
  //
  //    $context = stream_context_create(array(
  //        'ssl' => array(
  //            // set some SSL/TLS specific options
  //            'verify_peer' => false,
  //            'verify_peer_name' => false,
  //            'allow_self_signed' => true
  //        )
  //    ));
  //
  //    $options = array(
  //        'connect_timeout' => 20,
  //        'timeout' => 20,
  //        'cache_wsdl' => WSDL_CACHE_NONE,
  //        'stream_context' => $context
  //    );
  //    $params = array();
  //    $params['parnerID'] = $parnerID;
  //    $params['passphase'] = $passphase;
  //    $params['cardSerial'] = $cardSerial;
  //    $params['pinCard'] = $pinCard;
  //    $params['transId'] = $transId;
  //    $params['provider'] = $provider;
  //    $params['serviceName'] = $serviceName;
  //    try {
  //        $client = new SoapClient($wsdl, $options);
  //        //$client->call()
  //        $response = $client->__soapCall($method, array($params));
  //        $stdClass = $response->return;
  //        $params['passphase'] = "******";
  //        $params['pinCard'] = "******";
  //        $logger->info("[CALL_WEBSERVICE] method=[topupCard] params = [" . var_export($params, true) . "]| result=[" . var_export($stdClass, true) . "]");
  //        return $stdClass;
  //    } catch (Exception $e) {
  //        $params['passphase'] = "******";
  //        $params['pinCard'] = "******";
  //        $logger->info("[CALL_WEBSERVICE][ERROR]  method=[topupCard] params = [" . var_export($params, true) . "]| exception=[" . var_export($e->getMessage(), true) . "|message=" . $e->getTraceAsString() . "]");
  //        return null;
  //    }
  //}

  ///***
  // * @author: tuanbm2
  // * @description: Cong tien cho nguoi dung provisioning
  // * @param $msisdn
  // * @return array
  // */
  //
  //public static function addBalance($msisdn,$balance,$accountType){
  //    $startTime = self::current_millis();
  //    $loggerAll = VtHelper::getLogger4Php("all");
  //    $url = sfConfig::get('app_balance_link',"http://10.58.52.23:8670/provisioning");
  //    $vtEncryption = new vtEncryption();
  //    $user = $vtEncryption->decode(sfConfig::get('app_balance_username'));
  //    $password = $vtEncryption->decode(sfConfig::get('app_balance_password'));
  //    $data = array(
  //        "username"=>$user,
  //        "password"=>$password,
  //        "msisdn"=>$msisdn,
  //        "type"=>"addBalance",
  //        "accountType"=>$accountType,
  //        "partyCode"=>"ITSME_CONG_TIEN",
  //        "balance"=>$balance
  //    );
  //    if($accountType==17){
  //        $data["expireTime"]= "2016-06-11 15:55:00";
  //    }
  //
  //    $value = self::post_curl($url,$data,30);
  //
  //    $data['password'] = "******";
  //    $loggerAll->info(var_export($data,true));
  //    $loggerAll->info("addBalance:".$value."|Time:". (self::current_millis() - $startTime));
  //    try{
  //        $array = explode("|",$value);
  //        if(count($array)>=1) {
  //            return array("errorCode" => $array[0], "message" => $array[1]);
  //        }
  //    }catch (Exception $e){
  //        $data['password'] = "******";
  //        $loggerAll->info("[CALL_WEBSERVICE][ERROR]  method=[addBalance] params = [" . var_export($data, true) . "]| exception=[" . var_export($e->getMessage(), true) . "|message=" . $e->getMessage() . "]");
  //    }
  //    return null;
  //}


  public static function viewBalance($msisdn, $accountType)
  {
    $start = date('YmdHis');
    $startTime = VtHelper::getMilliTime();
    $loggerAll = VtHelper::getLogger4Php("all");
    $url = sfConfig::get('app_balance_link', "http://10.58.52.23:8670/provisioning");
    $vtEncryption = new vtEncryption();
    $password = $vtEncryption->decode(sfConfig::get('app_balance_password'));
    $data = array(
      "username" => sfConfig::get('app_balance_username'),
      "password" => $password,
      "msisdn" => $msisdn,
      "type" => "viewBalance",
      "accountType" => $accountType,
      "partyCode" => "ITSME_CONG_TIEN",
      "balance" => 0
    );
    $result = array("errorCode" => -555);
    try {
      $value = self::post_curl($url, $data, 30);
      if($value) {
        $data['password'] = "******";
        $loggerAll->info(var_export($data, true));
        $loggerAll->info("viewBalance:" . $value . "|Time:" . (self::current_millis() - $startTime));
        $array = explode("|", $value);
        if (count($array) >= 2) {
          $result = array("errorCode" => $array[0], "balance" => $array[1] * -100);
        }
        $errorCode = VtLogKpiStatus::OK;
        $message = '';
      }else{
        $errorCode = VtLogKpiStatus::TIMEOUT;
        $message = 'Timeout';
      }
    } catch (Exception $e) {
      $errorCode = VtLogKpiStatus::ERROR;
      $message = $e->getMessage();
      $data['password'] = "******";
      $loggerAll->info("[CALL_WEBSERVICE][ERROR]  method=[viewBalance] params = [" . var_export($data, true) . "]| exception=[" . var_export($e->getMessage(), true) . "|message=" . $e->getMessage() . "]");
    }
    //ghi log thoi gian goi ws
    VtHelper::writeLogKpi('vtshopWs',sprintf('%s/%s',__CLASS__,__FUNCTION__), VtLogKpiType::WS,$errorCode,$start, VtHelper::getMilliTime() - $startTime,$message);

    return $result;
  }

  //http://10.58.52.9/smsApi.php/registerYoutubePackage
  //params :
  //-	u : sms
  //-	p : SmS#@!456
  //-	phone : SĐT
  //-	syntax :
  //o	      T70
  //o	      T120
  //o	      T200
  //o	      T300
  //o	      MIMAX15
  //o	      T0
  //
  public static function register3g3($msisdn, $packageName, $source = 0, $sendSms = 1)
  {
    $start = date('YmdHis');
    $startTime = VtHelper::getMilliTime();
    $url = sfConfig::get('app_data_gw_url', "http://10.58.52.23:8756/smsApi.php/registerYoutubePackage");
//        if($packageName=="Mimax1.5" || $packageName=="MIMAX1.5"){
//            $packageName = "MIMAX15";
//        }
    $username = sfConfig::get('app_data_gw_username');
    $vtEnc = new vtEncryption();
    $password = $vtEnc->decode(sfConfig::get('app_data_gw_password'));
    $data = array(
      "u" => $username,
      "p" => $password,
      "phone" => $msisdn,
      "syntax" => $packageName,
      "channel" => 0,
      "source" => $source,
    );
    $printParams = $data;
    $printParams['p'] = '******';

    $loggerAll = VtHelper::getLogger4Php("data3g");
    $loggerAll->info($msisdn . '|registerData3g|' . var_export($printParams, true));

    $value = self::post_curl($url, $data);
    $resultRegisterObj = json_decode($value);
    if(is_object($resultRegisterObj)){
      $errorCode = VtLogKpiStatus::OK;
      $kpiStatus = VtLogKpiVtNetStatus::SUCCESS;
      $message = '';
    }else{
      $errorCode = VtLogKpiStatus::TIMEOUT;
      $kpiStatus = VtLogKpiVtNetStatus::FAIL;
      $message = 'Timeout';
    }
    //ghi log thoi gian goi ws
    VtHelper::writeLogKpi('vtshopWs',sprintf('%s/%s',__CLASS__,__FUNCTION__), VtLogKpiType::WS,$errorCode,$start, VtHelper::getMilliTime() - $startTime,$message);
    // Ghi log KPI VTNET
    $hostPort = VtHelper::getHostPortFromUrl($url);

    $logParams = array(
      'ApplicationCode' => sprintf('%s/%s', __CLASS__, __FUNCTION__),
      'ServiceCode' => VtLogKpiVtNetType::DATA3G,
      'SessionID' => session_id(),
      'IP_Port_ParentNode' => $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'],
      'IP_Port_CurrentNode' => $hostPort,
      'RequestContent' => json_encode($printParams),
      'ResponseContent' => $value ? json_encode($value) : '',
      'StartTime' => $startTime,
      'EndTime' => VtHelper::getMilliTime(),
      'Duration' => VtHelper::getMilliTime() - $startTime,
      'ErrorCode' => $resultRegisterObj ? $resultRegisterObj->errorCode : '',
      'ErrorDescription' => $resultRegisterObj ? $resultRegisterObj->message : '',
      'TransactionStatus' => $kpiStatus,
      'ActionName' => 'Dang_ky_goi_cuoc_data',
      'UserName' => $data['u'],
      'Account' => '',
    );
    VtHelper::writeLogKpiVtNet($logParams);
    return $value;
  }

  static function post_curl($_url, $_data, $timeoutSecond = 0)
  {
    try {
      $mfields = '';
      foreach ($_data as $key => $val) {
        $mfields .= $key . '=' . $val . '&';
      }
      rtrim($mfields, '&');
      $pst = curl_init();
      curl_setopt($pst, CURLOPT_URL, $_url);
      curl_setopt($pst, CURLOPT_POST, count($_data));
      curl_setopt($pst, CURLOPT_POSTFIELDS, $mfields);
      curl_setopt($pst, CURLOPT_RETURNTRANSFER, 1);
      if ($timeoutSecond != 0) {
        curl_setopt($pst, CURLOPT_TIMEOUT, $timeoutSecond); //so giay timeout hoac su dung milisecond voi CURLOPT_TIMEOUT_MS
        curl_setopt($pst, CURLOPT_CONNECTTIMEOUT, 2); //so giay timeout khi ket noi
      }
      $res = curl_exec($pst);
      curl_close($pst);
    } catch (Exception $ex) {
      return json_encode(array("errorCode" => -1, "message" => "Loi goi WS"));
    }
    return $res;
  }

  public static function registerVtFree($params)
  {
    $start = date('YmdHis');
    $startTime = VtHelper::getMilliTime();
    $vtEncrypt = new vtEncryption();
    $paramBccs = array(
      'user' => sfConfig::get('app_vtfree_user'),
      'pass' => $vtEncrypt->decode(sfConfig::get('app_vtfree_pass')),
      'msisdn' => VtHelper::getMobileNumber($params['msisdn'], VtHelper::MOBILE_GLOBAL),
      'product_name' => $params['product_name'],
      'sent_mt' => $params['sent_mt']
    );

    $logger = VtHelper::getLogger4Php('vtfree');
    $logger->info($params['msisdn'] . '|registerVtFree|' . var_export($paramBccs, true));
    try {
      $soapClient = new SoapClient(sfConfig::get('app_vtfree_wsdl'));
      $response = $soapClient->__soapCall('Register', array($paramBccs));
      if($response) {
        $res = $response->return;
        if ($res->errorCode == 0) {
          $message = "Đăng ký thành công";
          $errorCode = 0;
        } else {
          $errorCode = $res->errorCode;
          if (in_array($errorCode, array(2, 3, 7)))
            $message = sprintf('Đăng ký gói cước %s thất bại. Vui lòng liên hệ 18008168. Trân trọng', $params['product_name']);
          else
            $message = $res->description;
        }
        $messageLog = $res->description;
        $messageLogTime = '';
        $errorLogTime = VtLogKpiStatus::OK;
      }else{
        $errorCode = -1;
        $message = sprintf('Đăng ký gói cước %s thất bại. Vui lòng liên hệ 18008168. Trân trọng', $params['product_name']);
        $messageLogTime = $messageLog = 'Timeout';
        $errorLogTime = VtLogKpiStatus::TIMEOUT;
      }
    } catch (Exception $e) {
      $message = sprintf('Đăng ký gói cước %s thất bại. Vui lòng liên hệ 18008168. Trân trọng', $params['product_name']);
      $messageLog = $messageLogTime = $e->getMessage();
      $errorCode = -1;
      $errorLogTime = VtLogKpiStatus::ERROR;
    }
    //ghi log thoi gian goi ws
    VtHelper::writeLogKpi('vtshopWs',sprintf('%s/%s',__CLASS__,__FUNCTION__), VtLogKpiType::WS,$errorLogTime,$start, VtHelper::getMilliTime() - $startTime,$messageLogTime);
    $logger->info(sprintf('%s|registerVtFree|%s|%s', $params['msisdn'], $errorCode, $messageLog));
    return array(
      'errorCode' => $errorCode,
      'message' => $message,
      'description' => isset($res) ? $res->description : $message,
    );
  }

  public static function getListVtFreePromotion($phone)
  {
    $start = date('YmdHis');
    $startTime = VtHelper::getMilliTime();
    $vtEncrypt = new vtEncryption();
    $paramBccs = array(
      'user' => sfConfig::get('app_vtfree_user'),
      'password' => $vtEncrypt->decode(sfConfig::get('app_vtfree_pass')),
      'msisdn' => VtHelper::getMobileNumber($phone, VtHelper::MOBILE_GLOBAL)
    );

    $logger = VtHelper::getLogger4Php('vtfree');
    $listPackage = array();
    try {
      $soapClient = new SoapClient(sfConfig::get('app_vtfree_wsdl'));
      $response = $soapClient->__soapCall('checkProduct', array($paramBccs));
      if($response) {
        $res = $response->return;
        if ($res->errorCode == 0)
          $listPackage = $res->listProductVtFree;
        $errorCode = $res->errorCode;
        $message = $res->description;
        $messageLogTime = '';
        $errorCodeLogTime = VtLogKpiStatus::OK;
      }else{
        $errorCode = -1;
        $messageLogTime = $message = 'Timeout';
        $errorCodeLogTime = VtLogKpiStatus::TIMEOUT;
      }
    } catch (Exception $e) {
      $errorCode = -1;
      $message = $messageLogTime = $e->getMessage();
      $errorCodeLogTime = VtLogKpiStatus::ERROR;
    }
    //ghi log thoi gian goi ws
    VtHelper::writeLogKpi('vtshopWs',sprintf('%s/%s',__CLASS__,__FUNCTION__), VtLogKpiType::WS,$errorCodeLogTime,$start, VtHelper::getMilliTime() - $startTime,$messageLogTime);
    $logger->info(sprintf('getListVtFreePromotion|%s|%s', $errorCode, $message));
    return array(
      'errorCode' => $errorCode,
      'listPackage' => $listPackage
    );
  }

  public static function getListVtFreePromotionCache($phone) {
    $vt3gSettingKey = "vt_free_my_promote_key_" . md5($phone);
    $result = Vt3gSetting::getKeyCache($vt3gSettingKey);
    $timeCache = sfConfig::get("app_vt_free_cache", 600);
    if(!$result){
      $result = self::getListVtFreePromotion($phone);
      Vt3gSetting::setKeyCache($vt3gSettingKey,$result,$timeCache);
    }
    return $result;
  }

  public static function checkCanRegisterAddon($msisdn, $listPackage)
  {
    $start = date('YmdHis');
    $startTime = VtHelper::getMilliTime();
    $logger = VtHelper::getLogger4Php("data3g");
    $wsdl = "http://192.168.176.213:8788/WebServices/DataWS?wsdl";
    $location = "http://192.168.176.213:8788/WebServices/DataWS";
    $method = "checkAddOn";
    $options = array(
      'connect_timeout' => 1,
      'timeout' => 5,
      'cache_wsdl' => WSDL_CACHE_NONE,
      'location' => $location,
    );
    try {
      $logger->info("checkCanRegisterAddon:" . $wsdl);
      $params = array();
      $params['user'] = "TTVAS_OP";
      //sfConfig::get('app_addon_username');
      $params['password'] = "RD53@5datgfsgeopera";
      //sfConfig::get('app_addon_password');
      $params['msisdn'] = $msisdn;
      $params['listPackage'] = $listPackage;
      $client = new TimeoutSoapClient($wsdl, $options);
      $response = $client->__soapCall($method, array($params));
      if($response) {
        $stdClass = $response->return;
        $errorCodeLogTime = VtLogKpiStatus::OK;
        $messageLogTime = '';
      }else{
        $errorCodeLogTime = VtLogKpiStatus::TIMEOUT;
        $messageLogTime = 'Timeout';
        $stdClass = null;
      }
      $params['password'] = "******";
      $logger->info("[CALL_WEBSERVICE] method=[" . $method . "] params = [" . var_export($params, true) . "]| result=[" . var_export($stdClass, true) . "]");
    } catch (Exception $e) {
      $errorCodeLogTime = VtLogKpiStatus::ERROR;
      $messageLogTime = $e->getMessage();
      $params['password'] = "******";
      $logger->info("[CALL_WEBSERVICE][ERROR]  method=[" . $method . "] params = [" . var_export($params, true) . "]| exception=[" . var_export($e->getMessage(), true) . "|message=" . $e->getMessage() . "]");
      $stdClass = null;
    }
    //ghi log thoi gian goi ws
    VtHelper::writeLogKpi('vtshopWs',sprintf('%s/%s',__CLASS__,__FUNCTION__), VtLogKpiType::WS,$errorCodeLogTime,$start, VtHelper::getMilliTime() - $startTime,$messageLogTime);
    return $stdClass;
  }

  public static function checkCanRegisterMI($msisdn, $listPackage)
  {
    $start = date('YmdHis');
    $startTime = VtHelper::getMilliTime();
    $logger = VtHelper::getLogger4Php("data3g");
    $wsdl = "http://192.168.176.213:8788/WebServices/DataWS?wsdl";
    $location = "http://192.168.176.213:8788/WebServices/DataWS";
    $method = "checkDataMI";
    $options = array(
      'connect_timeout' => 1,
      'timeout' => 5,
      'cache_wsdl' => WSDL_CACHE_NONE,
      'location' => $location,
    );
    try {
      $logger->info("checkCanRegisterMI:" . $wsdl);
      $params = array();
      $params['user'] = "TTVAS_OP";
      $params['password'] = "RD53@5datgfsgeopera";
      $params['msisdn'] = $msisdn;
      $params['listPackage'] = $listPackage;
      $client = new TimeoutSoapClient($wsdl, $options);
      $response = $client->__soapCall($method, array($params));
      if($response) {
        $stdClass = $response->return;
        $errorCodeLogTime = VtLogKpiStatus::OK;
        $messageLogTime = '';
      }else{
        $errorCodeLogTime = VtLogKpiStatus::TIMEOUT;
        $messageLogTime = 'Timeout';
        $stdClass = null;
      }
      $params['password'] = "******";
      $logger->info("[CALL_WEBSERVICE] method=[" . $method . "] params = [" . var_export($params, true) . "]| result=[" . var_export($stdClass, true) . "]");
      $logger->info("checkDataMI|Time:" . (self::current_millis() - $startTime));
    } catch (Exception $e) {
      $errorCodeLogTime = VtLogKpiStatus::ERROR;
      $messageLogTime = $e->getMessage();
      $params['password'] = "******";
      $logger->info("[CALL_WEBSERVICE][ERROR]  method=[" . $method . "] params = [" . var_export($params, true) . "]| exception=[" . var_export($e->getMessage(), true) . "|message=" . $e->getMessage() . "]");
      $stdClass = null;
    }
    //ghi log thoi gian goi ws
    VtHelper::writeLogKpi('vtshopWs',sprintf('%s/%s',__CLASS__,__FUNCTION__), VtLogKpiType::WS,$errorCodeLogTime,$start, VtHelper::getMilliTime() - $startTime,$messageLogTime);
    return $stdClass;

  }

  public static function checkCanRegisterDataplus($msisdn, $listPackage)
  {
    $start = date('YmdHis');
    $startTime = VtHelper::getMilliTime();
    $logger = VtHelper::getLogger4Php("data3g");
    $wsdl = "http://10.58.52.23:8868/WebserviceDataPlus?wsdl";
    $method = "checkByListProduct";
    $options = array(
      'connect_timeout' => 1,
      'timeout' => 5,
      'cache_wsdl' => WSDL_CACHE_NONE
    );
    try {
      $logger->info("checkCanRegisterDataplus:" . $wsdl);
      $params = array();
      $params['user'] = "sale_digitals";
      $params['pass'] = "digitals@2016052";
      $params['msisdn'] = $msisdn;
      $params['listProduct'] = $listPackage;
      $client = new TimeoutSoapClient($wsdl, $options);
      $response = $client->__soapCall($method, array($params));
      if($response) {
        $stdClass = $response->return;
        $errorCodeLogTime = VtLogKpiStatus::OK;
        $messageLogTime = '';
      }else{
        $stdClass = null;
        $errorCodeLogTime = VtLogKpiStatus::TIMEOUT;
        $messageLogTime = 'Timeout';
      }
      $params['password'] = "******";
      $logger->info("[CALL_WEBSERVICE] method=[" . $method . "] params = [" . var_export($params, true) . "]| result=[" . var_export($stdClass, true) . "]");
    } catch (Exception $e) {
      $errorCodeLogTime = VtLogKpiStatus::ERROR;
      $messageLogTime = $e->getMessage();
      $params['password'] = "******";
      $logger->info("[CALL_WEBSERVICE][ERROR]  method=[" . $method . "] params = [" . var_export($params, true) . "]| exception=[" . var_export($e->getMessage(), true) . "|message=" . $e->getMessage() . "]");
      $stdClass = null;
    }

    //ghi log thoi gian goi ws
    VtHelper::writeLogKpi('vtshopWs',sprintf('%s/%s',__CLASS__,__FUNCTION__), VtLogKpiType::WS,$errorCodeLogTime,$start, VtHelper::getMilliTime() - $startTime,$messageLogTime);
    return $stdClass;
  }

  public static function wsCheckRegisterData3g($msisdn, $typeCheck, $syntax)
  {
    $canRegister = '';
    $errorCode = 1;
    //kiem tra goi khuyen mai
    if ($typeCheck == Vt3gSettingTypeCheckEnum::ADDON) {
      $result = VtRegisterAddonWS::checkCanRegisterAddon($msisdn, $syntax);
      if (isset($result->packageName)) {
        $canRegister = $result->packageName;
        $errorCode = 0;
      }
    } elseif ($typeCheck == Vt3gSettingTypeCheckEnum::MI) {
      $result = VtRegisterAddonWS::checkCanRegisterMI($msisdn, $syntax);
      if (isset($result->packageName)) {
        $canRegister = $result->packageName;
        $errorCode = 0;
      }
    } elseif ($typeCheck == Vt3gSettingTypeCheckEnum::DATA_PLUS) {
      $result = VtRegisterAddonWS::checkCanRegisterDataplus($msisdn, $syntax);
      if (isset($result->listProduct)) {
        $canRegister = $result->listProduct;
        $errorCode = 0;
      }
    }

    return array('errorCode' => $errorCode, 'listRegister' => $canRegister);
  }

  public static function checkCanRegisterMICache($msisdn, $listPackage)
  {
    $logger = VtHelper::getLogger4Php("data3g");
    $key_check_register = "cache_mi_" . md5($msisdn . "_" . $listPackage);
    $result = Vt3gSetting::getKeyCache($key_check_register);
    if (!$result) {
      $result = VtRegisterAddonWS::checkCanRegisterMI($msisdn, $listPackage);
      Vt3gSetting::setKeyCache($key_check_register, $result, 300);
    } else {
      $logger->info("GetCache|Key:" . $key_check_register . "|CanRegister:" . var_export($result, true));
    }
    return $result;
  }

  public static function checkCanRegisterAddonCache($msisdn, $listPackage)
  {
    $logger = VtHelper::getLogger4Php("data3g");
    $key_check_register = "cache_addon_" . md5($msisdn . "_" . $listPackage);
    $result = Vt3gSetting::getKeyCache($key_check_register);
    if (!$result) {
      $result = VtRegisterAddonWS::checkCanRegisterAddon($msisdn, $listPackage);
      Vt3gSetting::setKeyCache($key_check_register, $result, 300);
    } else {
      $logger->info("GetCache|Key:" . $key_check_register . "|CanRegister:" . var_export($result, true));
    }
    return $result;
  }

  public static function checkCanRegisterDataplusCache($msisdn, $listPackage)
  {
    $logger = VtHelper::getLogger4Php("data3g");
    $key_check_register = "cache_dataplus_" . md5($msisdn . "_" . $listPackage);
    $result = Vt3gSetting::getKeyCache($key_check_register);
    if (!$result) {
      $result = VtRegisterAddonWS::checkCanRegisterDataplus($msisdn, $listPackage);
      Vt3gSetting::setKeyCache($key_check_register, $result, 300);
    } else {
      $logger->info("GetCache|Key:" . $key_check_register . "|CanRegister:" . var_export($result, true));
    }
    return $result;
  }

  public static function getHotChargeAndStaOfCycle($subId,$contractId){
    $url = sfConfig::get('app_vtp_bccs_gw_url', "");
    $vtEncryption = new vtEncryption();
    $args = array(
      'userName' => 'test',
      'passWord' => 'test',
      'subId' => $subId,
      'contractId' => $contractId,
    );
    $startTime = VtHelper::getMilliTime();
    $client = new SoapClient($url, array('username' => 'vtp','password' => 'password'));
    try {
      $result = $client->__soapCall('getHotChargeAndStaOfCycle', array($args));
      $endTime = VtHelper::getMilliTime();
      if ($result) {
        $code = $result->return->msgCode;
        $message = 'success';
        if ($code == '1') {
          return $result->return;
        }
      }else {
        $code = -1;
        $message = 'timeOut';
      }
    } catch (Exception $e) {
      $code = -1;
      $message = $e->getMessage();
      $endTime = VtHelper::getMilliTime();
    }


    return false;
  }
}
