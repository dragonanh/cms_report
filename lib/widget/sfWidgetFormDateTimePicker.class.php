<?php

class sfWidgetFormDateTimePicker extends sfWidgetFormDateTime
{

  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('display_format', 'd/m/Y');
    parent::configure($options, $attributes);
  }

  /**
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

    //$html = '<div style="display:none">'.parent::render($name, $value, $attributes, $errors).'</div>';
    return /*$html.*/
      $this->renderDateTimePicker($name, $value, $attributes, $errors);

  }

  protected function renderDateTimePicker($name, $value, $attributes, $errors)
  {
    $widget = new sfWidgetFormInput();
    if ($value) {
      if (preg_match('/^\d+\/\d+\/\d+$/', $value)) {
        $dtime = DateTime::createFromFormat($this->getOption('display_format'), $value);
        $timestamp = $dtime->getTimestamp();
      } else {
        $timestamp = strtotime($value);
      }
      $value = date($this->getOption('display_format'), $timestamp);
    }
    return $widget->render($name, $value, array_merge($attributes, array("readonly" => "readonly")), $errors);
  }

//  public function getJavaScripts()
//  {
//    $javascripts = array(
//      '/dmDateTimePickerPlugin/js/datetimepicker.js',
//      '/dmDateTimePickerPlugin/js/launcher.js'
//    );
//    return array_merge(parent::getJavascripts(), $javascripts);
//  }

}
