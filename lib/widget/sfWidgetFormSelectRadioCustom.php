<?php
/**
 * Created by PhpStorm.
 * User: anhbhv
 * Date: 8/17/2018
 * Time: 9:56 AM
 */

class sfWidgetFormSelectRadioCustom extends sfWidgetFormSelectRadio
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('wrapTag', 'ul');
    $this->addOption('itemTag', 'li');
    $this->addOption('itemClass', '');
    $this->addOption('labelClass', '');
  }

  protected function formatChoices($name, $value, $choices, $attributes)
  {
    $inputs = array();
    foreach ($choices as $key => $option)
    {
      $baseAttributes = array(
        'name'  => substr($name, 0, -2),
        'type'  => 'radio',
        'value' => self::escapeOnce($key),
        'id'    => $id = $this->generateId($name, self::escapeOnce($key)),
      );

      if (strval($key) == strval($value === false ? 0 : $value))
      {
        $baseAttributes['checked'] = 'checked';
      }
      $inputs[$id] = array(
        'input' => $this->renderTag('input', array_merge($baseAttributes, $attributes)),
        'label' => $this->renderContentTag('label', self::escapeOnce($option), array('for' => $id, 'class' => $this->getOption('labelClass'))),
      );
    }
    return call_user_func($this->getOption('formatter'), $this, $inputs);
  }

  public function formatter($widget, $inputs)
  {
    $wrapperTag = $this->getOption('wrapTag');
    $itemTag = $this->getOption('itemTag');
    $rows = array();
    foreach ($inputs as $input)
    {
      $rows[] = $this->renderContentTag($itemTag, $input['input'].$this->getOption('label_separator').$input['label'],array('class' => $this->getOption('itemClass')));
    }
    $contentHtml = $wrapperTag ? $this->renderContentTag($wrapperTag, implode($this->getOption('separator'), $rows), array('class' => $this->getOption('class'))) : implode($this->getOption('separator'), $rows);
    return !$rows ? '' : $contentHtml;
  }
}