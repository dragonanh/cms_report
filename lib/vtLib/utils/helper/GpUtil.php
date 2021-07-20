<?php

/*
 * @(#)GpUtil.php 1.0 21/12/2010 4:54:32 PM
 * can merge
 *  Copyright 2006 Viettel Telecom. All rights reserved.
 *  VIETTEL PROPRIETARY/CONFIDENTIAL. Use is subject to license terms.
 */

/**
 * @author thanhtl
 * @since 21/12/2010
 */
class GpUtil
{
  const WAP_THUMBS_DIR = '.wapthumbs';
  const GAME_PORTAL_URL = 'http://10.60.34.9:8859/';
  const GA_ACCOUNT = 'MO-5995907-3';

  /**
   * Generate string random
   * @author TungNT53
   * @static
   * @param $length
   * @return string
   */
  public static final function generateRandomString($length)
  {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    $size = strlen($chars);
    for ($i = 0; $i < $length; $i++) {
      $str .= $chars[rand(0, $size - 1)];
    }
    return $str;
  }

  public static final function getSafeOutXSS($data)
  {
    // Fix &entity\n;
    $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do {
      // Remove really unwanted tags
      $old_data = $data;
      $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    } while ($old_data !== $data);

    return $data;
  }

  public static final function checkToken(sfWebRequest $request, $obj)
  {
    $form = new TokenForm();
    $serverToken = $obj->getUser()->getAttribute($form->getCSRFFieldName());
    $clientToken = $request->getParameter($form->getCSRFFieldName());

    if ($serverToken != $clientToken) {
      die("CSRF attack detected.");
    }
  }

  /**
   * convert attribute to array
   * @param <type> $attr
   * return array
   */
  public static final function attr2Arr($attr)
  {
    if (!$attr) return;
    $len = (int)(log($attr) / log(2) + 1);
    $current = 1;
    $arr = array();
    for ($i = 0; $i < $len; $i++) {
      //bit hien tai dang bat??
      if ($attr & $current) {
        $arr[] = $current;
      }
      $current = $current << 1;
    }
    return $arr;
  }

  /**
   * convert array to atribute
   * @param int $attr
   * @return <type>
   */
  public static final function arr2Attr(array $arr)
  {
    $current = 0;
    foreach ($arr as $item) {
      $current = $current | $item;
    }
    return $current;
  }

  public static final function validUrl($url)
  {
    return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?|i', $url);
  }

  /**
   * Like explode function, but trim string and remove empty string
   * @param string $delimiter
   * @param string $string
   * @param int $limit
   * @param <type> $trim
   * @return <type>
   */
  public static final function strExplode($delimiter, $string, $trim = true)
  {
    $rtrArr = array();
    $exArr = explode($delimiter, $string);
    foreach ($exArr as $value) {
      if ($trim) $value = trim($value);
      if ($value) {
        $rtrArr[] = $value;
      }
    }
    return $rtrArr;
  }

  /**
   * Xem MIME cua file hien tai la gi
   *
   * Su dung 2 ham de doan mime file.
   * @param <type> $file
   */
  public static final function mime($file)
  {
    //using finfo
    if (function_exists('finfo_open') || !is_readable($file)) {
      if (!$finfo = new finfo(FILEINFO_MIME)) {
        return null;
      }
      $type = @$finfo->file($file);
      // remove charset (added as of PHP 5.3)
      if (false !== $pos = strpos($type, ';')) {
        $type = substr($type, 0, $pos);
      }

      return $type;
    }
    //using mime_content_type
    if (!function_exists('mime_content_type') || !is_readable($file)) {
      return @mime_content_type($file);
    }
  }

  /**
   * chuyen kieu ngay trong mysql theo dinh dang $format
   * @param string $sqlDate
   * @param string $format
   * @return string
   */
  public static final function date($sqlDate, $format = 'd/m/Y')
  {
    return date($format, strtotime($sqlDate));
  }

  /*     * *
   * lay handset_id
   */

  public static final function getHandsetId()
  {
    $hsetArr = self::getHandSet();
    if (!empty($hsetArr) && is_array($hsetArr) && isset($hsetArr['id'])) {
      return $hsetArr['id'];
    } elseif (!empty($hsetArr) && is_object($hsetArr)) {
      return $hsetArr->id;
    } elseif (!empty($hsetArr)) {
      return $hsetArr;
    }
    /*         * return null* */
    return null;
  }

  /**
   * get all handset save in session
   * @return array handset
   */
  public static final function getHandSet()
  {
    return sfContext::getInstance()->getUser()->getAttribute('handset', array());
  }

  /**
   * add handset to session
   * @param GpHandset $handset handset
   * @return boolean true if ok, false else
   */
  public static final function addHandSet($handset)
  {
    if ($handset instanceof GpHandset) {
      if (empty($handset->id)) {
        sfContext::getInstance()->getUser()->getAttributeHolder()->remove('handset');
        return false;
      }
      sfContext::getInstance()->getUser()->setAttribute('handset',
        array('id' => $handset->getId(), 'model' => $handset->getModel()));
    } elseif (is_array($handset)) {
      sfContext::getInstance()->getUser()->setAttribute('handset', $handset);
    } else {
      //            sfContext::getInstance()->getUser()->getAttributeHolder()->remove('handset');
      sfContext::getInstance()->getUser()->setAttribute('handset', null);
      sfContext::getInstance()->getUser()->offsetUnset('handset');
      return false;
    }

    return true;
  }

  public static final function getCurrentCustId()
  {
    $cust = sfContext::getInstance()->getUser()->getCustomer();
    if (!empty($cust)) return $cust->getId();
    return null;
  }


  public static final function getCurrentMsisdn()
  {
    $user = sfContext::getInstance()->getUser();
    if (!empty($user)) return $user->offsetGet('msisdn');
    return null;
  }

  /**
   *
   * @param GpGame $gpGame
   * @return <type>
   */
  public static final function currentGamePrice($gpGame)
  {
    list ($price, $mcoin) = $gpGame->getSaleOffPrice();
    return $price;
  }

  /**
   *
   * @param GpGame $gpGame
   * @return <type>
   */
  public static final function currentGameMcoin($gpGame)
  {
    list ($price, $mcoin) = $gpGame->getSaleOffPrice();
    return $mcoin;
  }

  /*     * *
   * tao link wappush
   */

  public static final function genLinkWappush($gpGame, $msisdn, $trans_detail_id)
  {
    if ($trans_detail_id instanceof GpTransactionDetail) {
      $trans_detail_id = $trans_detail_id->id;
    }
    $maxCodeLen = sfConfig::get('app_code_download_len');
    $code = uniqid(dmString::random($maxCodeLen - 13));
    $wappush = new GpWapPush();
    $wappush->setMsisdn($msisdn);
    $wappush->setCode($code);
    $wappush->setTransDetailId($trans_detail_id);
    if ($gpGame instanceof GpGame) {
      $wappush->setGpGame($gpGame);
    } else {
      $wappush->setGameId($gpGame);
    }
    $expire_time = dmConfig::get('wappush_expire_hours');
    $expire_time *= 3600;
    $wappush->setExpireDate(date('Y-m-d H:i:s', time() + $expire_time));
    $wappush->setDownloadCount(0);
    $wappush->save();
    //        include_once('symfony/helper/UrlHelper.php');
    //return url
    $request = sfContext::getInstance()->getRequest();

    //todo. fixit
    return self::GAME_PORTAL_URL . 'm/download/' . $code;
  }

  /**
   * todo: fixit
   * @param <type> $msisdn
   * @return <type>
   */
  public static final function convertMsisdn($msisdn)
  {
    $msisdn = trim($msisdn);
    $start = substr($msisdn, 0, 1);
    if (empty($msisdn)) return null;
    if ($start == '0') {
      $msisdn = '84' . substr($msisdn, 1);
    } elseif (in_array($start, array('1', '9'))) {
      $msisdn = '84' . $msisdn;
    }

    return $msisdn;
  }

  public static function checkViettelNumber($msisdn)
  {
    $array_9 = array('8497', '8498', '8496');
    $array_10 = array('84164', '84165', '84166', '84167', '84168', '84169', '84163', '84162', '84161');

    $msisdn = GpUtil::convertMsisdn($msisdn);
    if (strlen($msisdn) == 12 || strlen($msisdn) == 11) {
      $start_9 = substr($msisdn, 0, 4);
      $start_10 = substr($msisdn, 0, 5);
      if (in_array($start_9, $array_9) || in_array($start_10, $array_10)) return true;
    }
    return false;
  }

  /**
   *
   * @param <type> $id
   * @param <type> $len
   * @return <type>
   */
  public static final function getPromotionCode($id, $len = 7)
  {
    $rand = rand(100, 999);
    if (strlen($id) < $len) {
      return $rand . str_pad($id, $len, '0', STR_PAD_LEFT);
    } else {
      return $rand . substr($id, -$len);
    }
  }

  public static final function endcodeMsisdn($msisdn)
  {
    $key = sfConfig::get('app_aes_key');
    $aes = new AES($key);
    return base64_encode($aes->encrypt(GpUtil::convertMsisdn($msisdn)));
  }

  /**
   * lay file name tu file_id va file_name phia client gui len
   */
  public static final function getFileName($fileId, $fileName)
  {
    return $fileId . '-' . $fileName;
  }

  /*     * *
   * create $path/yy/mm dir and return it;
   */

  public static final function createYYMMDir($path, $time = null)
  {
    $path = dmOs::join(sfConfig::get('sf_web_dir'), $path);
    if ($time == null) $time = time();
    if (is_dir($path)) {
      $path = dmOs::join($path, date('Y/m', $time));
      $oldUmask = umask(0);
      @mkdir($path, 0777, true);
      umask($oldUmask);
      if (is_dir($path) && is_writable($path)) {
        return $path;
      }
      return false;
    }
  }

  /*     * *
   * output $msg avoid xss
   *
   */

  private static $filter = null;

  public static function out($msg, $encodeHtml = true)
  {
    echo self::getSafeOut($msg, $encodeHtml);
  }

  public static function getSafeOut($msg, $encodeHtml = true)
  {
    if ($encodeHtml) {
      return htmlspecialchars($msg, ENT_COMPAT, 'UTF-8');
    } else {
      if (self::$filter == null) self::$filter = new GpInputFilter ();
      return self::$filter->stripUnsafeTag($msg);
    }
  }


  /*     * *
  * convert to human readable date
  */

  public static function hDate($time)
  {
    if (!is_numeric($time) && $t = strtotime($time)) {
      $time = $t;
    }
    $denta = time() - $time;
    if ($denta > 12 * 60 * 60 || $denta < 0) {
      return date('d/m/Y H:i:s', $time);
    }
    $h = floor($denta / 3600);
    $m = floor($denta / 60 - $h * 60);
    $msg = 'khoảng ';
    if ($h) $msg .= $h . ' giờ ';
    else $msg .= ($m ? $m : '1') . ' phút';
    $msg .= ' trước';
    return $msg;
  }

  public static function checkValidMbNumber($msisdn)
  {
    if (!is_numeric(trim($msisdn))) {
      return false;
    }
    if (!GpUtil::checkViettelNumber(trim($msisdn))) {
      return false;
    }
    return true;
  }

  /**
   * Lay ra anh da dc resize, neu anh chua resize thi thu hien resize
   * Coi thu muc web la thu muc goc
   * @param <type> $path duong dan
   * @param <type> $fileName ten file
   * @param <type> $w chieu rong
   * @param <type> $h chieu cao
   * @return <type> duong dan den file anh
   */
  public static function imgResize($path, $fileName, $w, $h)
  {
    $thumbName = 'thumb' . $w . $h . $fileName;
    $thumbDir = self::WAP_THUMBS_DIR;
    $fullPath = dmOs::join(sfConfig::get('sf_web_dir'), $path);
    //        die($fullPath);
    //file dua vao ko ton tai, return anh mac dinh
    if (!file_exists(dmOs::join($fullPath, $fileName)) || !is_readable(dmOs::join($fullPath, $fileName))) {
      sfContext::getInstance()->getLogger()->err('File ' . dmOs::join($fullPath, $fileName) . ' not exist or readable!');
      return '/mobile/images/unknow.gif';
    }
    //tao thu muc thumbs neu khong ton tai
    if (!is_dir($thumbFullDir = dmOs::join(sfConfig::get('sf_web_dir'), $path, $thumbDir))) {
      mkdir($thumbFullDir, 0777, true);
    }
    //neu da ton tai thumb roi thi return lai file thumb do
    if (file_exists(dmOs::join($thumbFullDir, $thumbName))) {
      return dmOs::join($path, $thumbDir, $thumbName);
    }
    //neu chua ton tai thumb thi tao moi
    try {
      $adapter = GpUtil::isAnimationGifImage(dmOs::join($fullPath, $fileName)) ? 'sfImageMagickAdapter' : 'sfGDAdapter';
      $thumbnail = new sfThumbnail($w, $h, true, true, 95, $adapter);
      try {
        $thumbnail->loadFile(dmOs::join($fullPath, $fileName));
      } catch (Exception $e) {
        GpUtil::error("Exception when create thumbs. Adapter $adapter " . $e);
        if ($adapter == 'sfGDAdapter') {
          $thumbnail = new sfThumbnail($w, $h, true, true, 95, 'sfImageMagickAdapter');
          $thumbnail->loadFile(dmOs::join($fullPath, $fileName));
        } else {
          return '/mobile/images/unknow.gif';
        }
      }
      $thumbnail->save(dmOs::join($thumbFullDir, $thumbName));
    } catch (Exception $ex) {
      sfContext::getInstance()->getLogger()->err('Exception when create thumbs ' . $ex);
      return '/mobile/images/unknow.gif';
    }
    return dmOs::join($path, $thumbDir, $thumbName);
  }

  public static function getUserAgent()
  {
    $userAgent = '';
    if (isset($_GET['UA'])) $userAgent = $_GET['UA'];
    elseif (isset($_SERVER['HTTP_X_DEVICE_USER_AGENT']))
      $userAgent = $_SERVER['HTTP_X_DEVICE_USER_AGENT'];
    elseif (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']))
      $userAgent = $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'];
    elseif (isset($_SERVER['HTTP_USER_AGENT'])) $userAgent = $_SERVER['HTTP_USER_AGENT'];
    return $userAgent;
  }

  /**
   * convert pem to der
   * @param <type> $pem_data
   * @return <type>
   */
  public static function pem2der($pem_data)
  {
    $begin = "KEY-----";
    $end = "-----END";
    $pem_data = substr($pem_data, strpos($pem_data, $begin) + strlen($begin));
    $pem_data = substr($pem_data, 0, strpos($pem_data, $end));
    $der = base64_decode($pem_data);
    return $der;
  }

  /**
   * convert der to pem
   * @param <type> $der_data
   * @return string
   */
  public static function der2pem($der_data, $private = true)
  {
    $pem = chunk_split($der_data, 64, "\n");
    if ($private) {
      $pem = "-----BEGIN RSA PRIVATE KEY-----\n" . $pem . "-----END RSA PRIVATE KEY-----\n";
    }
    return $pem;
  }

  /**
   *
   * @param <type> $key
   * @param <type> $value
   * @return <type>
   */
  public static function tripleDesEncrypt($key, $value)
  {
    $key = base64_decode($key);
    $iv = substr($key, -8);
    $key = substr($key, 0, 24);
    $value = mcrypt_encrypt(MCRYPT_3DES, $key, $value, MCRYPT_MODE_CBC, $iv);
    return bin2hex($value);
  }

  /**
   * get cache
   * @param string $cacheName
   * @return GpMetaCache
   */
  public static function getCache($cacheName)
  {
    $context = dmContext::getInstance();
    if ($context instanceof dmContext) {
      $tmp = $context->getServiceContainer()->getService('cache_manager');
      if ($tmp) {
        $a = $tmp->getCache($cacheName);
        return $a;
      }
    }
    $context->set('cache_manager',
      new dmCacheManager($context->getEventDispatcher(), array('meta_cache_class' => 'GpMetaCache')));
    return $context->get('cache_manager')->getCache($cacheName);
  }

  /**
   * Get client ip
   * @return <type>
   */
//  public static function getRemoteIp()
//  {
//    $ip = '--unknown--';
//    /* if (isset($_SERVER['HTTP_X_REAL_IP']) && ip2long($_SERVER['HTTP_X_REAL_IP']) && !self::inPriateIpRange($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//      $ip = $_SERVER['HTTP_X_REAL_IP'];
//      } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ip2long($_SERVER['HTTP_X_FORWARDED_FOR']) && !self::inPriateIpRange($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//      } else */
////    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
////      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
////    }
//
//    $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ?: $_SERVER['HTTP_X_FORWARDED_FOR'] ?: $_SERVER['REMOTE_ADDR'] ?: false;
//
//    return $ip;
//  }

  /**
   * Get client ip
   * @return <type>
   */
  public static function getRemoteIp()
  {
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
      $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
      $ip = $forward;
    } else {
      $ip = $remote;
    }

    return $ip;
  }

  /**
   * xet xem ip có thuoc dải private ko
   * @param <type> $ip
   */
  public static function inPriateIpRange($ip)
  {
    $ip = ip2long($ip);
    //        echo ip2long('10.0.0.0') . "\n";
    //        echo ip2long('10.255.255.255') . "\n";
    //        echo ip2long('172.16.0.0') . "\n";
    //        echo ip2long('172.31.255.255') . "\n";
    //        echo ip2long('127.0.0.0') . "\n";
    //        echo ip2long('127.0.0.255') . "\n";
    //        echo ip2long('192.168.0.0') . "\n";
    //        echo ip2long('192.168.255.255') . "\n";
    return ($ip >= 167772160 && $ip <= 184549375) ||
    ($ip >= -1408237568 && $ip <= -1407188993) ||
    ($ip >= -1062731776 && $ip <= -1062666241) ||
    ($ip >= 2130706432 && $ip <= 2130706687);
  }

  /**
   * xet xem ip co thuoc tu ipmin den ipmax ko
   * @param <type> $ip
   * @param <type> $ipmin
   * @param <type> $ipmax
   * @return <type>
   */
  public static function ipInRange($ip, $ipmin, $ipmax)
  {
    $ip = sprintf('%u', ip2long($ip));
    $ipmin = sprintf('%u', ip2long($ipmin));
    $ipmax = sprintf('%u', ip2long($ipmax));

    return $ip >= $ipmin && $ip <= $ipmax;
  }

  /**
   * Build download code cho game
   * @param GpGame $game
   * @return <type>
   */
  public static function buildDownloadCode(GpGame $game)
  {
    $cp = $game->getGpContentProvider();
    if ($cp) {
      return $cp->code . $game->id;
    }
    return '';
  }

  /**
   * get gameportal tmpdir
   */
  public static function getTmpDir()
  {
    return dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('app_upload_tmp_dir'));
  }

  /**
   *get current user cp id
   * @return type
   */
  public static function getCpId()
  {
    return sfContext::getInstance()->getUser()->getDmUser()->cp_id;
  }

  /**
   * @Author: utvtn@viettel.com.vn
   * get current Cp_code
   * @static
   * @return string
   */
  public static function getCpCode()
  {
    $cp = sfContext::getInstance()->getUser()->getDmUser()->GpContentProvider;
    if ($cp) {
      return $cp->code;
    }
    return '';
  }

  //END

  /**
   *get current user CP
   * @return type
   */
  public static function getCurrentContentProvider()
  {
    return sfContext::getInstance()->getUser()->getDmUser()->GpContentProvider;
  }

  public static function  debug($message)
  {
    if (sfContext::getInstance()->getConfiguration()->isDebug()) {
      sfContext::getInstance()->getLogger()->debug($message);
    }
  }

  public static function  error($message)
  {
    sfContext::getInstance()->getLogger()->err($message);
  }

  public static function  info($message)
  {
    sfContext::getInstance()->getLogger()->info($message);
  }

  /**
   * check ip co thuoc 1 dai mang hay khong. 192.168.133.31 co thuoc 192.168.133.0/24 ko?
   * @param type $IP
   * @param type $CIDR
   * @return type
   */
  public static function ipCIDRCheck($IP, $CIDR)
  {
    list ($net, $mask) = explode("/", $CIDR);

    $ip_net = ip2long($net);
    $ip_mask = ~((1 << (32 - $mask)) - 1);

    $ip_ip = ip2long($IP);

    $ip_ip_net = $ip_ip & $ip_mask;

    return ($ip_ip_net == $ip_net);
  }

  /**
   *
   * @global type $GA_ACCOUNT
   * @global type $GA_PIXEL
   * @return type
   */
  public static function googleAnalyticsGetImageUrl()
  {
    // Tracker version.
    define("VERSION", "4.4sh");

    define("COOKIE_NAME", "__utmmobile");

    // The path the cookie will be available to, edit this to use a different
    // cookie path.
    define("COOKIE_PATH", "/");

    // Two years in seconds.
    define("COOKIE_USER_PERSISTENCE", 63072000);

    $timeStamp = time();
    $domainName = $_SERVER["SERVER_NAME"];
    if (empty($domainName)) {
      $domainName = "upro.vn";
    }

    // Get the referrer from the utmr parameter, this is the referrer to the
    // page that contains the tracking pixel, not the referrer for tracking
    // pixel.
    $documentReferer = @$_SERVER["HTTP_REFERER"];
    if (empty($documentReferer) && $documentReferer !== "0") {
      $documentReferer = "-";
    } else {
      $documentReferer = urldecode($documentReferer);
    }
    $documentPath = @$_SERVER["REQUEST_URI"];
    if (empty($documentPath)) {
      $documentPath = "";
    } else {
      $documentPath = urldecode($documentPath);
    }

    $account = GpUtil::GA_ACCOUNT;
    $userAgent = @$_SERVER["HTTP_USER_AGENT"];
    if (empty($userAgent)) {
      $userAgent = "";
    }

    // Try and get visitor cookie from the request.
    $cookie = $_COOKIE[COOKIE_NAME];

    $visitorId = GpUtil::_GAGetVisitorId(
      @$_SERVER["HTTP_X_DCMGUID"], $account, $userAgent, $cookie);

    // Always try and add the cookie to the response.
    setrawcookie(
      COOKIE_NAME, $visitorId, $timeStamp + COOKIE_USER_PERSISTENCE, COOKIE_PATH);

    $utmGifLocation = "http://www.google-analytics.com/__utm.gif";

    // Construct the gif hit url.
    $utmUrl = $utmGifLocation . "?" .
      "utmwv=" . VERSION .
      "&utmn=" . rand(0, 0x7fffffff) .
      "&utmhn=" . urlencode($domainName) .
      "&utmr=" . urlencode($documentReferer) .
      "&utmp=" . urlencode($documentPath) .
      "&utmac=" . $account .
      "&utmcc=-" .
      "&utmvid=" . $visitorId .
      "&utmip=" . GpUtil::_GAGetIP($_SERVER["REMOTE_ADDR"]);
    return $utmUrl;
  }

  // Generate a visitor id for this hit.
  // If there is a visitor id in the cookie, use that, otherwise
  // use the guid if we have one, otherwise use a random number.
  private static function _GAGetVisitorId($guid, $account, $userAgent, $cookie)
  {

    // If there is a value in the cookie, don't change it.
    if (!empty($cookie)) {
      return $cookie;
    }

    $message = "";
    if (!empty($guid)) {
      // Create the visitor id using the guid.
      $message = $guid . $account;
    } else {
      // otherwise this is a new user, create a new random id.
      $message = $userAgent . uniqid(rand(0, 0x7fffffff), true);
    }

    $md5String = md5($message);

    return "0x" . substr($md5String, 0, 16);
  }

  // The last octect of the IP address is removed to anonymize the user.
  private static function _GAGetIP($remoteAddress)
  {
    if (empty($remoteAddress)) {
      return "";
    }

    // Capture the first three octects of the IP address and replace the forth
    // with 0, e.g. 124.455.3.123 becomes 124.455.3.0
    $regex = "/^([^.]+\.[^.]+\.[^.]+\.).*/";
    if (preg_match($regex, $remoteAddress, $matches)) {
      return $matches[1] . "0";
    } else {
      return "";
    }
  }

  /**
   *
   * @return string
   */
  public static function googleAnalyticsWrapperImageUrl()
  {
    $url = "/ga.php?";
    $url .= "utmac=" . GpUtil::GA_ACCOUNT;
    $url .= "&utmn=" . rand(0, 0x7fffffff);

    $referer = $_SERVER["HTTP_REFERER"];
    $query = $_SERVER["QUERY_STRING"];
    $path = $_SERVER["REQUEST_URI"];

    if (empty($referer)) {
      $referer = "-";
    }
    $url .= "&utmr=" . urlencode($referer);

    if (!empty($path)) {
      $url .= "&utmp=" . urlencode($path);
    }

    $url .= "&guid=ON";

    return $url;
  }


  /**
   * kiem tra xem file anh co phai la anh dong khong??
   * @param type $fileName
   * @return type
   */
  public static function isAnimationGifImage($filename)
  {
    return (bool)preg_match('/\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)/s', file_get_contents($filename),
      $m);
  }

  /**
   * Anh quang cao.
   * Neu la anh dong, chi lay duong dan, khong thay doi kich thuoc
   * Neu la anh binh thuong, thuc hien resize, tra ve duong dan anh
   */
  public static function advtiseMedia($media = null, $w = 0, $h = 0, $scale = false)
  {
    if ($media == null || !$media->isImage()) {
      return '/mobile/images/unknow.gif';
    }

    if ($media->get('file') == '' || (!$media->checkFileExists())) {
      return '/mobile/images/unknow.gif';
    }
    $relThumbDir = '/uploads/' . $media->get('Folder')->getRelPath() . '/';
    if (self::isAnimationGifImage(dmOs::join(sfConfig::get('sf_web_dir'), $relThumbDir, $media->get('file')))) {
      return dmOs::join($relThumbDir, $media->get('file'));
    } else {
      return self::imgResize($relThumbDir, $media->get('file'), $w, $h);
    }
  }


  public static function isZipFile($fileName)
  {
    return dmOs::getFileExtension($fileName) == '.zip';
  }

  // huync2
  const MOBILE_09x = 12;
  const MOBILE_9x = 13;
  const MOBILE_849x = 14;

  public static function formatMobileNumber($phone, $type = self::MOBILE_09x)
  {
    $phone = (string)$phone;
    // Bo het cac ky tu khong phai so
    $phone = preg_replace('@[^0-9]@', '', $phone);
    if ($type == self::MOBILE_09x) {
      if (substr($phone, 0, 1) == '0') { #0975292582
        # do nothing
      } elseif (substr($phone, 0, 2) == '84') { #+84975292582
        $phone = '0' . substr($phone, 2);
      } else { #975292582
        $phone = '0' . $phone;
      }
    } elseif ($type == self::MOBILE_849x) {
      if (substr($phone, 0, 1) == '0') { #0975292582
        $phone = '84' . substr($phone, 1);
      } elseif (substr($phone, 0, 2) == '84') { #+84975292582
        # do nothing
      } else { #975292582
        $phone = '84' . $phone;
      }
    } elseif ($type == self::MOBILE_9x) {
      if (substr($phone, 0, 1) == '0') { #0975292582
        $phone = substr($phone, 1);
      } elseif (substr($phone, 0, 2) == '84') { #+84975292582
        $phone = substr($phone, 2);
      } else { #975292582
        # do nothing
      }
    } else {
      throw new Exception('Format dien thoai khong hop le', 113);
    }

    return $phone;
  }


}

?>
