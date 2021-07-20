<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VtHelper
 * @author vas_tungtd2
 */
class VtHelper {

    const MOBILE_SIMPLE = '09x';
    const MOBILE_GLOBAL = '849x';
    const MOBILE_NOTPREFIX = '9x';
    const VT_MSISDN_PATTERN = '/^(84|0?)(16[2-9]\d{7}|86\d{7}|9[6-8]\d{7}|3(5|2|4|6|8|3|7|9)\d{7})$/'; // So dien thoai viettel
    const VT_MOBILE_PATTERN = '/^(84|0?)(5\d{8}|[7-9]\d{8}|3[2-9]\d{7})$/';
    const FULL_NAME_PATTERN_WITH_UNICODE = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s?)+$~u';
    const USERNAME_PATTERN = '/^\w+$/';
    const SALT = 'whateveryouwant';
    const MY_PBKDF2_SALT = "\x2d\xb7\x68\x1a\x28\x15\xbe\x06\x33\xa0\x7e\x0e\x8f\x79\xd5\xdf";
    const CONVERT_OLD_TO_NEW = 1;
    const CONVERT_NEW_TO_OLD = 2;

    //convert = 0: khong thuc hien chuyen dau so
    //convert = 1: Chuyen tu dau so cu sang dau so moi
    //convert = 2: chuyen tu dau so moi sang dau so cu
    public static function getMobileNumber($msisdn, $type, $trim = true, $convert = 0) {
        if (empty($type))
            $type = self::MOBILE_SIMPLE;

        if ($trim)
            $msisdn = trim($msisdn);

        if (!$msisdn)
          return '';

        //loai bo so + dau tien doi voi dinh dang +84
        if ($msisdn[0] == '+') {
            $msisdn = substr($msisdn, 1);
        }

        if ($msisdn[0] == '0') {
          $msisdn = substr($msisdn, 1);
        } else if ($msisdn[0] . $msisdn[1] == '84' && strlen($msisdn) >= 11) {
          $msisdn = substr($msisdn, 2);
        }

        switch ($type) {
            case self::MOBILE_GLOBAL:
                $msisdn = '84'.self::convertPhone($msisdn, $convert);
                break;
            case self::MOBILE_SIMPLE:
                $msisdn = '0'.self::convertPhone($msisdn, $convert);
                break;
            case self::MOBILE_NOTPREFIX:
                $msisdn = self::convertPhone($msisdn, $convert);
                break;
            default:
              $msisdn = '';
        }

        return $msisdn;
    }

    public static function convertPhone($phone,$type){
      $whitelistChangeHead = sfConfig::get('app_white_list_change_head_number', array());
      if(!empty($whitelistChangeHead)) {
        switch ($type) {
          case self::CONVERT_OLD_TO_NEW: //chuyen tu dau so cu sang dau so moi
            foreach ($whitelistChangeHead as $oldHead => $newHead) {
              if (strpos($phone, (string)$oldHead) === 0) {
                $phone = $newHead . substr($phone, strlen($oldHead));
              }
            }
            break;
          case self::CONVERT_NEW_TO_OLD: //chuyen tu dau so moi sang dau so cu
            foreach ($whitelistChangeHead as $oldHead => $newHead) {
              if (strpos($phone, (string)$newHead) === 0) {
                $phone = $oldHead . substr($phone, strlen($newHead));
              }
            }
            break;
        }
      }
      return $phone;
    }

    /**
     * @description Neu so dien thoai dang la 016 --> chuyen thanh 03 nguoc lai neu la 03 --> chuyen thanh 016
     * @param $phoneNumber
     * @return bool|null|string
     */
    public static function reverseConvert016And03Format($phoneNumber){
      $phone = null;
      $phoneNotPrefix = VtHelper::getMobileNumber($phoneNumber, VtHelper::MOBILE_NOTPREFIX);
      $whitelistChangeHead = sfConfig::get('app_white_list_change_head_number', array());
      foreach ($whitelistChangeHead as $oldHead => $newHead){
        if(strpos($phoneNotPrefix, (string)$oldHead) === 0){
          //truong hop la dau so cu --> chuyen sang dau so moi
          $phone = VtHelper::getMobileNumber($phoneNotPrefix, VtHelper::MOBILE_GLOBAL, true, VtHelper::CONVERT_OLD_TO_NEW);
          break;
        }elseif(strpos($phoneNotPrefix, (string)$newHead) === 0){
          //truong hop la dau so moi --> chuyen ve dau so cu
          $phone = VtHelper::getMobileNumber($phoneNotPrefix, VtHelper::MOBILE_GLOBAL, true, VtHelper::CONVERT_NEW_TO_OLD);
          break;
        }
      }

      return $phone;
    }

    public static function checkOldPhoneFormat($phone){
      $phoneNotPrefix = VtHelper::getMobileNumber($phone, VtHelper::MOBILE_NOTPREFIX);
      $whitelistChangeHead = sfConfig::get('app_white_list_change_head_number', array());
      $isOldFormat = false;
      $headNumber = null;
      foreach ($whitelistChangeHead as $key => $head){
        if(strpos($phoneNotPrefix, (string)$key) === 0){
          $isOldFormat = true;
          $headNumber = '0'.$key;
        }
      }
      return array('isOldFormat' => $isOldFormat, 'headNumber' => $headNumber);
    }

    public static function truncate($text, $length = 30, $truncateString = '...', $truncateLastspace = true, $escSpecialChars = false) {
        if (sfConfig::get('sf_escaping_method') == 'ESC_SPECIALCHARS') {
            $text = htmlspecialchars_decode($text, ENT_QUOTES);
        }

        if (is_array($text)) {
            throw new dmException('Can not truncate an array: ' . implode(', ', $text));
        }

        $text = (string) $text;

        if (extension_loaded('mbstring')) {
            $strlen = 'mb_strlen';
            $substr = 'mb_substr';
//hatt12 them dong nay de dem ky tu tieng viet
            $countStr = $strlen($text, 'utf-8');
            if ($countStr > $length) {
                $text = $substr($text, 0, $length, 'utf-8');

                if ($truncateLastspace) {
                    $text = preg_replace('/\s+?(\S+)?$/', '', $text);
                }

                $text = $text . $truncateString;
            }
        } else {
            $strlen = 'strlen';
            $substr = 'substr';
            $countStr = $strlen($text);
            if ($countStr > $length) {
                $text = $substr($text, 0, $length);
                if ($truncateLastspace) {
                    $text = preg_replace('/\s+?(\S+)?$/', '', $text);
                }

                $text = $text . $truncateString;
            }
        }
        if ($escSpecialChars) {
            return $text;
        } else {
            return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * preProcess query search backsplash (\) tat ca doan code search like trong PHP ma can tim ky tu dac biet deu phai goi ham nay
     * @author tuanbm
     * @date 2012/06/12
     * @return string
     */
    public static function preProcessForSearchLike($param) {
        return addslashes($param);
    }

    //tuanbm ghi log
    public static function getLogger() {
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/VtHelper.log'));
        return $logger;
    }

    /**
     * log message from anywhere
     * @author huynq28
     * @param type $module
     * @param type $mesage
     * @return type
     */
    public static function logMessage($module, $mesage) {
        sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($module, 'application.log', $mesage));
    }

    public static function checkCSRF(sfWebRequest $request) {
        $baseForm = new BaseForm();
        $baseForm->bind($request->getParameter('form'));
        if (!$baseForm->isValid()) {
            throw $baseForm->getErrorSchema();
        }
    }

    /**
     * @author ChuyenNV2
     * get a string with number character input
     * @static
     * @param $strInput
     * @param $maxString
     * @return string
     */
    public static function getStringMaxLength($strInput, $maxString) {
        //tuanbm su dung 1 ham duy nhat, fix loi tren ham getLimitString
        return self::getLimitString($strInput, $maxString);
    }

    public static function subString($str, $length = 22, $truncateString = '...', $truncateLastspace = true) {
        $str = self::replaceSpecialCharsFromWord($str);
        $str = (string) $str;
        if (extension_loaded('mbstring')) {
            $strlen = 'mb_strlen';
            $substr = 'mb_substr';
        } else {
            $strlen = 'strlen';
            $substr = 'substr';
        }

        if ($strlen($str) > $length) {
            if ($substr == 'mb_substr') {
                $str = $substr($str, 0, $length - $strlen($truncateString), 'UTF-8');
            } else {
                $str = $substr($str, 0, $length - $strlen($truncateString));
            }
            if ($truncateLastspace) {
                $str = preg_replace('/\s+?(\S+)?$/', '', $str);
            }
            $str = $str . $truncateString;
        }
        return $str;
    }

    /**
     * @modified by vos_khanhnq16
     * @param string $strInput
     * @param type   $limit
     * @return type
     */
//  public static function getLimitString($strInput, $limit = 10)
//  {
//      //chuyennv2
//      if($strInput=='')
//	  return '';
//    //tuanbm2 them decode truoc khi substring
//    return vtSecurity::encodeOutput(VtHelper::subString(vtSecurity::decodeInput($strInput), $limit, '...', true));
//  }

    public static function replaceSpecialCharsFromWord($strInput) {
        $strInput = str_replace('“', '"', $strInput);
        $strInput = str_replace('�?', '"', $strInput);
        return $strInput;
    }

    public static function getLimitString($strInput, $limit = 10) {
        $strInput = self::replaceSpecialCharsFromWord($strInput);

        //chuyennv2
        if ($strInput == '')
            return '';
        //tuanbm2 them decode truoc khi substring
        $str = vtSecurity::encodeOutput(VtHelper::subString(vtSecurity::decodeInput($strInput), $limit, '...', true));
        //    if($str==""){
        //      return vtSecurity::encodeOutput(VtHelper::subString(vtSecurity::decodeInput($strInput), $limit, '...', false));
        //    }
        //    if (!$str && $strInput) {
        //      $str = vtSecurity::encodeOutput(substr(vtSecurity::decodeInput($strInput), 0, $limit - 3), '...', true);
        //      //      $str = vtSecurity::encodeOutput(substr(vtSecurity::decodeInput($strInput), 0, $limit - 3) . '...');
        //    }


        return $str;
    }

    //truong hop $strInput ko bi encode boi symfony, return tu dong encode anti XSS
    public static function getLimitStringWithoutEncode($strInput, $limit = 10) {
        $strInput = self::replaceSpecialCharsFromWord($strInput);

        $resultReturn = vtSecurity::encodeOutput(VtHelper::subString($strInput, $limit, '...', true));
        return $resultReturn;
    }

    /*
     * @author tuanbm
     * Ham gui email toi nguoi dung
     * @static
     * @param: $to: Email toi nguoi nhan
     * @param $title: tieu de email
     * @param $body: noi dung email
     * $return (The number of sent emails)
     * 0: la khong co email nao duoc gui di
     * 1: la gui email thanh cong
     * -1: la co loi gui email
     */

    public static function SendEmail($to, $title, $body) {
        $logger = VtHelper::getLogger4Php("all");
        try {
            $mailer = sfContext::getInstance()->getMailer();
            $from = "shopviettel@viettel.com.vn";
              //sfConfig::get('app_email_from');
            $message = $mailer->compose();
            $message->setSubject($title);
            $message->setTo($to);
            $message->setFrom($from);
            $message->setBody($body, 'text/html'); //text/plain
            //      $result = $mailer->composeAndSend($from, $to, $title, $body);
            $result = $mailer->send($message);
            $logger->info("EmailTo:".$to."|Title:".$title."|body:".$body."|Result:".$result);
            return $result;
        } catch (exception $e) {
            $logger = VtHelper::getLogger4Php("all");
            $logger->info($e->getMessage());
            //ghi log gui that bai
            return 0;
        }
    }

    public static function logInfo($objModule, $description) {
        try {
            $user = $objModule->getUser();
            $objectName = get_class($objModule);
            $actionName = $objModule->getActionName();
            $param = "";

            if ($actionName == "batch") {
                $actionName = $_POST['batch_action'];
                $ids = $_POST["ids"];
                $param = implode(",", $ids);
            }

            if ($user->getGuardUser() == null)
                $message = $objectName . "|" . $actionName . "|params:" . $param . "|" . $description;
            else
                $message = $objectName . "|" . $actionName . "|User:" . $user . "|params:" . $param . "|" . $description;

            //if (sfConfig::get('sf_logging_enabled')){
            //sfContext::getInstance()->getLogger()->notice($message);
            $objModule->logMessage($message, "notice");
            //}
        } catch (exception $e) {
            //      echo $e;
        }
    }

    /*
     * * Date: 2014/04/17
     * @author tuanbm
     * Ham get Image Thumbnail, de tra ve duong dan tuyet doi (Anh Thumbnail)
     * @static
     * return: link day du ca http://server/media/....
     * EX: $folderThumb="thumbnail",$width=150,$height=150,$configDefaultImage = "app_url_media_default_image"
     */

    private static function createImageThumbnail($imageName, $folderThumbName, $width, $height, $configDefaultImage = "app_url_media_default_image") {
        try {
            //tuanbm: thu check xem Main Image co ton tai khong, neu ton tai thi generate ra anh thumbnail (Cai nay Do Keeng migrate ve)
            $full_path_file = sfConfig::get("app_upload_media_images") . "/" . $folderThumbName . "/" . $imageName;
            $originalImage = sfConfig::get("app_upload_media_images") . "/" . $imageName;
            if (is_file($originalImage)) {
                $file_name = basename($full_path_file); //test.jpg
                $folderThumb = str_replace($file_name, "", $full_path_file); //duong dan file
                if (!is_dir($folderThumb)) {
                    @mkdir($folderThumb, 0777, true);
                }
                //neu ton tai $originalImage thi generate no ra anh thumbnail
                if ($height == 0) {
                    $thumbnail = new sfThumbnail($width, $height, true, true, 60);
                } else {
                    $thumbnail = new sfThumbnail($width, $height, false, true, 60);
                }

                $thumbnail->loadFile($originalImage);
                $thumbnail->save($full_path_file, 'image/jpeg');
                return sfConfig::get('app_url_media_images') . "/" . $folderThumbName . "/" . $imageName;
            }
            return sfConfig::get($configDefaultImage);
        } catch (Exception $ex) {
            return sfConfig::get($configDefaultImage);
        }
    }

    /*
     * Date: 2014/04/17
     * @author tuanbm
     * Ham get Image Thumbnail, de tra ve duong dan tuyet doi (Anh Thumbnail)
     * @static
     * return: link day du ca http://server/media/....

     */

    public static function generateStructurePath($uniqueFileName) {
        //$uiq = uniqid(1,true);
        //$fileName = hash('sha1',$uiq);
        $mash = 255;
        $hashCode = crc32($uniqueFileName); //md5(serialize($fileName));
        $firstDir = $hashCode & $mash;
        $firstDir = vsprintf("%02x", $firstDir);
        $secondDir = ($hashCode >> 8) & $mash;
        $secondDir = vsprintf("%02x", $secondDir);
        $thirdDir = ($hashCode >> 4) & $mash;
        $thirdDir = vsprintf("%02x", $thirdDir);
        return $firstDir . "/" . $secondDir . "/" . $thirdDir;
    }

    public static function getUrlImagePathThumb($imageName, $width = 200, $height = 200, $configDefaultImage = "app_url_media_default_image") {
        try {
            if (strlen($imageName) == 0) {

                return VtHelper::getThumbUrl(sfConfig::get($configDefaultImage), $width, $height);
            } else {
                //them 1 doan code check exits file, neu ko ton tai thi cung hidden di
                //u01/apps/imuzik/cms-web/web/uploads/images
                $imageName = ltrim($imageName, "/");
                $folderThumbnail = "thumbnail" . $width . "x" . $height;
                $filename = sfConfig::get("sf_upload_dir") . "/" . $folderThumbnail . "/" . $imageName;
                if (is_file($filename)) {
                    return sfConfig::get('app_url_media_images') . "/" . $folderThumbnail . "/" . $imageName;
                } else {
                    return self::createImageThumbnail($imageName, $folderThumbnail, $width, $height, $configDefaultImage);
                    //return sfConfig::get($configDefaultImage);
                }
            }
        } catch (Exception $e) {
            return VtHelper::getThumbUrl(sfConfig::get($configDefaultImage), $width, $height);
        }
    }

    public static function getUrlImagePath($imageName, $configDefaultImage = "app_url_media_default_image") {
        try {
            if (strlen($imageName) == 0) {
                return sfConfig::get($configDefaultImage);
            } else {
                $filename = sfConfig::get("sf_upload_dir") . $imageName;
                if (is_file($filename)) {
                    return sfConfig::get('app_url_media_images') . $imageName;
                } else {
                    return sfConfig::get($configDefaultImage);
                }
            }
        } catch (Exception $e) {
            return sfConfig::get($configDefaultImage);
        }
    }

    public static function getFullDirectoryImageFile($imageName) {
        return sfConfig::get('sf_upload_dir') . $imageName;
    }

    public static function renderImg($source, $w = null, $h = null, $class = null, $configDefaultImage = "app_url_media_default_image") {
        try {
            if (strlen($source) == 0) {
                return sfConfig::get($configDefaultImage);
            } else {
                $filename = sfConfig::get("sf_upload_dir"). $source;
                if (is_file($filename)) {
                    return sfConfig::get('app_url_media') . self::getThumbUrl($source, $w, $h, $configDefaultImage);
                } else {
                    return sfConfig::get($configDefaultImage);
                }
            }
        } catch (Exception $e) {
            return sfConfig::get($configDefaultImage);
        }
    }

    /**
     * @author hoangl
     * Ham loai bo tat ca cac the html
     * @static
     * @param       $str - Xau can loai bo tag
     * @param array $tags - Mang cac tag se strip, vi du: array('a', 'b')
     * @param bool  $stripContent
     * @return mixed|string
     */
    public static function encodeOutput($string, $force = true) {
        if (sfConfig::get('sf_escaping_strategy')
            && sfConfig::get('sf_escaping_method') == "ESC_SPECIALCHARS" && $force == false
        ) {
            return $string;
        } else {
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
            //        return htmlentities($string, ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * @author tuanbm2
     * Ham loai bo tat ca cac the html mac dinh loai bo array('script', 'iframe', 'noscript')
     * @static
     * @param       $str - Xau can loai bo tag
     * @param array $tags - Mang cac tag se strip, vi du: array('a', 'b')
     * @param bool  $stripContent
     * @example: echo VtHelper::strip_html_default_tags($article->getBody())
     * @return mixed|string
     */
    public static function strip_html_default_tags($str) {
        return VtHelper::strip_html_tags($str, array('script', 'iframe', 'noscript'));
    }

    /**
     * @author tuanbm2
     * Ham loai bo tat ca cac the html mac dinh loai bo array('script', 'iframe', 'noscript')
     * @static
     * @param $str - Ham nay chi duoc dung doi voi  sf_escaping_strategy = 1 va sf_escaping_method=ESC_SPECIALCHARS
     * @return string
     */
    public static function strip_html_tags_and_decode($str) {
      $str = htmlspecialchars_decode($str);
      $config = HTMLPurifier_Config::createDefault();
      $config->set('HTML.MaxImgLength', null);
      $config->set('CSS.MaxImgLength', null);
      $purifier = new HTMLPurifier($config);
      $clean_html = $purifier->purify($str);
      return $clean_html;

        //Ham nay chi duoc dung doi voi get du lieu hien thi sf_escaping_strategy = 1 va sf_escaping_method=ESC_SPECIALCHARS
        //tuyet doi ko dung de remove truoc khi save du lieu
        //do symfony tu dong encode HTML nen phai decode truoc khi remove Script
//        $str = htmlspecialchars_decode($str); //co the dung ham $object->getSomething(ESC_RAW);//htmlspecialchars_decode($str, ENT_QUOTES);
//        $str = VtHelper::strip_html_tags($str, array('script', 'iframe', 'noscript', 'embed'));
//        return str_replace('<embed ', '', $str);
    }

    public static function strip_html_tags_and_decode_noquote($str) {
        //Ham nay chi duoc dung doi voi get du lieu hien thi sf_escaping_strategy = 1 va sf_escaping_method=ESC_SPECIALCHARS
        //tuyet doi ko dung de remove truoc khi save du lieu
        //do symfony tu dong encode HTML nen phai decode truoc khi remove Script
        $str = htmlspecialchars_decode($str, ENT_NOQUOTES); //co the dung ham $object->getSomething(ESC_RAW);//htmlspecialchars_decode($str, ENT_QUOTES);
        $str = VtHelper::strip_html_tags($str, array('script', 'iframe', 'noscript', 'embed'));
        return str_replace('<embed ', '', $str);
    }

    /**
     * @author hoangl
     * Ham loai bo tat ca cac the html
     * @static
     * @param       $str - Xau can loai bo tag
     * @param array $tags - Mang cac tag se strip, vi du: array('a', 'b')
     * @param bool  $stripContent
     * @example: <?php echo VtHelper::strip_html_tags($article->getBody(), array('script', 'iframe', 'noscript'))?>
     * @return mixed|string
     */
    public static function strip_html_tags($str, $tags = array(), $stripContent = false) {
        if (empty($tags)) {
            $tags = array("br/", "hr/", "!--...--", '!doctype', 'a', 'abbr', 'address', 'area', 'article', 'aside', 'audio', 'b', 'base', 'bb', 'bdo', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'cite', 'code', 'col', 'colgroup', 'command', 'datagrid', 'datalist', 'dd', 'del', 'details', 'dfn', 'div', 'dl', 'dt', 'em', 'embed', 'eventsource', 'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'iframe', 'img', 'input', 'ins', 'kbd', 'keygen', 'label', 'legend', 'li', 'link', 'mark', 'map', 'menu', 'meta', 'meter', 'nav', 'noscript', 'object', 'ol', 'optgroup', 'option', 'output', 'p', 'param', 'pre', 'progress', 'q', 'ruby', 'rp', 'rt', 'samp', 'script', 'section', 'select', 'small', 'source', 'span', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'time', 'title', 'tr', 'ul', 'var', 'video', 'wbr');
        }
        $content = '';
        if (!is_array($tags)) {
            $tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
            if (end($tags) == '')
                array_pop($tags);
        }
        foreach ($tags as $tag) {
            if ($stripContent)
                $content = '(.+</' . $tag . '(>|\s[^>]*>)|)';
            $str = preg_replace('#</?' . $tag . '(>|\s[^>]*>)' . $content . '#is', '', $str);
        }

        $str = trim($str, ' ');

        return $str;
    }

    public static function strip_html_tags_no_script($str) {
        $str = VtHelper::strip_html_tags($str, array('script', 'iframe'));
        return $str;
    }

    /**
     * @author: ChuyenNV2
     * @param type $credentialModule
     * @return type
     */
    public static function isAdmin($credentialModule) {
        $isAdmin = (sfContext::getInstance()->getUser()->getGuardUser()->hasPermission('admin') == 1) ? true : false;
        $isAdminSong = (sfContext::getInstance()->getUser()->getGuardUser()->hasPermission($credentialModule) == 1) ? true : false;
        return ($isAdmin || $isAdminSong);
    }

    /**
     * author: thongnq1
     * ham thuc hien them id qua tang am nhac trong bai hat cu khi gan qua tang am nhac voi bai hat moi
     * @param type $mpSongId
     * @param type $songIdOld
     * @return string ids sau khi them id qua tang am nhac
     */
    public static function addMpSongIdInMpIds($idMpSong, $newSongId) {
        $song = VtSongTable::getInstance()->find($newSongId);
        if ($song != null) {
            $arrmpIdsNew = explode(',', $song->getMpIds());
            if (empty($arrmpIdsNew) || $song->getMpIds() == $idMpSong || $song->getMpIds() == '') {
                return $idMpSong;
            } else {
                $keyNew = array_search($idMpSong, $arrmpIdsNew);
                if (!$keyNew) {
                    return $mpidsOld = '"' . implode(',', $arrmpIdsNew) . ',' . $idMpSong . '"';
                } else {
                    return $mpidsOld = '"' . implode(',', $arrmpIdsNew) . '"';
                }
            }
        }
        return 'none';
    }

    /**
     * author: thongnq1
     * ham thuc hien xoa id qua tang am nhac trong bai hat cu khi gan qua tang am nhac voi bai hat moi
     * @param type $mpSongId
     * @param type $songIdOld
     * @return string ids sau khi xoa id qua tang am nhac
     */
    public static function removeMpSongIdInMpIds($idMpSong, $oldSongId) {
        $song = VtSongTable::getInstance()->find($oldSongId);
        if ($song != null) {
            if ($song->getMpIds() == $idMpSong) {
                return '"' . '"';
            } else {
                $arrmpIdsOld = explode(',', $song->getMpIds());
                $arrResult = '';
                foreach ($arrmpIdsOld as $key => $value) {
                    if ($value != $idMpSong) {
                        $arrResult[] = $value;
                    }
                }
                return '"' . implode(',', $arrResult) . '"';
            }
        }
        return 'none';
    }

    //tuanbm
    public static function createTokenCsrf() {
        //    echo $_SERVER['SERVER_NAME']; die();
        sfContext::getInstance()->getResponse()->setCookie('sToken', uniqid(), NULL, "/");
    }

    //tuanbm
    //ham su dung de generate ra the Embed Flash player(Su dung cho backend)
    public static function generateEmbedJwplayer($url, $width = "300", $height = "30") {
        return '<embed id="player" height="' . $height . '" width="' . $width . '"
           flashvars="file=' . $url . '&controlbar=top" wmode="transparent" allowfullscreen="true"
           allowscriptaccess="always" bgcolor="undefined"
           src="/js/player.swf" name="player" type="application/x-shockwave-flash">';
    }

    public static function generateEmbedJwplayerArticle($sPlayerId, $url, $width = "300", $height = "30") {
        //tam thoi ko dung code nay, ly do code nay ko chay duoc tren IE8
        return "<input class='jwplayerArticle' type='hidden' value='" . $url . "," . $width . "," . $height . "," . $sPlayerId . "' />
     <div id='" . $sPlayerId . "' name='' ></div>";
    }

    public static function generateEmbedJwplayerFrontend($url, $width = "300", $height = "30") {
        //tam thoi ko dung code nay, ly do code nay ko chay duoc tren IE8, su dung: generateEmbedJwplayerArticle
        return '<object id="songPlayer" height="' . $height . '" width="' . $width . '" type="application/x-shockwave-flash" data="/js/player5.swf" style="visibility: visible;">'
                . '<param name="allowscriptaccess" value="always">'
                . '<param name="allowfullscreen" value="true">'
                . '<param name="seamlesstabbing" value="true">'
                . '<param name="wmode" value="opaque">'
                . '<param name="flashvars" value="id=songPlayer&name=songPlayer&file=' . $url . '&repeat=none&skin=/js/songPlayer.zip">'
                . '</object>';
    }

    //tuanbm ham xu ly convert cac ky tu dac biet @#|
    public static function replaceSpecialCharacterForXml($value) {
        //chu y: bat buoc phai tuan theo thu tu convert #, @ |
        //    $value = str_replace("#","&#35;",$value);
        //    $value = str_replace("@","&#64;",$value);
        //    $value = str_replace("|","&#124;",$value);
        //Su dung Ma Mo Rong ASCII de thay the ® ¶ ©
        return $value;
    }

    //tuanbm format date
    //format: 'd/m/Y' default
    //format: 'd/m/Y hh:mm'
    public static function formatDateTime($datetime, $pattern = "d/m/Y") {
        return date($pattern, strtotime($datetime));
    }

    public static function formatTimeAndDate($datetime, $pattern = "d/m/Y") {
        $format = new DateTime($datetime);
        return $format->format($pattern);
    }

    public static function generateLinkIms($ims_id, $ims_name) {
        if ($ims_id > 0) {
            return "http://sangtao.imuzik.com.vn/ringtone/nhac-chuong-" . removeSignClass::removeSign($ims_name) . '/' . $ims_id;
        }
        return "";
    }

    //huynq28 format number
    //format: 'x.yyy.zzz' default
    public static function formatNumber($number, $delimiter = ".") {
        return $number ? number_format($number, 0, $delimiter, $delimiter) : $number;
    }

    public static function generateString($length = 8) {

        $string = "";
        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

        $maxlength = strlen($possible);

        if ($length > $maxlength) {
            $length = $maxlength;
        }
        // set up a counter for how many characters are in
        $i = 0;
        // add random characters until $length is reached
        while ($i < $length) {
            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, $maxlength - 1), 1);
            // have we already used this character?
            if (!strstr($string, $char)) {
                // no, so it's OK to add it onto the end of whatever we've already got...
                $string .= $char;

                $i++;
            }
        }
        return $string;
    }

    /**
     * Lay ra danh sach id tu lucene theo dieu kien, co tra lai phan trang
     * @author HoangL
     * @param $preQuery
     * @param $typeIndex
     * @param $pageNo
     * @param $pageSize
     * @return array|null
     */
    public static function searchLucence($preQuery, $typeIndex, $pageNo, $pageSize) {
        $search_config = new vtYamlConfig('search_config');
        $searchCnf = $search_config->get('field_search');
        $query = $preQuery;
        if ($pageNo < 1)
            $pageNo = 1;
        $offset = $pageSize * ($pageNo - 1);
        $index = null;
        $newQuery = new Zend_Search_Lucene_Search_Query_MultiTerm();
        switch ($typeIndex) {
            case LucenceType::SongName:
                $index = VtSongTable::getLuceneIndex();
                $sQuery = explode(' ', $query);
                foreach ($sQuery as $q) {
                    $newQuery->addTerm(new Zend_Search_Lucene_Index_Term($q, $searchCnf['song_field']), null);
                }
                break;
            case LucenceType::VideoName:
                $index = VtVideoTable::getLuceneIndex();
                $sQuery = explode(' ', $query);
                foreach ($sQuery as $q) {
                    $newQuery->addTerm(new Zend_Search_Lucene_Index_Term($q, $searchCnf['video_field']), null);
                }
                break;
            case LucenceType::AlbumName:
                $index = VtAlbumTable::getLuceneIndex();
                $sQuery = explode(' ', $query);
                foreach ($sQuery as $q) {
                    $newQuery->addTerm(new Zend_Search_Lucene_Index_Term($q, $searchCnf['album_field']), null);
                }
                break;
            case LucenceType::SingerName:
                $index = VtSingerTable::getLuceneIndex();
                $sQuery = explode(' ', $query);
                foreach ($sQuery as $q) {
                    $newQuery->addTerm(new Zend_Search_Lucene_Index_Term($q, $searchCnf['singer_field']), null);
                }
                break;
        }

        $hits = $index->find($newQuery);
        $total = count($hits);
        $strIds = '';
        $arrIds = array();
        if ($hits) {
            if ($total < $pageSize) {
                $offset = 0;
            } else if ($offset == $total) {
                $offset = $total - $pageSize;
            } elseif ($offset > $total)
                $offset = $total - ($total % $pageSize);
            if ($hits) {
                $end = $offset + $pageSize;
                if ($end > $total)
                    $end = $total;
                for ($i = $offset; $i < $end; $i++) {
                    if ($i == $offset) {
                        $strIds = $hits[$i]->pk;
                    } else {
                        $strIds .= ',' . $hits[$i]->pk;
                    }
                    array_push($arrIds, $hits[$i]->pk);
                }
                return array(
                    "strIds" => $strIds,
                    "arrIds" => $arrIds,
                    "hits" => $hits,
                    "total" => $total
                );
            }
        }
        return null;
    }

    /**
     * Lay ra danh sach cac thong tin luu trong lucene theo dieu kien tim kiem
     * @author HoangL
     * @param $preQuery
     * @param $typeIndex
     * @param $info
     * @param $pageNo
     * @param $pageSize
     * @return array|null
     */
    public static function searchLucenceWithInfo($preQuery, $typeIndex, $info, $pageNo, $pageSize) {
        //   	$query = removeOnlySignClass::removeSign($preQuery);
        //   	$query = preg_replace("/[^a-zA-Z0-9\s]/", "", $query);
        $search_config = new vtYamlConfig('search_config');
        $searchCnf = $search_config->get('field_search');
        $query = $preQuery;
        if ($pageNo < 1)
            $pageNo = 1;
        $offset = $pageSize * ($pageNo - 1);
        $index = null;
        $newQuery = new Zend_Search_Lucene_Search_Query_MultiTerm();
        switch ($typeIndex) {
            case LucenceType::SongName:
                $index = VtSongTable::getLuceneIndex();
                $sQuery = explode(' ', $query);
                foreach ($sQuery as $q) {
                    $newQuery->addTerm(new Zend_Search_Lucene_Index_Term($q, $searchCnf['song_field']), null);
                }
                break;
            case LucenceType::VideoName:
                $index = VtVideoTable::getLuceneIndex();
                $sQuery = explode(' ', $query);
                foreach ($sQuery as $q) {
                    $newQuery->addTerm(new Zend_Search_Lucene_Index_Term($q, $searchCnf['video_field']), null);
                }
                break;
            case LucenceType::AlbumName:
                $index = VtAlbumTable::getLuceneIndex();
                $sQuery = explode(' ', $query);
                foreach ($sQuery as $q) {
                    $newQuery->addTerm(new Zend_Search_Lucene_Index_Term($q, $searchCnf['album_field']), null);
                }
                break;
            case LucenceType::SingerName:
                $index = VtSingerTable::getLuceneIndex();
                $sQuery = explode(' ', $query);
                foreach ($sQuery as $q) {
                    $newQuery->addTerm(new Zend_Search_Lucene_Index_Term($q, $searchCnf['singer_field']), null);
                }
                break;
        }

        $hits = $index->find($newQuery);
        $total = count($hits);
        $strIds = '';
        $arrIds = array();
        $res = array();
        if ($hits) {
            if ($total < $pageSize) {
                $offset = 0;
            } else if ($offset == $total) {
                $offset = $total - $pageSize;
            } elseif ($offset > $total)
                $offset = $total - ($total % $pageSize);
            $end = $offset + $pageSize;
            if ($end > $total)
                $end = $total;
            for ($i = $offset; $i < $end; $i++) {
                if ($i == $offset) {
                    $strIds = $hits[$i]->pk;
                } else {
                    $strIds .= ',' . $hits[$i]->pk;
                }
                $item = array();
                foreach ($info as $subinfo) {
                    $item[$subinfo] = $hits[$i]->$subinfo;
                }
                $res[$hits[$i]->pk] = $item;
                array_push($arrIds, $hits[$i]->pk);
            }
            return array(
                "strIds" => $strIds,
                "arrIds" => $arrIds,
                "hits" => $hits,
                "total" => $total,
                "full_items" => $res
            );
        }
        return null;
    }

    /**
     * Lay link anh thumbnail<br />
     * Vi du su dung:<br />
     * <img src="<?php VtHelper::getThumbUrl('/medias/2011/06/15/abc.jpg', 90, 60); ?>" />
     * @param string $source /medias/2011/06/15/abc.jpg (nam trong thu muc web!)
     * @param int    $width
     * @param int    $height
     * @return string /medias/2011/06/15/thumbs/abc_90_60.jpg
     */
    public static function getThumbUrl($source, $width = null, $height = null, $type = '') {
        $isSite4G = sfConfig::get('app_is_site4g_media_file', false);
        if ($isSite4G) {
            return sfConfig::get('app_media_file_domain', 'https://shop.viettel.vn') . $source;
        }

        $defaultImage = sfConfig::get('app_url_media_default_image');
//        $source = self::getUrlImagePath($type, $source);
        if ($width == null && $height == null)
            return (file_exists(sfConfig::get('sf_web_dir') . $source)) ? $source : $defaultImage;
        if (empty($source)) {
            $source = $defaultImage;
        }

        $mediasDir = sfConfig::get('sf_web_dir');

        $fullPath = $mediasDir . $source;
        $pos = strrpos($source, '/');
        if ($pos !== false) {
            $filename = substr($source, $pos + 1);
            $dir = '/cache' . '/' . substr($source, 1, $pos);
        } else {
            return $defaultImage;
        }

        $pos = strrpos($filename, '.');
        if ($pos !== false) {
            $basename = substr($filename, 0, $pos);
            $extension = substr($filename, $pos + 1);
        } else {
            return $defaultImage;
        }

        if ($width == null) {
            $thumbName = $basename . '_auto_' . $height . '.' . $extension;
        } else if ($height == null) {
            $thumbName = $basename . '_' . $width . '_auto.' . $extension;
        } else {
            $thumbName = $basename . '_' . $width . '_' . $height . '.' . $extension;
        }

        $fullThumbPath = $mediasDir . $dir . $thumbName;

        # Neu thumbnail da ton tai roi thi khong can generate
        if (file_exists($fullThumbPath)) {
            return $dir . $thumbName;
        }

        # Neu thumbnail chua ton tai thi su dung plugin de tao ra
        $scale = ($width != null && $height != null) ? false : true;
        $thumbnail = new sfThumbnail($width, $height, $scale, true, 100);
        if (!is_file($fullPath)) {
            return $defaultImage;
        }
        $thumbnail->loadFile($fullPath);

        if (!is_dir($mediasDir . $dir))
            mkdir($mediasDir . $dir, 0777, true);
        $thumbnail->save($fullThumbPath);
        return (file_exists(sfConfig::get('sf_web_dir') . $dir . $thumbName)) ? $dir . $thumbName : $defaultImage;
    }

    /**
     * Ham tra ve duong dan theo ngay thang nam hoac theo duong dan truyen vao
     * @author NamDT5
     * @created on 29/09/2012
     * @param string $path
     */
    public static function generatePath($path = '', $byDate = true) {
        if ($byDate) {
            if ($path)
                $folder = $path . '/' . date('Y') . '/' . date('m') . '/' . date('d') . "/";
            else
                $folder = date('Y') . '/' . date('m') . '/' . date('d') . "/";
        } else {
            $folder = $path . '/';
        }
        $fullDir = sfConfig::get('sf_web_dir') . $folder;
        if (!is_dir($fullDir)) {
            @mkdir($fullDir, 0777, true);
        }
        return $folder;
    }

    public static function formatDurationTime($duration, $delimiter = ':') {
        $seconds = $duration % 60;
        $minutes = floor($duration / 60);
        $hours = floor($duration / 3600);
        $seconds = str_pad($seconds, 2, "0", STR_PAD_LEFT);
        $minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT) . $delimiter;
        if ($hours > 0) {
            $hours = str_pad($hours, 2, "0", STR_PAD_LEFT) . $delimiter;
        } else {
            $hours = '';
        }
        return "$hours$minutes$seconds";
    }

    /**
     * replace apostrophe
     * @param type $inputString
     * @return string
     */
    public static function replaceApostrophe($inputString) {
        if (!$inputString)
            return "";
        return str_replace("'", "\'", vtSecurity::decodeInput($inputString));
    }

    /**
     * Replace het cac ky tu dac biet
     * @param $str
     * @return mixed
     */
    public static function replaceSpecialChar($str) {
        $specialChar = array(
            unichr(160), //'\xA0',     // space
            # '\x60',     //
            # '\xB4',     //
            unichr(8216), // '\x2018',   // left single quotation mark
            unichr(8217), // '\x2019',   // right single quotation mark
            unichr(8220), // '\x201C',   // left double quotation mark
            unichr(8221), // '\x201D'    // right double quotation mark
            unichr(130), // baseline single quote
            unichr(145), // left single quote
            unichr(146), // right single quote)
            unichr(147), // right single quote)
            unichr(148), // right single quote)
        );
        $specialCharReplace = array(
            ' ', // space
            # '\x60',     //
            # '\xB4',     //
            "'", // left single quotation mark
            "'", // right single quotation mark
            '"', // left double quotation mark
            '"', // right double quotation mark
            ',', // baseline single quote
            "'", // 145
            "'", // 146
            '"', // 147
            '"', // 148
        );
        return str_replace($specialChar, $specialCharReplace, $str);
    }

    /**
     * author: thongnq1
     * han thuc hien kiem tra xem so thue bao co phai cua viettel khong
     */
    public static function checkViettelPhoneNumber($phoneNumber) {
        if (preg_match(VtHelper::VT_MSISDN_PATTERN, $phoneNumber)) {
            return true;
        }
        return false;
    }

    public static function datapost($URLServer, $postdata) {
        $agent = "Mozilla/5.0";
        $cURL_Session = curl_init();
        curl_setopt($cURL_Session, CURLOPT_URL, $URLServer);
        curl_setopt($cURL_Session, CURLOPT_USERAGENT, $agent);
        curl_setopt($cURL_Session, CURLOPT_POST, 1);
        curl_setopt($cURL_Session, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($cURL_Session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cURL_Session, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($cURL_Session);
        return $result;
    }

    //loilv4
    public static function getUrlBannerPath($objectStr, $fileName) {
        if (strlen($fileName) == 0) {
            return "";
        } else {
            return sfConfig::get('app_url_media_images') . "/" . $objectStr . "/" . $fileName;
        }
    }

    /**
     * @author loilv4
     * Ham loai bo tat ca cac the html mac dinh loai bo array('script', 'iframe', 'noscript') va loai bo ca the 'p'
     */
    public static function strip_html_tags_and_decode_p($str) {
        //Ham nay chi duoc dung doi voi get du lieu hien thi sf_escaping_strategy = 1 va sf_escaping_method=ESC_SPECIALCHARS
        //tuyet doi ko dung de remove truoc khi save du lieu
        //do symfony tu dong encode HTML nen phai decode truoc khi remove Script
        $str = htmlspecialchars_decode($str); //co the dung ham $object->getSomething(ESC_RAW);//htmlspecialchars_decode($str, ENT_QUOTES);
        $str = VtHelper::strip_html_tags($str, array('script', 'iframe', 'noscript', 'embed', 'p'));
        return str_replace('<embed ', '', $str);
    }

    /*
     * duynt10
     * remove all tag <p> , <li>,....
     * use for song lyric
     */

    public static function strip_html_tags_and_decode_songlyric($str) {
        //Ham nay chi duoc dung doi voi get du lieu hien thi sf_escaping_strategy = 1 va sf_escaping_method=ESC_SPECIALCHARS
        //tuyet doi ko dung de remove truoc khi save du lieu
        //do symfony tu dong encode HTML nen phai decode truoc khi remove Script
        $str = htmlspecialchars_decode($str); //co the dung ham $object->getSomething(ESC_RAW);//htmlspecialchars_decode($str, ENT_QUOTES);
        $str = VtHelper::strip_html_tags($str, array('script', 'iframe', 'noscript', 'embed', 'p', 'li', 'ul', "br/", "hr/", "!--...--", '!doctype', 'a', 'abbr', 'address', 'area', 'article', 'aside', 'audio', 'b', 'base', 'bb', 'bdo', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'cite', 'code', 'col', 'colgroup', 'command', 'datagrid', 'datalist', 'dd', 'del', 'details', 'dfn', 'div', 'dl', 'dt', 'em', 'embed', 'eventsource', 'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                    'head', 'header', 'hgroup', 'hr', 'html', 'i', 'iframe', 'img', 'input', 'ins', 'kbd', 'keygen', 'label', 'legend', 'li', 'link', 'mark', 'map', 'menu', 'meta', 'meter', 'nav', 'noscript', 'object', 'ol', 'optgroup', 'option', 'output', 'p', 'param', 'pre', 'progress', 'q', 'ruby', 'rp', 'rt', 'samp', 'script', 'section', 'select', 'small', 'source', 'span', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'time',
                    'title', 'tr', 'ul', 'var', 'video', 'wbr'));
        return str_replace('<embed ', '', $str);
    }

    /**
     * Check datetime
     * @author HoangL
     * @param $dateTime
     * @return bool
     */
    public static function checkDateTime($dateTime) {
        if (preg_match("/^(\d{2})-(\d{2})-(\d{4}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches)) {
            if (count($matches) >= 6 && checkdate($matches[2], $matches[1], $matches[3])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Kiem tra moi gia tri mang $childArray co thuoc mang $parentArray ko?
     * @param $childArray
     * @param $parentArray
     * @return bool
     */
    public static function checkChildArray($childArray, $parentArray) {
        foreach ($childArray as $child) {
            if (!in_array($child, $parentArray)) {
                return false;
            }
        }
        return true;
    }

    public static function startsWith($haystack, $needle) {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    /**
     * @author ngoctv
     * Ham kiem tra mot ky tu hay mot chuoi co trong phan duoi cua chuoi khac hay khong, neu co thi tra ve true nguoc lai la false
     * @param type $haystack
     * @param type $needle
     * @return boolean
     */
    public static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    public static function preLikeQuery($s, $e) {
        return str_replace(array($e, '_', '%'), array($e . $e, $e . '_', $e . '%'), $s);
    }

    public static function translateQuery($str, $trim = true) {
        if ($str == null || $str == '')
            return $str;
        $str = $trim ? trim($str) : $str;
        $str = addcslashes($str, "\\%_");
        return $str;
    }

    public static function genPassword($length=6)
    {
        # first character is capitalize
        $pass =  chr(mt_rand(65,90));    // A-Z

        # rest are either 0-9 or a-z
        for($k=0; $k < $length - 1; $k++)
        {
            $probab = mt_rand(1,10);

            if($probab <= 8)   // a-z probability is 80%
                $pass .= chr(mt_rand(97,122));
            else            // 0-9 probability is 20%
                $pass .= chr(mt_rand(48, 57));
        }
        return $pass;
    }

    public static function generateCode($length = 8){
        return substr(md5(uniqid(mt_rand(), true)) , 0, $length);
    }

    public static function writeLogValue($content, $fileName = 'default.log') {
        $rotateFile = $fileName . '.' . date('Y-m-d');
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/' . $rotateFile));
        $logger->log($content, sfFileLogger::INFO);
    }

    /**
     * Ham kiem tra 2 khoang thoi gian co bi trung nhau khong
     * @author anhbhv
     * @created on 16/10/2014
     * @param $start_one
     * @param $end_one
     * @param $start_two
     * @param $end_two
     * @return int
     */
    public static function datesOverlap($start_one, $end_one, $start_two, $end_two)
    {
        $start_one = new DateTime($start_one);
        $end_one = new DateTime($end_one);
        $start_two = new DateTime($start_two);
        $end_two = new DateTime($end_two);
        if ($start_one <= $end_two && $end_one >= $start_two) { //If the dates overlap
            return min($end_one, $end_two)->diff(max($start_two, $start_one))->days + 1; //return how many days overlap
        }

        return 0; //Return 0 if there is no overlap
    }

    /**
     * Ham kiem tra bat dau cua mot chuoi co ton tai trong mang truyen vao ko
     * VD: $sourceString = 01654926551 , $sourceArray = array('097', '098', '0165') --> result = true
     * @author anhbhv
     * @param $sourceArray
     * @param $sourceString
     * @return bool
     */
    public static function checkBeginOfStringInArray($sourceArray, $sourceString){
        foreach ($sourceArray as $key => $search) {
            if (strpos($sourceString, $search) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Ham tra ve cac thiet lap cua he thong
     * @author NamDT5
     * @created on Apr 21, 2011
     * @param $key - Key hoac 1 mang cac key can lay. Neu = null -> lay tat ca thiet lap he thong
     * @return array hoac string
     */
    public static function getSystemSetting($key = null, $useCache = true, $default = null)
    {
        if ($useCache) {
            $cache = new sfFileCache(array('cache_dir' => sfConfig::get('sf_cache_dir') . '/function'));
            $cache->setOption('lifetime', 86400);
            $fc = new sfFunctionCache($cache);
            $result = $fc->call('VtHelper::getSystemSetting', array('key' => $key, 'useCache' => false));
            return $result != null ? $result : $default;
        }

        $result = array();
        $query = VtSettingTable::getInstance()->createQuery()->select('name, value');
        $fetchOne = false;

        if (!empty($key)) {
            if (is_array($key)) {
                $query = $query->andWhereIn('name', $key);
            } else if (is_string($key)) {
                $query = $query->andWhere('name = ?', $key);
                $fetchOne = true;
            }
        }

        $pixConfig = $query->fetchArray();

        if (count($pixConfig)) {
            // Tra ve gia tri cua 1 config
            if ($fetchOne)
                return $pixConfig[0]['value'];
            // Tra ve 1 mang cac config
            foreach ($pixConfig as $config) {
                $result[$config['name']] = $config['name'];
            }
        } else
            return $default;
        return $result;
    }
    
     /**
     * Ham ghi du lieu ra file text
     * @author tuba
     * @created on 28/12/2015
     * @param $string
     * @return $string
     */
    public static function exportToFileTxt($string, $fullFile, $filepath)
    {

        try {
            $strlength = strlen($string);
            $create = fopen($fullFile, "w"); //uses fopen to create our file.
            $write = fwrite($create, $string, $strlength); //writes our string to our file.
            $close = fclose($create); //closes our file

            return $filepath;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

    }

    /**
     * Ham doc du lieu tu file text
     * @author tuba
     * @created on 30/12/2015
     * @param $string
     * @return $string
     */
    public static function readFileTxt($filepath)
    {

        try {
            if(!is_null($filepath) && is_file(sfConfig::get('sf_web_dir').$filepath)){
                $exsention = substr($filepath, strrpos($filepath, '.') + 1);
                if($exsention == 'txt'){
                    return file_get_contents(sfConfig::get('sf_web_dir').$filepath);
                } else {
                    return $filepath;
                }

            } else {
                return '';
            }

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

    }


    /**
     * Ham doc du lieu tu file text
     * @author tuba
     * @created on 30/12/2015
     * @param $string
     * @return $string
     */
    public static function genFileToContent($programId)
    {
        $strContent = VtMmsTable::getListContentMms($programId);
        $str = null;
        if($strContent) {
            //chuyen json string sang array
            $arrContent = json_decode($strContent, true);
            foreach ($arrContent as $k => $v) {

                $rs = $v['content'] ? VtHelper::encodeOutput(VtHelper::readFileTxt($v['content']), true) . '<br/>' : '';
                $rs .= $v['image_path'] ? '<img src = "' . $v['image_path'] . '" width="100" height="100" ><br/>' : '';
                $str .= $rs;
            }
        }
        return $str;

    }

    /**
     * Ham doc du lieu tu file text
     * @author tuba
     * @created on 30/12/2015
     * @param $string
     * @return $string
     */
    public static function getMimeTypeFile($file)
    {


        if (!$finfo = new finfo(FILEINFO_MIME))
        {
            return null;
        }

        $type = $finfo->file($file);

        // remove charset (added as of PHP 5.3)
        if (false !== $pos = strpos($type, ';'))
        {
            $type = substr($type, 0, $pos);
        }

        return $type;
    }

    /**
     * @author anhbhv
     * @description Ham dem so luong mt theo noi dung truyen vao
     * @param $string
     * @param $isUnicode
     * @return float|int
     */
    public static function getNumMtOfString($string, $isUnicode){
        $length = function_exists('mb_strlen') ? mb_strlen($string, 'UTF-8') : strlen($string);
        if($isUnicode)
          $numMt = $length > 70 ? ($length - 1)/67 + 1 : 1;
        else
          $numMt = $length > 160 ? ($length - 1)/153 + 1 : 1;

        return floor($numMt);
    }

    public static function number_format_clean($number,$precision=0,$dec_point='.',$thousands_sep=',')
    {
        RETURN trim(number_format($number,$precision,$dec_point,$thousands_sep),'0'.$dec_point);
    }

    public static function remove_utf8_bom($text)
    {
        return remove_utf8_bom($text);
    }

    /**
     * @author tuba
     * @description Ham check unicode
     * @param $string
     * @return boolean
     */
    public static function checkIsUnicode($string){
        if(mb_strlen($string, 'UTF-8') != strlen($string))
            return true;
        return false;

    }

    public static function logActions($objId, $actionContent, $fileName){
        $user = sfContext::getInstance()->getUser();
        $app = sfContext::getInstance()->getConfiguration()->getApplication();
        $userId = ($app == 'backend') ? $user->getGuardUser()->getId() : (($app == 'frontend') ? $user->getCpId() : 0);

        $logContent = sprintf('%s | user_id: %s | object_id: %d | %s',$app,$userId,$objId,$actionContent);
        VtHelper::writeLogValue($logContent, $fileName);
    }

    //Doi so dien thoai sang 09xxx
    public static function changeMobileNumber($phone)
    {
        $phone = (string)$phone;
        // Bo het cac ky tu khong phai so
//        $phone = preg_replace('@[^0-9]@', '', $phone);
        if (substr($phone, 0, 1) == '0') { #0975292582
            $phone = substr($phone, 1);
        } elseif (substr($phone, 0, 2) == '84') { #+84975292582
            $phone = substr($phone, 2);
        }

        return $phone;
    }

    /**
     * Ham tra ve so ngay giua 2 ngay truyen vao
     * @author anhbhv
     * @param $date1
     * @param $date2
     * @return mixed
     */
    public static function getNumOfDayBetweenTwoDate($date1, $date2){
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);
        return $date2->diff($date1)->days + 1;
    }

    /**
     * @author: tuanbm2
     * @Description: Ham xu ly hide xxx o duoi SDT
     * @param $isdn
     * @param mixed $numHide
     * @return string
     */
    public static function getIsdnXXX($isdn, $numHide = 3){
        $length = strlen($isdn);
        if($length>$numHide){
            $isdnXXX=substr($isdn,0,$length-$numHide).str_repeat("x",$numHide);
            return $isdnXXX;
        }
        return $isdn;
    }

    public static function getIsdnMidleXXX($isdn) {
        $number = self::getMobileNumber($isdn, self::MOBILE_SIMPLE);
        $length = strlen($number);
        if ($length > 5) {
            $numberMidleXXX = substr($number, 0, strlen($number) - 5) . 'xxx' . substr($number, -2);
            return $numberMidleXXX;
        }
        return $number;
    }

    private static $configurator4php=null;
    private static $config4php=null;

    /**
     * @author: tuanbm2
     * @description: su dung khoi tao Log4PHP 1 lan cho request
     * @param $content
     * @param string $loggerName
     * @param string $type
     */
    public static function writeLog4Php($content,$loggerName = 'all',$type="info") {
        if(self::$config4php==null){
            self::$configurator4php = new LoggerConfiguratorDefault();
            self::$config4php = self::$configurator4php->parse(sfConfig::get('sf_config_dir').'/log4php.xml');
            Logger::configure(self::$config4php);
        }
        $logger = Logger::getLogger($loggerName);
        $logger->$type($content);
    }
    public static function getLogger4Php($loggerName) {
        if(self::$config4php==null){
            self::$configurator4php = new LoggerConfiguratorDefault();
            self::$config4php = self::$configurator4php->parse(sfConfig::get('sf_config_dir').'/log4php.xml');
            Logger::configure(self::$config4php);
        }
        $logger = Logger::getLogger($loggerName);
        return $logger;
    }

    //ham render image theo mimetype
    public static function renderImgTag($filePath, $params){
        if(is_file($filePath)){
            $mimeType = mime_content_type($filePath);
            if($mimeType != 'application/x-shockwave-flash'){
                $attrStr = '';
                foreach($params as $key => $param){
                    $attrStr .= sprintf(' %s="%s"', $key, $param);
                }
                return sprintf('<img %s>', $attrStr);
            }else{
                return '
                    <object height="150"
                          classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
                          codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0">
                            <param value="link='.$params['src'].'" name="flashvars">
                            <param name="movie" value="'. $params['src'].'">
                            <param name="wmode" value="opaque">

                            <embed height="150" type="application/x-shockwave-flash"
                              pluginspage="http://www.macromedia.com/go/getflashplayer"
                              src="'. $params['src'].'" wmode="opaque" />

                    </object>
                  ';
            }
        }
    }

    public static function decrypt($input, $securekey)
    {
        //$securekey = $securekey.'qwerty';
        $securekey = md5($securekey);
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        //$iv = mcrypt_create_iv(32);
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $securekey, base64_decode(rawurldecode($input)), MCRYPT_MODE_ECB, $iv));
    }

    /**
     * Ham tao tai khoan random
     *
     * @author Tiennx6
     * @param $number
     * @param $randomPass
     * @param $isTemp
     * @since 05/09/2014
     * @return mixed
     */
    public static function createRandomAccount($number, $randomPass, $isTemp = false)
    {
        $number = self::getMobileNumber($number, self::MOBILE_GLOBAL);
        // Dang ky tai khoan
        $salt = md5(self::randomAlphanumeric(6) . $number);
        $password = sha1($salt . $randomPass);

        try {
            $vtUser = new VtUser();
            $vtUser->setPhone($number);
            $vtUser->setStatus(1);
            $vtUser->setSalt($salt);
            $vtUser->setPassword($password);
            $vtUser->setLastLogin(date('Y-m-d H:i:s'));
            $vtUser->setIsTemp($isTemp);
            $vtUser->save();
            return $vtUser;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function randomAlphanumeric($length = 8)
    {
//        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $alphabet = "abcdefghijklmnopqrstuwxyz0123456789";
        $pass = array(); //remember to declare $pass as an array
        for ($i = 0; $i < $length; $i++) {
            $n = mt_rand(0, strlen($alphabet) - 1); //use strlen instead of count
            $pass[$i] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public static function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
          'y' => 'năm',
          'm' => 'tháng',
          'w' => 'tuần',
          'd' => 'ngày',
          'h' => 'giờ',
          'i' => 'phút',
          's' => 'giây',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' trước' : 'vừa xong';
    }

    public static function getStringCharacters($string) {
        preg_match('/(\s+.{1}).*(\s+.{1})/', $string, $matches);
        $count = count($matches);
        if ($count == 1) {
            return trim($matches[0]);
        } else if ($count >= 2) {
            return trim($matches[$count - 2]) . trim($matches[$count - 1]);
        }
        return $string[0];
    }

    public static function getDayByDate($date, $format = 'd/m/Y H:i:s')
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $weekday = date("l", strtotime($date));
        $weekday = strtolower($weekday);
        switch ($weekday) {
            case 'monday':
                $weekday = 'Thứ hai';
                break;
            case 'tuesday':
                $weekday = 'Thứ ba';
                break;
            case 'wednesday':
                $weekday = 'Thứ tư';
                break;
            case 'thursday':
                $weekday = 'Thứ năm';
                break;
            case 'friday':
                $weekday = 'Thứ sáu';
                break;
            case 'saturday':
                $weekday = 'Thứ bảy';
                break;
            default:
                $weekday = 'Chủ nhật';
                break;
        }
        return sprintf('%s, %s', $weekday,date($format, strtotime($date)));
    }

    public static function getDiscountPercent($price, $oldPrice) {
        return sprintf('%.1f', (($oldPrice - $price) / $oldPrice) * 100);
    }

    /**
     * Ham tra ve number dang ngan gon
     * @author anhbhv
     * @created on 10/06/2014
     * @param $number
     * @return bool|string
     */
    public static function getShortNumber($number){
        $number = (0+str_replace(",","",$number));

        // is this a number?
        if(!is_numeric($number)) return false;

        // now filter it;
        if($number>1000000000000) return round(($number/1000000000000),1).' Tri';
        else if($number>1000000000) return round(($number/1000000000),1).' Bil';
        else if($number>1000000) return round(($number/1000000),1).' Mil';
        else if($number>1000) return round(($number/1000),1).' K';

        return number_format($number);
    }

    public static function getHourBetweenDates($date1,$date2){
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);
        $diff = $date2->diff($date1);
        $hours = $diff->h;
        return $hours + ($diff->days*24);
    }


    /**
     * @author: tuanbm2
     * @description: Add more Zero before a string
     * @param $objectId
     */
    public static function addZeroLeft($objectId,$isSim){
      if($isSim){
        $prefix = "S";
      }else {
        $prefix = "D";
      }
      $length = strlen($objectId);
      $maxLength = sfConfig::get("app_maxlength_zero",8);
      if($length>$maxLength){
        $maxLength= $maxLength + 2;
      }
      $orderCode = str_pad($objectId,$maxLength,"0", STR_PAD_LEFT);;
      return $prefix.$orderCode;
    }
    public static function removeZeroLeft($orderCodeZero,$isSim){
      if($isSim){
        $prefix = "S";
      }else {
        $prefix = "D";
      }
      $orderCodeZero = ltrim($orderCodeZero,$prefix);
      $orderCodeZero = ltrim($orderCodeZero,"0");
      return $orderCodeZero;
    }

    public static function setKeyCache($key,$value,$timeout=600){
      try{
        $redis = sfRedis::getClient("cache_sim");
        $redis->setex($key,$timeout,serialize($value));
        $redis->disconnect();
      }catch (Exception $ex){
        VtHelper::writeLog4Php('ERROR:setKeyCache, Key='. $key ."&value=".serialize($value).": ".$ex->getMessage(),"all");
      }
    }

    public static function getKeyCache($key){
      try{
        $redis = sfRedis::getClient("cache_sim");
        $value = $redis->get($key);
        $redis->disconnect();
        return unserialize($value);
      }catch (Exception $ex){
        VtHelper::writeLog4Php('ERROR:getKeyCache: '. $key.": ".$ex->getMessage(),"all");
      }
      return false;
    }

    public static function getNumberTotalPoint($number) {
        if (is_numeric($number)) {
            $total = 0;
            for ($i = 0; $i < strlen($number); $i++) {
                $total += $number[$i];
            }
            return $total;
        }
        return false;
    }

    public static function inArray($element, $array) {
        foreach ($array as $member) {
            if ($element == $member) {
                return true;
            }
        }
        return false;
    }

    public static function writeLogKpi($moduleName, $fileName, $type, $status, $requestTime, $responseTime, $errorDetail) {
        $file = '../log/log_kpi/' . VtLogKpiType::getFileName($type);
        self::getRotateFile($file);
        $logger = new sfFileLogger(new sfEventDispatcher(), array(
          'file' => $file,
          'format' => '%message%%EOL%'
        ));
        $content = sprintf('%s|%s|%s|%s|%s|%s|%s|%s|%s',
          date('YmdHis'), $moduleName, $_SERVER['SERVER_ADDR'], $fileName, $type, $status, $requestTime, $responseTime, $errorDetail);
        $logger->info($content);
    }
    
    public static function getMilliTime(){
      $microtime = microtime();
      $comps = explode(' ', $microtime);

      // Note: Using a string here to prevent loss of precision
      // in case of "overflow" (PHP converts it to a double)
      return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }

    public static function getRotateFile($filename) {
        if (is_file($filename)) {
            $lastModify = filemtime($filename);
            $lastDateModify = date('Y-m-d', $lastModify);
            if ($lastDateModify != date('Y-m-d')) {
                rename($filename, $filename . '.' . $lastDateModify);
            }
        }
    }

    /**
     * Returns a routed URL based on the module/action passed as argument
     * and the routing configuration.
     *
     * <b>Examples:</b>
     * <code>
     *  echo url_for('my_module/my_action');
     *    => /path/to/my/action
     *  echo url_for('@my_rule');
     *    => /path/to/my/action
     *  echo url_for('@my_rule', true);
     *    => http://myapp.example.com/path/to/my/action
     * </code>
     *
     * @param  string $internal_uri  'module/action' or '@rule' of the action
     * @param  bool   $absolute      return absolute path?
     * @return string routed URL
     */
    public static function urlFor()
    {
        // for BC with 1.1
        $arguments = func_get_args();
        if (is_array($arguments[0]) || '@' == substr($arguments[0], 0, 1) || false !== strpos($arguments[0], '/'))
        {
            return call_user_func_array('url_for1', $arguments);
        }
        else
        {
            return call_user_func_array('url_for2', $arguments);
        }
    }

    public static function getOriginalBccsGW($val, $tagOpenName, $tagCloseName)
    {
      $pos1 = strpos($val, $tagOpenName); //tag open
      $pos2 = strpos($val, $tagCloseName); //tag close
      $source = substr($val, $pos1, $pos2 - $pos1 + strlen($tagCloseName));
      return @json_decode(json_encode(simplexml_load_string($source)));
    }

    public static function getElementXml($xml, $startTag, $endTag) {
        $startPos = strpos($xml, $startTag);
        $endPos = strpos($xml, $endTag);
        if ($startPos && $endPos) {
            $startLen = strlen($startTag);
            $startPos = $startPos + $startLen;
            return substr($xml, $startPos, $endPos - $startPos);
        } else {
            return null;
        }
    }

    // xoa cache cho component
    // muon xoa het thi de $key la *
    public static function deleteCacheKeyOfComponent($module,$partial,$key, $apps = null)
    {
      $cacheKey = "@sf_cache_partial?module=".$module."&action=".$partial."&sf_cache_key=".$key;
      return self::deleteCacheKey($cacheKey, $apps);
    }

    // xoa cache cho page
    public static function deleteCacheKey($cacheKey,$apps = null)
    {
        $result = false;
        if ($apps) {
            $currentApps = sfContext::getInstance()->getConfiguration()->getApplication();
            sfContext::switchTo($apps);
            $viewManager = sfContext::getInstance()->getViewCacheManager();
            if ($viewManager) {
                $result = sfContext::getInstance()->getViewCacheManager()->remove($cacheKey);
                sfContext::switchTo($currentApps);
            }
        } else {
            $viewManager = sfContext::getInstance()->getViewCacheManager();
            if ($viewManager) {
                $result = sfContext::getInstance()->getViewCacheManager()->remove($cacheKey);
            }
        }
        return $result;
    }

    public static function removeCacheAfterLoginAndLogout(){
//      VtHelper::deleteCacheKeyOfComponent('vtData3g','_listPackage3g','*');
//      VtHelper::deleteCacheKeyOfComponent('vtData3g','_listPackageVas','*');
//      VtHelper::deleteCacheKeyOfComponent('vtData3g','_listPackageVtfree','*');
//      VtHelper::deleteCacheKeyOfComponent('common','_commentPackage','*');
//      VtHelper::deleteCacheKeyOfComponent('vtData3g','_detailPackage','*');
//      VtHelper::deleteCacheKeyOfComponent('vtVas','_detailPackage','*');
//      VtHelper::deleteCacheKeyOfComponent('vtFtth','_detailPackage','*');
//      VtHelper::deleteCacheKeyOfComponent('vtDevice','_deviceDetail','*');
    }

    public static function writeLogKpiVtNet($params = array()) {
        $file = '../log/log_kpi_vtnet/' . VtLogKpiVtNetType::getFileName();
        self::getRotateFile($file);
        $logger = new sfFileLogger(new sfEventDispatcher(), array(
          'file' => $file,
          'format' => '%message%%EOL%'
        ));
        $content = sprintf('%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s',
          $params['ApplicationCode'], $params['ServiceCode'], $params['SessionID'], $params['IP_Port_ParentNode'],
          $params['IP_Port_CurrentNode'], $params['RequestContent'], $params['ResponseContent'], $params['StartTime'],
          $params['EndTime'], $params['Duration'], $params['ErrorCode'], $params['ErrorDescription'],
          $params['TransactionStatus'], $params['ActionName'], $params['UserName'], $params['Account']);
        // Remove whitespace, new lines, etc...
        $content = trim(preg_replace('/\s+/', ' ', $content));
        $logger->info($content);
    }

    public static function getHostPortFromUrl($url) {
        // Tach lay host va port
        preg_match('/^http:\/\/(\d+|\:)\/\.+$/', $url, $matches);
        if (count($matches) >= 2) {
            return $matches[1];
        }
        return '';
    }

    public static function checkCaptchaEnable($username, $namespace) {
        $sfUser = sfContext::getInstance()->getUser();
        if ($sfUser->getAttribute('captchaEnable', false, $namespace)) {
            return true;
        }

        if ($sfUser->hasAttribute('otpFailTimes', $namespace)) {
            $sessionLoginFail = $sfUser->getAttribute('otpFailTimes', 1, $namespace) + 1;
            $sfUser->setAttribute('otpFailTimes', $sessionLoginFail, $namespace);
        } else {
            $sessionLoginFail = 1;
            //--Insert time login fail to session
            $sfUser->setAttribute('otpFailTimes', $sessionLoginFail, $namespace);
        }

        //-- Check max login fail session and login fail user DB
        if ($sessionLoginFail >= 2) {
            $sfUser->setAttribute('captchaEnable', true, $namespace);
            return true;
        }

        $seconds = strtotime('tomorrow') - strtotime('now');
        $key = 'num_input_otp:' . $namespace . ':' . $username;
        if (AntiSpam::getLimited($key, 1, $seconds)) {
            $sfUser->setAttribute('captchaEnable', true, $namespace);
            return true;
        }

        return false;
    }

    public static function maskPhone($mobile, $mask='***')
    {
        if(strlen($mobile) > 6){
            $mobile1 = substr($mobile, 0, strlen($mobile) - 6);
            $mobile2 = substr($mobile, strlen($mobile) - 3, strlen($mobile));
            return $mobile1.$mask.$mobile2;
        }else{
            return $mobile;
        }
    }

    public static function getShortNameIcon($name)
    {
        if(!$name) return '';
        $nameArr = explode(' ', $name);
        if(count($nameArr) == 1){
          return substr($nameArr[0],0,1);
        }else{
          $firstLetter = substr(array_pop($nameArr),0,1);
          $secondLetter = substr(array_pop($nameArr),0,1);
          return $secondLetter.$firstLetter;
        }
    }

    public static function getPager($model, $query, $page = 1, $max_per_page = 10)
    {
        $pager = new sfDoctrinePager($model, $max_per_page);
        $pager->setPage($page);
        $pager->setQuery($query);
        $pager->init();
        return $pager;
    }

    public static function getCheckOrderStatusName($row) {
        return VtOrderLog::getDisplayStatusForAllOrder($row);
    }

    public static function writeUserActivityLog($values) {
        $sfUser = sfContext::getInstance()->getUser();
        $lastStepSession = $sfUser->getAttribute('user_activity_log_last_step');
        $lastStep = $values['type'] . '.' . $values['step'];
        // Neu chi la refresh lai trang
        if ($lastStep == $lastStepSession) {
            return false;
        }

        $vtUser = $sfUser->getVtUser();
        $isLogin = $sfUser->getIsLogin();
        $msisdn = $isLogin ? $vtUser->getPhone() : (isset($values['msisdn']) ? $values['msisdn'] : null);
        $msisdn = VtHelper::getMobileNumber($msisdn, VtHelper::MOBILE_GLOBAL);
        $values = array(
          'user_id' => $isLogin ? $vtUser->getId() : null,
          'msisdn' => $msisdn,
          'order_sess_id' => self::generateActivityLogSessId($values['step']),
          'session_id' => session_id(),
          'client_ip' => VtRadius::getRealIpAddr(),
          'user_agent' => sfContext::getInstance()->getRequest()->getHttpHeader('User-Agent'),
          'customer_name' => $isLogin && $vtUser->getFullName() ? $vtUser->getFullName() : (isset($values['customer_name']) ? $values['customer_name'] : null),
          'step' => $values['step'],
          'type' => $values['type'],
          'result' => isset($values['result']) ? $values['result'] : null,
          'log_time' => time(),
          'created_at' => date('Y-m-d H:i:s')
        );

        // Reset last step
        $sfUser->setAttribute('user_activity_log_last_step', $lastStep);

        try {
            // Luu log
            $vtUserActivityLog = new VtUserActivityLog();
            $vtUserActivityLog->setUserId($values['user_id']);
            $vtUserActivityLog->setMsisdn($values['msisdn']);
            $vtUserActivityLog->setOrderSessId($values['order_sess_id']);
            $vtUserActivityLog->setSessionId($values['session_id']);
            $vtUserActivityLog->setClientIp($values['client_ip']);
            $vtUserActivityLog->setUserAgent($values['user_agent']);
            $vtUserActivityLog->setCustomerName($values['customer_name']);
            $vtUserActivityLog->setStep($values['step']);
            $vtUserActivityLog->setType($values['type']);
            $vtUserActivityLog->setResult($values['result']);
            $vtUserActivityLog->setLogTime($values['log_time']);
            $vtUserActivityLog->setCreatedAt($values['created_at']);
            $vtUserActivityLog->save();
            return true;
        } catch (Exception $e) {
            $logger = VtHelper::getLogger4Php("all");
            $logger->error("writeUserActivityLog|Exception when writing user activity log, error=" . $e->getMessage());
            return false;
        }
    }

    public static function generateActivityLogSessId($step) {
        $sfUser = sfContext::getInstance()->getUser();
        if ($step != 1 && $sfUser->hasAttribute('user_activity_log_order_sess_id')) {
            return $sfUser->getAttribute('user_activity_log_order_sess_id');
        }
        $newSessId = md5(session_id() . date("YmdHis") . uniqid());
        $sfUser->setAttribute('user_activity_log_order_sess_id', $newSessId);
        return $newSessId;
    }

    public static function checkWeakPass($password){
      $isWeakPass = false;
      $yamlFile = sfConfig::get('sf_config_dir') . '/weakPass.yml';
      $arrData = sfYaml::load($yamlFile);
      if(!empty($arrData['weak_pass'])){
        if(in_array($password, $arrData['weak_pass']))
          $isWeakPass = true;
      }
      return $isWeakPass;
    }

    public static function getPromotionFromLanding($landingData,$objectId,$type){
      $promotion = null;
      $landingId = null;
      if($landingData){
        $enCrypt = new vtEncryption();
        $landingInfo = json_decode($enCrypt->decode($landingData));
        if($landingInfo && $landingInfo->landing_item_id && $landingInfo->landing_id){
          $landingId = $landingInfo->landing_id;
          $landingItem = VtLandingItemTable::getActiveItemByIdAndType($landingInfo->landing_item_id, $type, $landingInfo->landing_id);
          if($landingItem && $landingItem->getProductId() == $objectId && $landingItem->getPromotion()){
            $promotionArr = json_decode($landingItem->getPromotion());
            $promotionContent = [];
            foreach ($promotionArr as $promotion)
              $promotionContent[] = $promotion->content;
            $promotion = implode('; ', $promotionContent);
          }
        }
      }
      return ['promotion' => $promotion, 'landing_id' => $landingId];
    }

    public static function writeFlashSaleInteractiveLog($values) {
        $sfUser = sfContext::getInstance()->getUser();
        // Neu la buoc dau tien ma chua co session khi click -> ket thuc
        $hasClickSess = $sfUser->hasAttribute('flashsale_interactive_log_click_sess_values', 'vtFlashSale');
        if ($values['event'] != VtFlashSaleInteractiveLogEventEnum::CLICK && !$hasClickSess) {
            return false;
        }

        $lastEventSession = $sfUser->getAttribute('flashsale_interactive_log_last_event', null, 'vtFlashSale');
        $lastEvent = sprintf('%s.%s', $values['object_type'], $values['event']);
        // Neu chi la refresh lai trang
        if ($values['event'] != VtFlashSaleInteractiveLogEventEnum::CLICK && $lastEvent === $lastEventSession) {
            return false;
        }

        $timeOut = time() + 3600;
        // Kiem tra theo IP
        $ip = VtRadius::getRealIpAddr();
        $uniqueKey = "VTSHOP_CLICK_SESS_" . $ip;
        $check = AntiSpam::getLimited($uniqueKey, 100, $timeOut);
        if ($check) {
            return false;
        }

        // Reset last event
        $sfUser->setAttribute('flashsale_interactive_log_last_event', $lastEvent, 'vtFlashSale');

        $programId = isset($values['program_id']) ? $values['program_id'] : null;
        $type = isset($values['type']) ? $values['type'] : null;
        $clickSessValues = self::generateInteractiveSession($values['event'], $programId, $type);
        $values = array(
          'type' => $clickSessValues['type'],
          'program_id' => $clickSessValues['program_id'],
          'click_sess_id' => $clickSessValues['sess_id'],
          'event' => $values['event'],
          'object_id' => isset($values['object_id']) ? $values['object_id'] : null,
          'object_type' => isset($values['object_type']) ? $values['object_type'] : null,
          'object_name' => isset($values['object_name']) ? $values['object_name'] : null,
          'order_id' => isset($values['order_id']) ? $values['order_id'] : null,
          'order_code' => isset($values['order_code']) ? $values['order_code'] : null,
          'order_phone' => isset($values['order_phone']) ? $values['order_phone'] : null
        );

        try {
            // Luu log
            $vtFlashSaleActivityLog = new VtFlashSaleActivityLog();
            $vtFlashSaleActivityLog->setProgramId($values['program_id']);
            $vtFlashSaleActivityLog->setType($values['type']);
            $vtFlashSaleActivityLog->setClickSessId($values['click_sess_id']);
            $vtFlashSaleActivityLog->setEvent($values['event']);
            $vtFlashSaleActivityLog->setObjectId($values['object_id']);
            $vtFlashSaleActivityLog->setObjectType($values['object_type']);
            $vtFlashSaleActivityLog->setObjectName($values['object_name']);
            $vtFlashSaleActivityLog->setOrderId($values['order_id']);
            $vtFlashSaleActivityLog->setOrderCode($values['order_code']);
            $vtFlashSaleActivityLog->setOrderPhone($values['order_phone']);
            $vtFlashSaleActivityLog->save();
            // Kiem tra neu la buoc dat hang thanh cong -> xoa session
            if ($values['event'] == VtFlashSaleInteractiveLogEventEnum::ORDER_SUCCESS) {
                $sfUser->setAttribute('flashsale_interactive_log_click_sess_values', null, 'vtFlashSale');
            }
            return true;
        } catch (Exception $e) {
            $logger = VtHelper::getLogger4Php("all");
            $logger->error("writeFlashSaleInteractiveLog|Exception when writing flash sale interactive log, error=" . $e->getMessage());
            return false;
        }
    }

    public static function generateInteractiveSession($event, $programId = null, $type = null) {
        $sfUser = sfContext::getInstance()->getUser();
        if ($event != VtFlashSaleInteractiveLogEventEnum::CLICK && $sfUser->hasAttribute('flashsale_interactive_log_click_sess_values', 'vtFlashSale')) {
            return unserialize($sfUser->getAttribute('flashsale_interactive_log_click_sess_values', null, 'vtFlashSale'));
        }
        $newSessId = md5(session_id() . date("YmdHis") . uniqid());
        $values = array(
          'type' => $type,
          'program_id' => $programId,
          'sess_id' => $newSessId
        );
        $sfUser->setAttribute('flashsale_interactive_log_click_sess_values', serialize($values), 'vtFlashSale');
        // Reset last event
        $sfUser->setAttribute('flashsale_interactive_log_last_event', null, 'vtFlashSale');
        return $values;
    }
}

//@tungtd2
// function to call post webservice QTAN

/**
 * Chuyen doi ky tu ASCII ve dang chuan UNICODE
 * @author HoangL
 * @param        $unicode
 * @param string $encoding
 * @return string
 */
function unichr($unicode, $encoding = 'UTF-8') {
    return mb_convert_encoding("&#{$unicode};", $encoding, 'HTML-ENTITIES');
}

/**
 * Tra ve ma ASCII
 * @param        $string
 * @param string $encoding
 * @return mixed
 */
function uniord($string, $encoding = 'UTF-8') {
    $entity = mb_encode_numericentity($string, array(0x0, 0xffff, 0, 0xffff), $encoding);
    return preg_replace('`^&#([0-9]+);.*$`', '\\1', $entity);
}

function remove_utf8_bom($text)
{
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}



