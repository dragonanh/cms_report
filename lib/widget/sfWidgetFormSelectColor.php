<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormSelectCheckbox represents an array of checkboxes.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormSelectCheckbox.class.php 33362 2012-03-08 13:53:08Z fabien $
 */
class sfWidgetFormSelectColor extends sfWidgetFormChoiceBase
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * choices:         An array of possible choices (required)
   *  * label_separator: The separator to use between the input checkbox and the label
   *  * class:           The class to use for the main <ul> tag
   *  * separator:       The separator to use between each input checkbox
   *  * formatter:       A callable to call to format the checkbox choices
   *                     The formatter callable receives the widget and the array of inputs as arguments
   *  * template:        The template to use when grouping option in groups (%group% %options%)
   *
   * @param array $options An array of options
   * @param array $attributes An array of default HTML attributes
   *
   * @see sfWidgetFormChoiceBase
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('type', 'checkbox');
    $this->addOption('class', 'checkbox_list');
    $this->addOption('label_separator', '&nbsp;');
    $this->addOption('separator', "\n");
    $this->addOption('formatter', array($this, 'formatter'));
    $this->addOption('template', '%group% %options%');
    $this->addOption('is_frontend', true);
    $this->addOption('display_product_code', false);
    $this->addOption('display_partner_product_code', false);
  }

  /**
   * Renders the widget.
   *
   * @param  string $name The element name
   * @param  string $value The value selected in this widget
   * @param  array $attributes An array of HTML attributes to be merged with the default HTML attributes
   * @param  array $errors An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if ('[]' != substr($name, -2)) {
      $name .= '[]';
    }

    if (null === $value) {
      $value = array();
    }

    $choices = $this->getOption('choices');

    // with groups?
//    if (count($choices) && is_array(current($choices))) {
//      $parts = array();
//      foreach ($choices as $key => $option) {
//        $parts[] = strtr($this->getOption('template'), array('%group%' => $key, '%options%' => $this->formatChoices($name, $value, $option, $attributes)));
//      }
//
//      return implode("\n", $parts);
//    } else {
    return $this->formatChoices($name, $value, $choices, $attributes);
//    }
  }

  protected function formatChoices($name, $value, $choices, $attributes)
  {
    $inputs = array();
    foreach ($choices as $key => $option) {
      $baseAttributes = array(
        'name' => $name,
        'type' => $this->getOption('type'),
        'value' => self::escapeOnce($key),
        'id' => $id = $this->generateId($name, self::escapeOnce($key))
      );

      $isFrontend = $this->getOption('is_frontend');
      if ($isFrontend) {
        $vtColor = $option['vtColor'];
        $baseAttributes = array_merge($baseAttributes, array('data-price' => VtHelper::formatNumber($option['vtDeviceColor']->getPrice()) . 'đ'));
      } else {
        $vtColor = $option['vtColor'];
        $vtDeviceColor = $option['vtDeviceColor'];
      }

      if ((is_array($value) && in_array(strval($key), $value)) || (is_string($value) && strval($key) == strval($value))) {
        $baseAttributes['checked'] = 'checked';
      }

      $inputs[$id] = array(
        'input' => $this->renderTag('input', array_merge($baseAttributes, $attributes)),
        'label' => $this->renderContentTag('label', $vtColor->getName(), array('for' => $id)),
        'div' => $this->renderContentTag('div', '', array(
          'style' => 'background-color: #' . self::escapeOnce($vtColor->getHexCode()),
          'class' => 'color-box'
        ))
      );

      if (!$isFrontend) {
        $deviceColorArr = array(
          'inputPrice' => $this->renderTag('input', array(
            'name' => 'vt_device[price][' . $key . ']',
            'type' => 'text',
            'value' => $vtDeviceColor ? $vtDeviceColor->getPrice() : null,
            'placeholder' => 'Giá'
          )),
          'inputOldPrice' => $this->renderTag('input', array(
            'name' => 'vt_device[old_price][' . $key . ']',
            'type' => 'text',
            'value' => $vtDeviceColor ? $vtDeviceColor->getOldPrice() : null,
            'placeholder' => 'Giá cũ'
          )),
          'inputQuantity' => $this->renderTag('input', array(
            'name' => 'vt_device[quantity][' . $key . ']',
            'type' => 'text',
            'value' => $vtDeviceColor ? $vtDeviceColor->getQuantity() : null,
            'placeholder' => 'Số lượng'
          ))
        );

        if($this->getOption('display_product_code')){
          $deviceColorArr['inputProductCode'] = $this->renderTag('input', array(
            'name' => 'vt_device[product_code][' . $key . ']',
            'type' => 'text',
            'value' => $vtDeviceColor ? $vtDeviceColor->getProductCode() : null,
            'placeholder' => 'Mã mặt hàng'
          ));
        }
        if($this->getOption('display_partner_product_code')){
          $deviceColorArr['inputPartnerProductCode'] = $this->renderTag('input', array(
            'name' => 'vt_device[partner_product_code][' . $key . ']',
            'type' => 'text',
            'value' => $vtDeviceColor ? $vtDeviceColor->getPartnerProductCode() : null,
            'placeholder' => 'Mã mặt hàng đối tác'
          ));
        }

        $inputs[$id] = array_merge($inputs[$id], $deviceColorArr);
      }
    }

    return call_user_func($this->getOption('formatter'), $this, $inputs);
  }

  public function formatter($widget, $inputs)
  {
    $rows = array();

    foreach ($inputs as $input) {
      $productCodeRow = $this->getOption('display_product_code') ?  $input['inputProductCode'] . $this->getOption('label_separator') : '';
      $partnerProductCodeRow = $this->getOption('display_partner_product_code') ?  $input['inputPartnerProductCode'] . $this->getOption('label_separator') : '';
      $rows[] = $this->renderContentTag('li', $input['inputPrice'] . $this->getOption('label_separator')
      . $input['inputOldPrice'] . $this->getOption('label_separator')
      . $input['inputQuantity'] . $this->getOption('label_separator')
        . $productCodeRow
        . $partnerProductCodeRow
      . $input['input'] . $this->getOption('label_separator')
      . $input['div'] . $this->getOption('label_separator')
      . $input['label']);
    }

    return !$rows ? '' : $this->renderContentTag('ul', implode($this->getOption('separator'), $rows), array('class' => $this->getOption('class')));
  }
}
