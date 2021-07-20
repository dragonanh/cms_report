<?php

//$url = "https://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
//$inputChecksum = '';
////$params = [
////    'vnp_Version' => '2.0.0',
////    "vnp_Command" => 'querydr',
////    "vnp_TmnCode" => 'WEBVTEL1',
////    "vnp_TxnRef" => '61621',
////    "vnp_OrderInfo" => 'test noi dung tra ve',
////    "vnp_TransactionNo" => '13480609',
////    "vnp_TransDate" => date('YmdHis',strtotime('2021-03-26 14:45:24')),
////    "vnp_CreateDate" => date('YmdHis'),
////    "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
////
////];
//$params = [
//    'vnp_Version' => '2.0.0',
//    "vnp_Command" => 'refund',
//    "vnp_TmnCode" => 'WEBVTEL1',
//    "vnp_TxnRef" => '61621',
//    "vnp_Amount" => 1000000,
//    "vnp_OrderInfo" => 'test noi dung tra ve',
//    "vnp_TransactionNo" => '13480609',
//    "vnp_TransDate" => date('YmdHis',strtotime('2021-03-26 14:45:24')),
//    "vnp_CreateDate" => date('YmdHis'),
//    "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
//];
//ksort($params);
//$i = 0;
//$hashdata = "";
//foreach ($params as $key => $value) {
//    if ($i == 1) {
//        $hashdata .= '&' . $key . "=" . $value;
//    } else {
//        $hashdata .= $key . "=" . $value;
//        $i = 1;
//    }
//}
//$params['vnp_SecureHash'] = hash('sha256','WCMPPNVICVISRJOEDNMOKZNFUSPPHLNT' . $hashdata);
//try {
//    $curl = curl_init($url);
//    curl_setopt($curl, CURLOPT_VERBOSE, false);
//    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($curl, CURLOPT_POST, true);
//    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
//    curl_setopt($curl, CURLOPT_HEADER, false);
//    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//        "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
//    ));
//    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
//    $response = curl_exec($curl);
//    var_dump($response);die();
//    curl_close($curl);
//
//    return json_decode($response);
//} catch (Exception $e) {
//    return false;
//}
require_once(dirname(__FILE__) . '/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
