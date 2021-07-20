<?php

/**
 * VtVpgTransaction filter form.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FlashSaleLogAdminFormFilter extends BaseFlashSaleLogFormFilter
{
    public function configure()
    {
        $i18n = sfContext::getInstance()->getI18N();
        $this->useFields(['msisdn', 'pack_code', 'app_code', 'serial', 'processed']);
//
//    $channelArr = array('' => $i18n->__('Tất cả')) + VtCttChannelEnum::getArr();
//    $this->widgetSchema['channel'] =  new sfWidgetFormChoice(array(
//      'choices' => $channelArr
//    ));
//    $this->validatorSchema['channel'] =  new sfValidatorChoice(array(
//      'required' => false, 'choices' => array_keys($channelArr)
//    ));
//
        $appCode = array('' => $i18n->__('Tất cả'), 'WEBPORTAL' => $i18n->__('WEBPORTAL'), 'MyViettel' => $i18n->__('MyViettel'));
        $this->widgetSchema['app_code'] = new sfWidgetFormChoice(array(
            'choices' => $appCode
        ));
//    $this->validatorSchema['app_code'] =  new sfValidatorChoice(array(
//      'required' => false, 'choices' => array_keys($appCode)
//    ));
//
        $omniErrorCodeArr = array('' => $i18n->__('Tất cả'), 0 => $i18n->__('Chưa tặng thẻ cào'), 1 => $i18n->__('Đã tặng thẻ cào'));
        $this->widgetSchema['processed'] = new sfWidgetFormChoice(array(
            'choices' => $omniErrorCodeArr
        ));
//    $this->validatorSchema['omni_error_code'] =  new sfValidatorChoice(array(
//      'required' => false, 'choices' => array_keys($omniErrorCodeArr)
//    ));
//
        $this->widgetSchema['msisdn'] = new sfWidgetFormInputText();
        $this->widgetSchema['pack_code'] = new sfWidgetFormInputText();
        $this->widgetSchema['serial'] = new sfWidgetFormInputText();
//    $this->validatorSchema['isdn'] = new sfValidatorString([
//      'required' => false
//    ]);
//
//    $this->widgetSchema['tran_id'] = new sfWidgetFormInputText();
//    $this->validatorSchema['tran_id'] = new sfValidatorString([
//      'required' => false
//    ]);
//
//	$cttPackgeArr = array('' => $i18n->__('Tất cả'), 1 => $i18n->__('Trả trước'), 2 => $i18n->__('Trả sau'));
//    $this->widgetSchema['ctt_package'] =  new sfWidgetFormChoice(array(
//      'choices' => $cttPackgeArr
//    ));
//    $this->validatorSchema['ctt_package'] =  new sfValidatorChoice(array(
//      'required' => false, 'choices' => array_keys($cttPackgeArr)
//    ));
//
        $this->widgetSchema['process_time'] = new sfWidgetFormFilterDate(array(
            'from_date' => new sfWidgetFormVnDatePicker(array('jq_picker_options' => array('changeMonth' => true, 'changeYear' => true)), array('readonly' => 'readonly')),
            'to_date' => new sfWidgetFormVnDatePicker(array('jq_picker_options' => array('changeMonth' => true, 'changeYear' => true)), array('readonly' => 'readonly')),
            'template' => '%from_date% ' . $i18n->__('đến ngày') . ' %to_date%',
            'with_empty' => false,
        ));
        $this->validatorSchema['process_time'] = new sfValidatorDateRange(
            array('required' => false,
                'from_date' => new sfValidatorVnDateTime(
                    array(
                        'required' => false,
                        'datetime_output' => 'Y-m-d 00:00:00'
                    ), array(
                        'required' => $i18n->__('Vui lòng chọn khoảng thời gian muốn tra cứu')
                    )
                ),
                'to_date' => new sfValidatorVnDateTime(
                    array(
                        'required' => false,
                        'datetime_output' => 'Y-m-d 23:59:59'
                    ), array(
                        'required' => $i18n->__('Vui lòng chọn khoảng thời gian muốn tra cứu')
                    )
                )),
            array(
                'invalid' => $i18n->__('Từ ngày phải nhỏ hơn hoặc bằng đến ngày')
            )
        );
//
        $this->widgetSchema->setLabels([
            'msisdn' => $i18n->__('Số thuê bao'),
            'pack_code' => $i18n->__('Gói cước'),
            'app_code' => $i18n->__('Kênh đăng ký gói'),
            'serial' => $i18n->__('Serial thẻ cào'),
            'processed' => $i18n->__('Trạng thái tặng thẻ'),
        ]);
    }

    protected function doBuildQuery(array $values)
    {
        $query = parent::doBuildQuery($values);
        $alias = $query->getRootAlias();

        if (isset($values['process_time']['from']))
            $query->andWhere('created_at >= ?', $values['process_time']['from']);

        if (isset($values['process_time']['to']))
            $query->andWhere('created_at <= ?', $values['process_time']['to']);


        if (isset($values['processed']) && $values['processed'] !== '') {
            if ($values['processed'] == 0)
                $query->andWhere('(processed = 0)');
            else
                $query->andWhere('processed = ?', $values['processed']);
        }

        if (isset($values['msisdn']) && $values['msisdn'] !== '') {
            $query->andWhere('msisdn like ?', '%' . VtHelper::getMobileNumber($values['msisdn'], VtHelper::MOBILE_NOTPREFIX) . '%');
        }

        if (isset($values['app_code']) && $values['app_code'] !== '') {
            $query->andWhere('app_code = ?', $values['app_code']);
        }

        if (isset($values['pack_code']) && $values['pack_code'] !== '') {
            $query->andWhere('pack_code = ?', $values['pack_code']);
        }

        if (isset($values['serial']) && $values['serial'] !== '') {
            $query->andWhere('serial = ?', $values['serial']);
        }

        $query->orderBy('created_at DESC');

        return $query;
    }
}
