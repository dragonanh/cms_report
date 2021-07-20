<?php

/**
 * VtOmniOrderHis filter form.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtStandardAIReportAdminFormFilter extends BaseVtOmniOrderHisFormFilter
{
    public function configure()
    {
        $i18n = sfContext::getInstance()->getI18N();
        $this->useFields(['error_code', 'msisdn', 'order_code']);
//    $this->useFields(['status','isdn']);

        $statusArr = array('' => $i18n->__('Tất cả'), 0 => $i18n->__('Thành công'), 1 => $i18n->__('Thất bại'), 2 => $i18n->__('Chờ kiểm duyệt'));
        $this->widgetSchema['error_code'] = new sfWidgetFormChoice(array(
            'choices' => $statusArr
        ));
        $this->validatorSchema['error_code'] = new sfValidatorChoice(array(
            'required' => false, 'choices' => array_keys($statusArr)
        ));

        $this->widgetSchema['order_code'] = new sfWidgetFormInputText();
        $this->validatorSchema['order_code'] = new sfValidatorString([
            'required' => false
        ]);

        $this->widgetSchema['msisdn'] = new sfWidgetFormInputText();
        $this->validatorSchema['msisdn'] = new sfValidatorString([
            'required' => false
        ]);

        $this->widgetSchema['process_time'] = new sfWidgetFormFilterDate(array(
            'from_date' => new sfWidgetFormVnDatePicker(array('jq_picker_options' => array('changeMonth' => true, 'changeYear' => true)), array('readonly' => 'readonly')),
            'to_date' => new sfWidgetFormVnDatePicker(array('jq_picker_options' => array('changeMonth' => true, 'changeYear' => true)), array('readonly' => 'readonly')),
            'template' => '%from_date% ' . $i18n->__('Đến ngày') . ' %to_date%',
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

        $this->widgetSchema->setLabels([
            'error_code' => $i18n->__('Trạng thái'),
            'order_code' => $i18n->__('Mã Order'),
            'msisdn' => $i18n->__('Thuê bao thực hiện chuẩn hóa'),
        ]);
    }

    protected function doBuildQuery(array $values)
    {
        $query = parent::doBuildQuery($values)
            ->andWhere('order_type = 25');

        if (isset($values['process_time']['from']))
            $query->andWhere('created_at >= ?', $values['process_time']['from']);

        if (isset($values['process_time']['to']))
            $query->andWhere('created_at <= ?', $values['process_time']['to']);

        if (isset($values['error_code']) && $values['error_code'] !== '') {
            if ($values['error_code'] == 0) {
                $query->andWhere('error_code = 0');
                $query->andWhere('order_content rlike ?', '"custAcceptAIResult":true');
            } elseif ($values['error_code'] == 1) {
                $query->andWhere('error_code <> 0');
            } else {
                $query->andWhere('error_code = 0');
                $query->andwhere('order_content = "" OR order_content rlike ?', '"custAcceptAIResult":false');
            }
        }

        if (isset($values['msisdn']) && $values['msisdn'] != '')
            $query->andWhere('msisdn = ?', VtHelper::getMobileNumber($values['msisdn'], VtHelper::MOBILE_GLOBAL));

        if (isset($values['order_code']) && $values['order_code'] != '')
            $query->andWhere('order_code = ?', $values['order_code']);

        $query->orderBy('created_at DESC');

        return $query;
    }
}
