<?php

/**
 * Created by PhpStorm.
 * User: anhbhv
 * Date: 04/12/2017
 * Time: 9:18 SA
 */
class PaymentGatewayWS
{
    public $key = 'd41d8cd98f00b204e9800998ecf8427e0fa91b0aae01d7a68930a7855d2e3d56';
    public $access_code = "d41d8cd98f00b204e9800998ecf8427e17a8b105b6bd44f7efd2fa56fdc1ba29";
    public $merchant_code = "MYVIETTELV2";
//  public $key = 'd41d8cd98f00b204e9800998ecf8427e68831ac6c457f6b6d77bade0cb50ba51';
//  public $access_code = "d41d8cd98f00b204e9800998ecf8427eb3113362f4df34d5103db608abca8676";
//  public $merchant_code = "MYVIETTEL19";
    public $billCode = "";
    public $locale = "Vi";
    public $command = "";
    public $currency = "VND";
    public $orderId = "";
    public $trans_amount = "0";
    public $version = "2.0";
    public $return_url = "";
    public $cancel_url = "";
    public $desc = "";
    public $response_code = "";

    public function __construct($orderId = "", $billCode = "", $amount = "", $desc = "", $command = "PAYMENT", $merchant_code="MYVIETTELV2")
    {
        $this->orderId = $orderId;
        $this->billCode = $billCode;
        $this->command = $command;
        $this->trans_amount = $amount;
        $this->desc = $desc;
        $this->merchant_code = $merchant_code;
        if($merchant_code == "MYVIETTELV2"){
          $this->key = 'd41d8cd98f00b204e9800998ecf8427e0fa91b0aae01d7a68930a7855d2e3d56';
          $this->access_code = "d41d8cd98f00b204e9800998ecf8427e17a8b105b6bd44f7efd2fa56fdc1ba29";
        }elseif($merchant_code == "MYVIETTEL5"){
          $this->key = 'd41d8cd98f00b204e9800998ecf8427ebf818d4abc164b55c6d4253117e44f63';
          $this->access_code = "d41d8cd98f00b204e9800998ecf8427e0b0fbd73b1df94a337d571d70060e935";
        }
    }

    public function getRequestUrl($isdn = null)
    {
        $data = $this->access_code . $this->billCode . $this->command . $this->merchant_code . $this->orderId . $this->trans_amount . $this->version;
//    $link = 'http://125.235.40.34:8801/PaymentGateway/payment?';
        $link = 'https://pay.bankplus.vn:8450/PaymentGateway/payment?';
        $params =
            array(
                "version" => $this->version,
                "command" => $this->command,
                "billcode" => $this->billCode,
                "login_msisdn" => $isdn,
                "merchant_code" => $this->merchant_code,
                "order_id" => $this->orderId,
                "trans_amount" => $this->trans_amount,
                "locale" => $this->locale,
                "desc" => $this->desc,
                "return_url" => $this->return_url,
                "check_sum" => $this->getHmacSHA1($data),
            );
        $paramString = http_build_query($params);
        $link = $link . $paramString;
        return $link;
    }

    public function getHmacSHA1($strInput)
    {
        return base64_encode(hash_hmac("sha1", $strInput, $this->key, TRUE));
    }

    public function checkTransaction($orderId)
    {
        $logger = VtHelper::getLogger4Php('all');
        $url = "http://10.60.102.181:8888/vtp/API_REFUND_CTT/e561a7aa-6fe6-4669-aadd-b39f7e277eb8";
        $inputChecksum = $this->access_code . "TRANS_INQUIRY" . $this->merchant_code . $orderId . $this->version;
        $params = [
            'cmd' => "TRANS_INQUIRY",
            "merchant_code" => $this->merchant_code,
            "order_id" => $orderId,
            "version" => $this->version,
            "check_sum" => $this->getHmacSHA1($inputChecksum)
        ];
        $logger->info(sprintf("[JOB_PROCESS][PaymentGatewayWS.checkTransaction] BEGIN CHECK | params: %s", json_encode($params)));
        try {
            $curl = curl_init($url);
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
            curl_setopt($curl, CURLOPT_TIMEOUT, 1000);
            $response = curl_exec($curl);
            curl_close($curl);
            $logger->info(sprintf("[JOB_PROCESS][PaymentGatewayWS.checkTransaction] %s | response: %s", $orderId, json_encode($response)));
            $logFields['results'] = json_encode($response);
            VtHelper::writeLogValue($logFields);

            return json_decode($response);
        } catch (Exception $e) {
            $logger->info(sprintf("[JOB_PROCESS][PaymentGatewayWS.checkTransaction] %s | ERROR: %s", $orderId, $e->getMessage()));
            $logFields['results'] = json_encode(['message' => $e->getMessage()]);
            VtHelper::writeLogValue($logFields);
            return false;
        }
    }

    public function refundMoney($orderId, $originalRequestId, $refundType, $trans_amount, $trans_content)
    {
        $logger = VtHelper::getLogger4Php('all');
        $url = "http://10.60.102.181:8888/vtp/API_REFUND_CTT/e561a7aa-6fe6-4669-aadd-b39f7e277eb8";
        $inputChecksum = $this->access_code . "REFUND_PAYMENT" . $this->merchant_code . $orderId . $originalRequestId . $refundType . $trans_amount . $this->version;
        $params = [
            'cmd' => "REFUND_PAYMENT",
            "merchant_code" => $this->merchant_code,
            "order_id" => $orderId,
            "originalRequestId" => $originalRequestId,
            "refundType" => $refundType,
            "trans_amount" => $trans_amount,
            "trans_content" => removeSignClass::removeSignKeepSpace($trans_content),
            "version" => $this->version,
            "check_sum" => $this->getHmacSHA1($inputChecksum)
        ];
        $logger->info(sprintf("[JOB_PROCESS][PaymentGatewayWS.refundMoney] BEGIN CHECK | params: %s", json_encode($params)));
        try {
            $curl = curl_init($url);
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
            curl_setopt($curl, CURLOPT_TIMEOUT, 60);
            $response = curl_exec($curl);
            curl_close($curl);
            $logger->info(sprintf("[JOB_PROCESS][PaymentGatewayWS.refundMoney] %s | response: %s", $orderId, json_encode($response)));

            $return = json_decode($response);
            if($return){
              if(isset($return->error_code) && $return->error_code == "00"){
                $errorCode = 0;
                $message = "success";
              }else{
                $errorCode = 1;
                $message = $return->error_msg;
              }
            }else{
              $errorCode = 500;
              $message = "Timeout";
            }
        } catch (Exception $e) {
            $logger->info(sprintf("[JOB_PROCESS][PaymentGatewayWS.refundMoney] %s | ERROR: %s", $orderId, $e->getMessage()));
            $errorCode = 500;
            $message = $e->getMessage();
        }

        return ["errorCode" => $errorCode, "message" => $message];
    }
}