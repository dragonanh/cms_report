<?php

/**
 * VtCttTransaction filter form.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vtPaymentDebtAdminFormFilter extends BaseVtPaymentDebtFormFilter
{
    public function configure()
    {
        $i18n = sfContext::getInstance()->getI18N();
        $this->useFields(['msisdn', 'order_type', 'service_type', 'price', 'status', 'staff_code', 'utm_source', 'aff_sid', 'utm_medium']);
        $this->widgetSchema['msisdn'] = new sfWidgetFormInputText();
        $this->validatorSchema['msisdn'] = new sfValidatorString([
            'required' => false
        ]);
        $this->widgetSchema['price'] = new sfWidgetFormInputText();
        $this->validatorSchema['price'] = new sfValidatorString([
            'required' => false
        ]);
        $this->widgetSchema['staff_code'] = new sfWidgetFormInputText();
        $this->validatorSchema['staff_code'] = new sfValidatorString([
            'required' => false
        ]);
        $this->widgetSchema['utm_source'] = new sfWidgetFormInputText();
        $this->validatorSchema['utm_source'] = new sfValidatorString([
            'required' => false
        ]);
        $this->widgetSchema['aff_sid'] = new sfWidgetFormInputText();
        $this->validatorSchema['aff_sid'] = new sfValidatorString([
            'required' => false
        ]);
        $this->widgetSchema['utm_medium'] = new sfWidgetFormInputText();
        $this->validatorSchema['utm_medium'] = new sfValidatorString([
            'required' => false
        ]);
        $omniErrorCodeArr = array('' => $i18n->__('Tất cả'), 37 => $i18n->__('Hàm gạch nợ'), 24 => $i18n->__('Hàm topup'));
        $this->widgetSchema['order_type'] = new sfWidgetFormChoice(array(
            'choices' => $omniErrorCodeArr
        ));
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

        $serviceTypeArr = array('' => $i18n->__('Tất cả'), 1 => $i18n->__('Topup'), 2 => $i18n->__('Thanh toán cước di động trả sau'), 3 => $i18n->__('Thanh toán cước di dộng cố định'));
        $this->widgetSchema['service_type'] = new sfWidgetFormChoice(array(
            'choices' => $serviceTypeArr
        ));
        $arrStatus = array('' => $i18n->__('Tất cả'), 0 => $i18n->__('Thất bại'), 1 => $i18n->__('Thành công'));
        $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
            'choices' => $arrStatus
        ));
        $this->widgetSchema->setLabels([
            'msisdn' => $i18n->__('Thuê bao'),
            'order_type' => $i18n->__('Tên hàm'),
            'service_type' => $i18n->__('Loại giao dịch'),
            'price' => $i18n->__('Giá tiền'),
            'status' => $i18n->__('Trạng thái'),
            'staff_code' => $i18n->__('Mã nhân viên tư vấn'),
            'created_at' => $i18n->__('Ngày tạo'),
            'utm_source' => $i18n->__('utm_source'),
            'aff_sid' => $i18n->__('aff_sid'),
            'utm_medium' => $i18n->__('utm_medium'),
        ]);
    }

    protected function doBuildQuery(array $values)
    {
        $query = parent::doBuildQuery($values);

        $alias = $query->getRootAlias();
        $query->select($alias . '.vt_payment_debt, *');
        if (isset($values['process_time']['from']))
            $query->andWhere('created_at >= ?', $values['process_time']['from']);

        if (isset($values['process_time']['to']))
            $query->andWhere('created_at <= ?', $values['process_time']['to']);

        if (isset($values['msisdn']) && $values['msisdn'] !== '') {
            $query->andWhere($alias . '.msisdn like ?', '%' . VtHelper::getMobileNumber($values['msisdn'], VtHelper::MOBILE_NOTPREFIX) . '%');
        }
        if (isset($values['status']) && $values['status'] !== '') {
            $query->andWhere($alias.'.status = ?', $values['status']);
        }
        if (isset($values['service_type']) && $values['service_type'] !== '') {
            $query->andWhere($alias.'.service_type = ?', $values['service_type']);
        }
        if (isset($values['price']) && $values['price'] !== '') {
            $query->andWhere($alias.'.price = ?', $values['price']);
        }
        if (isset($values['order_type']) && $values['order_type'] !== '') {
            $query->andWhere($alias.'.order_type = ?', $values['order_type']);
        }
        if (isset($values['staff_code']) && $values['staff_code'] !== '') {
            $query->andWhere($alias . '.staff_code = ?', $values['staff_code']);
        }
        if (isset($values['utm_source']) && $values['utm_source'] !== '') {
            $query->andWhere($alias . '.utm_source = ?', $values['utm_source']);
        }
        if (isset($values['aff_sid']) && $values['aff_sid'] !== '') {
            $query->andWhere($alias . '.aff_sid = ?', $values['aff_sid']);
        }
        if (isset($values['utm_medium']) && $values['utm_medium'] !== '') {
            $query->andWhere($alias . '.utm_medium = ?', $values['utm_medium']);
        }

        $query->orderBy('created_at DESC');
        return $query;
    }
}
