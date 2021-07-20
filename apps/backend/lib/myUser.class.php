<?php

class myUser extends sfGuardSecurityUser
{
    //tuanbm2 set mac dinh language tieng viet:
    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array())
    {
        parent::initialize($dispatcher, $storage, $options);
        $this->setCulture('vi');
    }

    public function setIpAddress($ip) {
        $this->setAttribute('IpAddress', $ip);
    }

    public function getIpAddress() {
        return $this->getAttribute('IpAddress', null);
    }

    public function setUserAgent($userAgent) {
        $this->setAttribute('UserAgent', $userAgent);
    }

    public function getUserAgent() {
        return $this->getAttribute('UserAgent', null);
    }

    public function checkPermission($permissions, $includeAdmin = true){
        if($this->getGuardUser()->getIsSuperAdmin()){
            return $includeAdmin;
        }else{
            if(is_array($permissions)){
                foreach ($permissions as $permission){
                    if($this->hasPermission($permission)){
                        return true;
                    }
                }
            }else{
                if($this->hasPermission($permissions))
                    return true;
            }
        }
        return false;
    }
}
