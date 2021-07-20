<?php

/**
 * VtVpgTransaction filter form.
 *
 * @package    cms_ctt
 * @subpackage filter
 * @author     viettel
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PartnerTransactionReportAdminFormFilter extends BasePartnerTransactionFormFilter
{
    public function configure()
    {
        $i18n = sfContext::getInstance()->getI18N();
        $this->useFields(['transaction_id', 'status','status_viettel_id', 'refund_status','refund_viettel_id']);

//        $statusArr = array('' => $i18n->__('Tất cả'), 1 => $i18n->__('Thanh toán thành công'), 0 => $i18n->__('Thanh toán thất bại'));
        $statusArr = array('' => $i18n->__('Tất cả'), 1 => $i18n->__('Thanh toán thành công'));
        $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
            'choices' => $statusArr
        ));
        $this->validatorSchema['status'] = new sfValidatorChoice(array(
            'required' => false, 'choices' => array_keys($statusArr)
        ));

        $status_viettel_id_array = array('' => $i18n->__('Tất cả'), 1 => $i18n->__('Trừ điểm thành công'), 0 => $i18n->__('Trừ điểm thất bại'));
        $this->widgetSchema['status_viettel_id'] = new sfWidgetFormChoice(array(
            'choices' => $status_viettel_id_array
        ));
        $this->validatorSchema['status_viettel_id'] = new sfValidatorChoice(array(
            'required' => false, 'choices' => array_keys($status_viettel_id_array)
        ));

        $refund_viettel_id = array('' => $i18n->__('Tất cả'), 1 => $i18n->__('Hoàn điểm thành công'), 0 => $i18n->__('Hoàn điểm thất bại'));
        $this->widgetSchema['refund_viettel_id'] = new sfWidgetFormChoice(array(
            'choices' => $refund_viettel_id
        ));
        $this->validatorSchema['refund_viettel_id'] = new sfValidatorChoice(array(
            'required' => false, 'choices' => array_keys($refund_viettel_id)
        ));

        $statusArr = array('' => $i18n->__('Tất cả'),
            1 => $i18n->__('Đã gửi yêu cầu hoàn tiền, đang chờ xác nhận hoàn tiền'),
            2 => $i18n->__('Hoàn tiền thành công'),
            3 => $i18n->__('Hoàn tiền thất bại'),
            4 => $i18n->__('Đã từ chối'),
            5 => $i18n->__('Đã duyệt yêu cầu hoàn tiền'),
            6 => $i18n->__('Gửi yêu cầu hoàn tiền thất bại'),
        );
        $this->widgetSchema['refund_status'] = new sfWidgetFormChoice(array(
            'choices' => $statusArr
        ));
        $this->validatorSchema['refund_status'] = new sfValidatorChoice(array(
            'required' => false, 'choices' => array_keys($statusArr)
        ));


        $this->widgetSchema['transaction_id'] = new sfWidgetFormInputText();
        $this->validatorSchema['transaction_id'] = new sfValidatorString([
            'required' => false
        ]);


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

        $this->widgetSchema->setLabels([
            'transaction_id' => $i18n->__('Mã giao dịch'),
            'status' => $i18n->__('Trạng thái giao dịch'),
            'refund_status' => $i18n->__('Trạng thái hoàn tiền'),
            'status_viettel_id' => $i18n->__('Trạng thái trừ điểm'),
            'refund_viettel_id' => $i18n->__('Trạng thái hoàn điểm'),
        ]);
    }

    protected function doBuildQuery(array $values)
    {
        $query = parent::doBuildQuery($values);
        $fromTime = !empty($values['process_time']['from']) ? $values['process_time']['from'] : date('Y-m-d', strtotime('now'));
        $query->andWhere('created_at >= ?', $fromTime);

        if (isset($values['process_time']['to']))
            $query->andWhere('updated_at <= ?', $values['process_time']['to']);

        if (isset($values['status']) && $values['status'] !== '') {
            if ($values['status'] == 0)
                $query->andWhere('(status = 0 OR status is null)');
            else
                $query->andWhere('(status <> 0)');
        }

        if (isset($values['status_viettel_id']) && $values['status_viettel_id'] !== '') {
            if ($values['status_viettel_id'] == 0)
                $query->andWhere('(status_viettel_id = 0 OR status_viettel_id is null)');
            else
                $query->andWhere('(status_viettel_id <> 0)');
        }

        if (isset($values['transaction_id']) && $values['transaction_id'] !== '') {
            $query->andWhere('transaction_id = ?', $values['transaction_id']);
        }

        if (isset($values['refund_status']) && $values['refund_status'] !== '') {
            $query->andWhere('refund_status = ?', $values['refund_status']);
        }

        if (isset($values['refund_viettel_id']) && $values['refund_viettel_id'] !== '') {
            $query->andWhere('refund_viettel_id = ?', $values['refund_viettel_id']);
        }

        $query->orderBy('updated_at DESC');

        return $query;
    }
}
