<?php
require_once dirname(__FILE__).'/../lib/vendor/apache-log4php-2.3.0/src/main/php/Logger.php';
require_once dirname(__FILE__).'/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();
require_once dirname(__FILE__).'/../lib/vendor/spout/Autoloader/autoload.php';
require_once dirname(__FILE__).'/../plugins/htmlpurifier-4.8.0/library/HTMLPurifier.auto.php';
require_once dirname(__FILE__).'/../lib/phpExcel/vendor/autoload.php';

class ProjectConfiguration extends sfProjectConfiguration
{
    public function setup() {
        $this->enablePlugins(array(
//            'sfThumbnailPlugin',
            'sfDoctrinePlugin',
            'sfDoctrineGuardPlugin',
            'sfCKEditorPlugin',
            'sfPhpExcelPlugin',
            'sfFormExtraPlugin',
            'sfDatePickerTimePlugin',
            'tmcTwitterBootstrapPlugin',
            'sfCaptchaGDPlugin',
            'sfRedisPlugin',
            'sfThumbnailPlugin'
        ));
        $this->setWebDir(sfConfig::get('sf_root_dir') . '/web');
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    public function configure() {

    }
}
