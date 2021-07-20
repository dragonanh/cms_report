<?php
/**
 * Created by PhpStorm.
 * User: halt14
 * Date: 11/15/2016
 * Time: 2:28 PM
 */
class vtareaFormImportExcel extends sfForm
{
    public function configure()
    {
        parent::configure();
        $i18n = sfContext::getInstance()->getI18N();
        $this->widgetSchema['image'] = new sfWidgetFormInputFile(array(), array(
            'class' => 'btn'
        ));

        $this->widgetSchema['file'] = new sfWidgetFormInputFile(array(), array(
            'class' => 'btn'
        ));

        $this->widgetSchema['refundId'] = new sfWidgetFormInputFile(array(), array(
            'class' => 'hidden'
        ));
        $maxSizeFile = sfConfig::get('app_max_size_file_sim', 200);
        $maxSize = 50 * $maxSizeFile * 1024;

        $this->validatorSchema['image'] = new sfValidatorPass();
        $this->validatorSchema['refundId'] = new sfValidatorPass();

        $this->validatorSchema['file'] = new sfValidatorFileViettel(array(
            'max_size' => $maxSize,
            'mime_types' => array(
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/octet-stream',
                'application/wps-office.xlsx',
                'application/zip',
            ),
            'extensions' => array('xls', 'xlsx'),
            'path' => sfConfig::get('sf_web_dir') . 'uploads/excel'
        ));
        $this->widgetSchema->setNameFormat('excel-import[%s]');
    }


}