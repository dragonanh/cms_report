<?php

class sfWidgetCaptchaGD extends sfWidgetForm {

    protected function configure($options = array(), $attributes = array()) {
        $this->addOption('is_easy');
        $this->addOption('is_4g');
        $this->addOption('with_refresh_icon');
    }

    public function render($name, $value = null, $attributes = array(), $errors = array()) {

        $namespace = isset($attributes['namespace']) ? $attributes['namespace'] : 'default';
        sfContext::getInstance()->getConfiguration()->loadHelpers('Asset', 'Url', 'I18n');
        if($this->getOption('is_4g'))
          $img_src = sfContext::getInstance()->getRouting()->generate("sf_captchagd_4g") . '?sid=' . md5(rand());
        elseif($this->getOption('is_easy'))
          $img_src = sfContext::getInstance()->getRouting()->generate("sf_captchagd_easy") . '?sid=' . md5(rand());
        else
          $img_src = sfContext::getInstance()->getRouting()->generate("sf_captchagd") . '?sid=' . md5(rand());

        $refreshIcon = "";
        if ($this->getOption('with_refresh_icon')) {
          $refreshIcon = "&nbsp;&nbsp;<a href='javascript:void(0);' onclick='$(this).siblings(\"a\").find(\"img\").attr(\"src\", \"" . $img_src . "?r=\" + Math.random() + \"&amp;reload=1\"+ \"&amp;namespace=" . $namespace . "\")'><i class='fa fa-refresh'></i></a>";
        }

        $html = $this->renderTag('input', array_merge(
            array(
              'type' => 'text',
              'name' => $name, 'value' => $value, 'style' => 'width:120px', 'autocomplete' => 'off'), $attributes)) .
                "<a href='' onClick='return false' title='" . __("Reload image") . "'>
                  <img src='$img_src&amp;namespace=$namespace' onClick='this.src=\"$img_src?r=\" + Math.random() + \"&amp;reload=1\"+ \"&amp;namespace=$namespace\"' border='0' class='captcha' />
                </a>" . $refreshIcon;

        return $html;
    }

}
