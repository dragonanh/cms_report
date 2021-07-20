<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorString validates a string. It also converts the input value to a string.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorString.class.php 12641 2008-11-04 18:22:00Z fabien $
 */
class sfValidatorCKEditor extends sfValidatorString {

    /**
     * Configures the current validator.
     *
     * Available options:
     *  * clean_xss: remove xss tag in string
     * @param array $options   An array of options
     * @param array $messages  An array of error messages
     *
     * @see sfValidatorBase
     */
    protected function configure($options = array(), $messages = array()) {
      $this->addOption('clean_xss');
      parent::configure($options, $messages);
    }

    /**
     * @param mixed $value
     * @return mixed|string
     */
    protected function doClean($value) {

      $value = $this->getOption('clean_xss') ? VtHelper::strip_html_tags_and_decode($value) : $value;

      return parent::doClean($value);

    }

}
