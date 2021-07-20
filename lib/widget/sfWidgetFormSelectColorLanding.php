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
class sfWidgetFormSelectColorLanding extends sfWidgetFormChoiceBase
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

    return $this->formatChoices($name, $value, $choices, $attributes);
  }

  protected function formatChoices($name, $value, $choices, $attributes)
  {
    $inputs = array();
    foreach ($choices as $key => $option) {
      $baseAttributes = array(
        'name' => $name,
        'type' => $this->getOption('type'),
        'value' => self::escapeOnce($key),
        'id' => $id = $this->generateId($name, self::escapeOnce($key)),
      );

      $vtColor = $option['vtColor'];

      if ((is_array($value) && in_array(strval($key), $value)) || (is_string($value) && strval($key) == strval($value))) {
        $baseAttributes['checked'] = 'checked';
      }

      $labelAttribute = [
        'class' => 'color-box',
        'for' => $id
      ];
      $inputs[$id] = array(
        'input' => $this->renderTag('input', array_merge($baseAttributes, $attributes)),
        'label' => $this->renderTag('label',$labelAttribute),
        'color' => $vtColor->getHexCode()
      );
    }

    return call_user_func($this->getOption('formatter'), $this, $inputs);
  }

  public function formatter($widget, $inputs)
  {
    $rows = array();
    $style = '';
    foreach ($inputs as $id => $input) {
      $style .= sprintf("#%s + .color-box{background-color: #%s;border:2px solid #%s}",$id,$input['color'],$input['color']);
      $style .= sprintf("#%s:checked + .color-box{background-color: #%s;border:2px solid red}",$id,$input['color']);
      $rows[] = $this->renderContentTag('div', $input['input'].$input['label'],['class' => 'radio-inline']);
    }
    $renderStyle = sprintf('<style>%s</style>', $style);
    return !$rows ? '' : implode($this->getOption('separator'), $rows).$renderStyle;
  }
}
