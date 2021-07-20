<?php

/**
 * Created by PhpStorm.
 * User: tiennx6
 * Date: 17/06/2016
 * Time: 9:08 SA
 */
class VtWidgetAttributeTable extends sfWidgetForm
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * type: The widget type
   *
   * @param array $options An array of options
   * @param array $attributes An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('prefix', '');
    $this->addOption('header', array());
    $this->addOption('input', array());
    $this->addOption('title');
    $this->addOption('data', array());
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
    $th = '';
    $input = '';
    foreach ($this->getOption('header') as $label) {
      $th .= '<th>' . $label . '</th>';
    }
    foreach ($this->getOption('input') as $value) {
      if (is_array($value)) {
        $input .= '<td><select>';
        foreach ($value as $k => $v) {
          $input .= '<option value="' . $k . '">' . $v . '</option>';
        }
        $input .= '</select></td>';
      } else {
        $input .= '<td><input type="text" placeholder="' . $value . '" /></td>';
      }
    }

    $dataTr = '';
    $inputData = '';
    $index = 0;
    foreach ($this->getOption('data') as $dataContent) {
      $dataTr .= '<tr>';
      foreach ($dataContent as $contentKey => $content) {
        if (is_array($this->getOption('input')[$contentKey])) {
          $dataTr .= '<td>' . $this->getOption('input')[$contentKey][$content] . '</td>';
        } else {
          $dataTr .= '<td>' . VtHelper::encodeOutput($content) . '</td>';
        }
        $inputData .= '<input data-index="' . $index . '" name="' . $name . '[' . $index . '][]" type="hidden" value="' . $content . '" />';
      }
      $dataTr .= '<td><button class="btn btn-danger attr-del" data-index="' . $index . '">Xóa</button></td></tr>';
      $index++;
    }

    $html = '<button type="button" name="' . $name . '" class="btn" style="cursor:pointer;" data-toggle="modal" data-target="#' . $this->getOption('prefix') . 'AttrModal"><i class="icon-edit"></i> Chỉnh sửa</button>
         ' . $inputData . '
         <input type="hidden" class="attr-data-index" value="' . $index . '" />
         <div class="modal fade" id="' . $this->getOption('prefix') . 'AttrModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
             <div class="modal-dialog" role="document">
                 <div class="modal-content">
                     <div class="modal-header">
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">&times;</span>
                         </button>
                         <h4 class="modal-title">' . $this->getOption('title') . '</h4>
                     </div>
                     <div class="modal-body">
                         <table class="table table-bordered">
                             <thead>
                             <tr>
                                ' . $th . '
                                <th>Thao tác</th>
                             </tr>
                             </thead>
                             <tbody>
                                ' . $dataTr . '
                             <tr>
                                ' . $input . '
                                <td></td>
                             </tr>
                             </tbody>
                         </table>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="attr-add btn btn-primary">Thêm</button>
                         <button type="button" class="btn btn-secondary" data-dismiss="modal">Thoát</button>
                     </div>
                 </div>
             </div>
         </div>';
    return $html;
  }

  public function getJavaScripts()
  {
    return array(
      'backend/VtWidgetAttributeTable.js'
    );
  }
}