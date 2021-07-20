<?php
///**
// * Created by PhpStorm.
// * User: tuanbm2
// * Date: 14/04/2016
// * Time: 9:01 SA
// */
//
//class VCGW {
//
//    public static function checkCondition($msisdn){
//        $minMoney = sfConfig::get('app_balance_money', 0);
//        $result = self::checkBalance($msisdn);
//        if(result==null){
//            //TH loi, ko goi dc ws checkbalance
//        }else if($result=="0"){
//            //thue bao tra sau dang hoat dong 2 chieu
//            return true;
//        }else{
//            $params = explode("|", $result);
//            if($params[0]==0){
//                $myMoney = $params[1];
//                if($myMoney>=$minMoney){
//                    return true;
//                }
//            }else{
//                //TH goi balance gap loi:
//                return true;
//            }
//        }
//        return false;
//    }
//
//    public static function checkBalance($msisdn) {
//        $msisdn = VtHelper::getMobileNumber($msisdn,VtHelper::MOBILE_NOTPREFIX);
//        $logger = VtHelper::getLogger4Php("all");
//        $wsdl =
//            //sfConfig::get('app_checkBalance_webservice');
//            sfConfig::get('sf_app_config_dir') . DIRECTORY_SEPARATOR . "vcgw.wsdl";
//        $options = array(
//            'connect_timeout' => sfConfig::get('app_checkBalance_timeout', 5),
//            'timeout' => sfConfig::get('app_checkBalance_timeout', 5),
//            'cache_wsdl' => WSDL_CACHE_NONE,
//        );
//        try {
//            $params = array();
//            $params['username'] = sfConfig::get('app_checkBalance_username');
//            $params['password'] = sfConfig::get('app_checkBalance_password');
//            $params['providerid'] = sfConfig::get('app_checkBalance_providerid');
//            $params['serviceId'] = sfConfig::get('app_checkBalance_serviceId');
//            $params['msisdn'] = $msisdn;
//            $params['charging'] = 0;
//            $params['reqTime'] = date("YmdHis");
//            $params['cmd'] = "CHKBALANCE";
//            $params['contents'] = "CHK BALANCE ADDON";
//            $params['requestId'] = uniqid();
//            $client = new TimeoutSoapClient($wsdl, $options);
//            $response = $client->__soapCall("processCharging", array($params));
//            $stdClass = $response->return;
//            $params['password'] = "******";
//            $logger->info("[CALL_WEBSERVICE] method=[processCharging] params = [" . var_export($params, true) . "]| result=[" . var_export($stdClass, true) . "]");
//            return $stdClass;
//        } catch (Exception $e) {
//            $logger->info("[CALL_WEBSERVICE][ERROR]  method=[processCharging] params = [" . var_export($params, true) . "]||message=" . $e->getMessage() . "]");
//            return null;
//        }
//    }
//}