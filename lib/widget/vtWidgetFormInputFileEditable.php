<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormInputFileEditable represents an upload HTML input tag with the possibility
 * to remove a previously uploaded file.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormInputFileEditable.class.php 30762 2010-08-25 12:33:33Z fabien $
 */
class vtWidgetFormInputFileEditable extends sfWidgetFormInputFileEditable
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * file_src:     The current image web source path (required)
   *  * edit_mode:    A Boolean: true to enabled edit mode, false otherwise
   *  * is_image:     Whether the file is a displayable image
   *  * with_delete:  Whether to add a delete checkbox or not
   *  * delete_label: The delete label used by the template
   *  * template:     The HTML template to use to render this widget when in edit mode
   *                  The available placeholders are:
   *                    * %input% (the image upload widget)
   *                    * %delete% (the delete checkbox)
   *                    * %delete_label% (the delete label text)
   *                    * %file% (the file tag)
   *
   * In edit mode, this widget renders an additional widget named after the
   * file upload widget with a "_delete" suffix. So, when creating a form,
   * don't forget to add a validator for this additional field.
   *
   * @param array $options An array of options
   * @param array $attributes An array of default HTML attributes
   *
   * @see sfWidgetFormInputFile
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
  }

  /**
   * Renders the widget.
   *
   * @param  string $name The element name
   * @param  string $value The value displayed in this widget
   * @param  array $attributes An array of HTML attributes to be merged with the default HTML attributes
   * @param  array $errors An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $filePath = sfConfig::get('sf_web_dir') . $this->getOption('file_src');
    if (!is_file($filePath)) {
      $mime = 'image/jpg';
    } else {
      $mime = mime_content_type($filePath);
    }
    if (strstr($mime, "video/")) {
      $this->setOption('is_image', false);
      $template = '<div>%input%<br />
                    <video width="320" height="240" controls>
                    <source src="' . $this->getOption('file_src') . '" type="video/mp4">
                    <source src="' . $this->getOption('file_src') . '" type="video/ogg">
                    Your browser does not support the video tag.
                    </video>
                   </div>';
      $this->setOption('template', $template);
    } else if (strstr($mime, "image/")) {
      $this->setOption('is_image', true);
      $this->setOption('template', "<div>%input%<br /><img style='width:150px; height: auto;' src='" . $this->getOption('file_src') . "' ></div>");
    }
    return parent::render($name, $value, $attributes, $errors);
  }
}
