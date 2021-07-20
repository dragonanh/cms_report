<?php

/**
 * VtCttTransaction filter form.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtRegistrationFixedRevenueReportAdminFormFilter extends BaseVtCttTransactionFormFilter
{
    public function configure()
    {
        $i18n = sfContext::getInstance()->getI18N();
        $this->useFields(['isdn']);

        $this->widgetSchema['isdn'] = new sfWidgetFormInputText();
        $this->validatorSchema['isdn'] = new sfValidatorString([
            'required' => false
        ]);

        $this->widgetSchema['package_name'] = new sfWidgetFormInputText();
        $this->validatorSchema['package_name'] = new sfValidatorString([
            'required' => false
        ]);

        $serviceTypeArr = array('' => $i18n->__('Tất cả'), 0 => $i18n->__('Internet'), 1 => $i18n->__('Combo'), 2 => $i18n->__('Truyền hình'));
        $this->widgetSchema['service_type'] = new sfWidgetFormChoice(array(
            'choices' => $serviceTypeArr
        ));
        $this->validatorSchema['service_type'] = new sfValidatorChoice(array(
            'required' => false, 'choices' => array_keys($serviceTypeArr)
        ));

        $totalFeeArr = array('' => $i18n->__('Tất cả'), 0 => $i18n->__('Miễn phí'), 1 => $i18n->__('Mất phí'));
        $this->widgetSchema['total_fee'] = new sfWidgetFormChoice(array(
            'choices' => $totalFeeArr
        ));
        $this->validatorSchema['total_fee'] = new sfValidatorChoice(array(
            'required' => false, 'choices' => array_keys($totalFeeArr)
        ));

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
            'status' => $i18n->__('Trạng thái'),
            'isdn' => $i18n->__('SĐT đăng ký/acc đăng ký dịch vụ'),
            'package_name' => $i18n->__('Gói cước đăng ký'),
            'service_type' => $i18n->__('Loại dịch vụ'),
            'total_fee' => $i18n->__('Tổng tiền phải thanh toán')
        ]);
    }

    protected function doBuildQuery(array $values)
    {
        $query = parent::doBuildQuery($values)
            ->andWhere('order_type = 12');

        if (isset($values['process_time']['from']))
            $query->andWhere('created_at >= ?', $values['process_time']['from']);

        if (isset($values['process_time']['to']))
            $query->andWhere('created_at <= ?', $values['process_time']['to']);

        if (isset($values['isdn']) && $values['isdn'] !== '') {
            $query->andWhere('isdn = ?', VtHelper::getMobileNumber($values['isdn'], VtHelper::MOBILE_NOTPREFIX));
        }

        if (isset($values['package_name']) && $values['package_name'] !== '') {
            $query->andWhere('ctt_package_name like ?', '%' . $values['package_name'] . '%');
        }

        if (isset($values['service_type']) && $values['service_type'] !== '') {
            if ($values['service_type'] == 0) {
                $query->andWhere('service_indicator = "F"');
            } elseif ($values['service_type'] == 2) {
                $query->andWhere('service_indicator = "U" OR service_indicator = "2"');
            } else {
                $query->andWhereNotIn('service_indicator', ['F', 'U', '2']);
            }
        }

        if (isset($values['total_fee']) && $values['total_fee'] !== '') {
            if ($values['total_fee'] == 0) { // Miễn phí
                $query->andWhere('content LIKE ?', '%' . '"totalFee":0' . '%');
            } else { // Mất phí
                $query->andWhere('content NOT LIKE ?', '%' . '"totalFee":0' . '%');
            }
        }

        $query->orderBy('created_at DESC');

        return $query;
    }
}
