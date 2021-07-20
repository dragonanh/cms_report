<?php
/**
 * Created by PhpStorm.
 * User: halt14
 * Date: 11/15/2016
 * Time: 2:28 PM
 */
class vtManageBackendUserCancelImportForm extends sfForm
{
    public function configure()
    {
        parent::configure();
        $i18n = sfContext::getInstance()->getI18N();

        $this->widgetSchema['file'] = new sfWidgetFormInputFile(array(), array(
            'class' => 'btn'
        ));

        $maxSizeFile = sfConfig::get('app_max_size_file_sim', 200);
        $maxSize = $maxSizeFile * 1024;

        $this->validatorSchema['file'] = new sfValidatorFileViettel(array(
            'max_size' => $maxSize,
            'mime_types' => array(
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/octet-stream',
                'application/zip',
            ),
            'extensions' => array('xls', 'xlsx'),
            'path' => sfConfig::get('sf_web_dir') . 'uploads/excel'
        ));
        $this->widgetSchema->setNameFormat('excel-cancel-import[%s]');
    }


}