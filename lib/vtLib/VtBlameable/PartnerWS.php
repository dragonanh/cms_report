<?php
/**
 * Created by PhpStorm.
 * User: huyvq
 * Date: 30/10/2020
 * Time: 3:58 PM
 */

class PartnerWS
{
    const WSDL_URBOX = 'http://10.58.71.186:8087/merchant/login';
    const WSDL_URBOX_USER = 'toprate';
    const WSDL_URBOX_PASS = 'toprate@123';

    private $i18n;
    public $version = "2.0.0";
    public $merchant_code = "";
    public $vnp_HashSecret = '';
    public $url_vnp = 'https://sandbox.vnpayment.vn/merchant_webapi/merchant.html';
    public function __construct($merchant_code = "WEBVTEL1")
    {
        $this->i18n = sfContext::getInstance()->getI18N();
        $this->merchant_code = $merchant_code;
        if($merchant_code == "WEBVTEL1"){
          $this->vnp_HashSecret = "WCMPPNVICVISRJOEDNMOKZNFUSPPHLNT";
        }else
          $this->vnp_HashSecret = "HQZOPEJZZQKCCBQRRGOZHLMAKVWPIZBB";
    }

    public function checkTransactionRefundPoint($tranId){
      return ["errorCode" => 0, "message" => "success"];
    }

    /*ham tru diem viettel ++ */
    public function _plusPoint($msisdn, $amount, $transId)
    {
        $logFields = array(
            'actionType' => 'PartnerWS',
            'title' => "minusPoint",
            'service' => "PartnerWS.minusPoint",
            'creatorId' => $msisdn,
            'objectId' => $transId,
        );
        $config = $this->getConfig();
        $url = $config["wsdlViettelId"];
        $params = [
            "isdn" => GpUtil::formatMobileNumber($msisdn,GpUtil::MOBILE_9x),
            "pointAmount" => $amount,
            "transTypeId" => $config["transTypeIdMinus"],
            "pointId" => $config["pointId"],
            "transId" => $transId
        ];

        try {
            $curl = curl_init($url);
            $userPassAuth = "vtp:password";
            curl_setopt($curl, CURLOPT_VERBOSE, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "userName:".$config["viettelIdUser"],
                "passWord:".$config["viettelIdPass"],
                "Content-Type: application/json",
                'Authorization: Basic ' . base64_encode($userPassAuth)
            ));
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($curl);
            curl_close($curl);

            $response = json_decode($response);

            $logFields["content"] = json_encode(["input" => $params, "response" => json_encode($response)]);
            if($response){
                if($response->code == "000"){
                    $errorCode = 0;
                    $message = $this->i18n->__("Giao dịch thành công");
                }elseif($response->code == "006" || $response->code == "010"){
                    $errorCode = 1;
                    $message = $this->i18n->__("Số điểm tích lũy của Khách hàng không đủ để thực hiện giao dịch");
                }else{
                    $errorCode = 2;
                    $message = $this->i18n->__("Giao dịch thất bại, Quý khách vui lòng thử lại");
                }
            }else{
                $errorCode = 500;
                $message = $this->i18n->__("Giao dịch thất bại, Quý khách vui lòng thử lại");
            }

            $logFields['results'] = json_encode(["errorCode" => $errorCode, "message" => $message]);
        } catch (Exception $e) {
            $errorCode = 500;
            $message = $this->i18n->__("Giao dịch thất bại, Quý khách vui lòng thử lại");
            $logFields["results"] = json_encode(["errorCode" => 500, "message" => $e->getMessage()]);

        }
        $this->insertLogElastic($logFields);
        return ["errorCode" => $errorCode, "message" => $message];
    }

    public function refundVNPAYMoney($orderId, $trans_amount, $trans_content,$transaction_type,$pay_date,$user)
    {

        $logger = VtHelper::getLogger4Php('all');
        $url = $this->url_vnp;
        if($transaction_type == 1){
            $vnp_TransactionType = '03';
        }else{
            $vnp_TransactionType = '02';
        }
        $params = [
            'vnp_Version' => $this->version,
            "vnp_Command" => 'refund',
            "vnp_TmnCode" => $this->merchant_code,
            "vnp_TxnRef" => $orderId,
            "vnp_CreateBy" => $user,
            'vnp_TransactionType' => $vnp_TransactionType,
            "vnp_Amount" => $trans_amount,
            "vnp_OrderInfo" => $trans_content,
            "vnp_TransDate" => date('YmdHis',strtotime($pay_date)),
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
        ];

        ksort($params);
        $i = 0;
        $hashdata = "";
        $query = "";
        foreach ($params as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $vnp_Url = $url . "?" . $query;

        $vnpSecureHash = hash('sha256',$this->vnp_HashSecret . $hashdata);
        $vnp_Url .= '&vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;

        $params['vnp_SecureHash'] = hash('sha256',$this->vnp_HashSecret . $hashdata);
        $logger->info(sprintf("[JOB_PROCESS][PartnerWS.refundVNPAYMoney] BEGIN CHECK | params: %s | %s", json_encode($params), $vnp_Url));
        try {
            $curl = curl_init($vnp_Url);
            $userPassAuth = "vtp:password";
            curl_setopt($curl, CURLOPT_VERBOSE, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
                'Authorization: Basic ' . base64_encode($userPassAuth)
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_PROXY, "192.168.193.12:3128");
            curl_setopt($curl, CURLOPT_TIMEOUT, 60);
            $response = curl_exec($curl);
            curl_close($curl);
            $logger->info(sprintf("[JOB_PROCESS][PartnerWS.refundVNPAYMoney] %s | response: %s", $orderId, json_encode($response)));
            $response = explode('&',$response);
            $result = [];
            foreach ($response as $key => $val){
                $result[strstr($val, "=",true)] = ltrim(strstr($val, "="),'=');
            }
            return $result;
        } catch (Exception $e) {
            $logger->info(sprintf("[JOB_PROCESS][PartnerWS.refundVNPAYMoney] %s | ERROR: %s", $orderId, $e->getMessage()));
            return false;
        }
    }

    public function checkVNPAYtransaction($orderId, $trans_content, $request_id, $pay_date)
    {
        $logger = VtHelper::getLogger4Php('all');
        $url = $this->url_vnp;
        $inputChecksum = '';
        $params = [
            'vnp_Version' => $this->version,
            "vnp_Command" => 'querydr',
            "vnp_TmnCode" => $this->merchant_code,
            "vnp_TxnRef" => $orderId,
            "vnp_OrderInfo" => $trans_content,
            "vnp_TransactionNo" => $request_id,
            "vnp_TransDate" => date('YmdHis',strtotime($pay_date)),
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],

        ];
        ksort($params);
        $i = 0;
        $hashdata = "";
        $query = "";
        foreach ($params as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $vnp_Url = $url . "?" . $query;
        if (isset($this->vnp_HashSecret)) {
            $vnpSecureHash = hash('sha256',$this->vnp_HashSecret . $hashdata);
            $vnp_Url .= '&vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
        }
        $params['vnp_SecureHash'] = hash('sha256',$this->vnp_HashSecret . $hashdata);
        $logger->info(sprintf("[JOB_PROCESS][PaymentGatewayWS.refundMoney] BEGIN CHECK | params: %s | %s", json_encode($params), $vnp_Url));
        try {
            $curl = curl_init($vnp_Url);
            $userPassAuth = "vtp:password";
            curl_setopt($curl, CURLOPT_VERBOSE, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
                'Authorization: Basic ' . base64_encode($userPassAuth)
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_PROXY, "192.168.193.12:3128");
            curl_setopt($curl, CURLOPT_TIMEOUT, 60);
            $response = curl_exec($curl);
            curl_close($curl);
            $logger->info(sprintf("[JOB_PROCESS][PartnerWS.checkVNPAYtransaction] %s | response: %s", $orderId, json_encode($response)));
            $response = explode('&',$response);
            $result = [];
            foreach ($response as $key => $val){
                $result[strstr($val, "=",true)] = ltrim(strstr($val, "="),'=');
            }
            return $result;
        } catch (Exception $e) {
            $logger->info(sprintf("[JOB_PROCESS][PartnerWS.checkVNPAYtransaction] %s | ERROR: %s", $orderId, $e->getMessage()));
            return false;
        }
    }

    public function getHmacSHA1($strInput)
    {
        return base64_encode(hash_hmac("sha1", $strInput, $this->key, TRUE));
    }

    public function insertLogElastic($logFields){
        $logger = VtHelper::getLogger4Php('all');
        $inputs = $_REQUEST;
        unset($inputs['actionName']);
        $defaultLogFields = array(
            'logType' => 'PartnerWS',
            'actionType' => 'PartnerWS',
            'title' => "",
            'service' => "",
            'objectTitle' => '',
            'webservices' => "",
            'inputs' => $inputs,
            'content' => '',
            'results' => "",
        );

        $logFields = array_merge($defaultLogFields, $logFields);
        $logger->info(json_encode($logFields));
    }

    /*ham dang ky data qua diem viettel ++ */
    /*PYC_3409754 validate login merchant*/
    public function SHAEncryptPasswordMerchant($algorithm, $salt, $password)
    {
        return call_user_func_array('hash', array($algorithm, $salt . $password));
    }


    public function getConfig(){
      $wsdlViettelId = 'http://10.60.102.181:8888/vtp/MloyaltyPointService/79796ca7-61b3-4ccf-afb2-db8112527c8d/adjustAccountPoint';
        $transTypeIdMinus = "1000139";
        $pointId = "1000001";
        $viettelIdUser = "viettelid_myviettel";
        $viettelIdPass = "Viettelid@123";

        return [
            'wsdlViettelId' => $wsdlViettelId,
            'transTypeIdMinus' => $transTypeIdMinus,
            'pointId' => $pointId,
            'viettelIdUser' => $viettelIdUser,
            'viettelIdPass' => $viettelIdPass,
        ];
    }
}
