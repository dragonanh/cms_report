<?php

/**
 * Created by PhpStorm.
 * User: tiennx6
 * Date: 15/06/2016
 * Time: 11:04 SA
 */
class VtWidgetMultipleFileInput extends sfWidgetFormInputFile
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setOption('type', 'file');
    $this->setOption('needs_multipart', true);

    $this->addRequiredOption('file_src');
    $this->addOption('is_image', false);
    $this->addOption('edit_mode', true);
    $this->addOption('style', '');
    $this->addOption('action', '');
    $this->addOption('action_delete', '');
    $this->addOption('object_id', '');
//    $this->addOption('classified', true);
    $template = '
	      <table width="100%">
	        <tr>
	          <td align="left" style="overflow: hidden;">%file%</td>
	        </tr>
	        <tr>
            <td>
            <input type="checkbox" name="delete-file">
            <label for="delete-file">' . sfContext::getInstance()->getI18N()->__('Xóa ảnh cũ') . '</label>
            &nbsp;&nbsp;%radio%
            </td>
          </tr>
	        <tr>
	          <td>%input%</td>
	        </tr>
	      </table>
	    ';
    $this->addOption('template', $template);
    $this->configColors = VtColorTable::getInstance()->getAllColors();
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $input = '<span id="fileupload" data-url="' . url_for('@' . $this->getOption('action') . '?id=' . $this->getOption('object_id')) . '">
          <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
          <div class="fileupload-buttonbar">
              <div class="col-lg-7">
                  <!-- The fileinput-button span is used to style the file input field as button -->
                  <span class="btn btn-success fileinput-button">
                      <i class="glyphicon glyphicon-plus"></i>
                      <span>Add files...</span>
                      <input type="file" name="files[]" multiple>
                  </span>
                  <button type="submit" class="btn btn-primary start">
                      <i class="glyphicon glyphicon-upload"></i>
                      <span>Start upload</span>
                  </button>
                  <button type="reset" class="btn btn-warning cancel">
                      <i class="glyphicon glyphicon-ban-circle"></i>
                      <span>Cancel upload</span>
                  </button>
                  <!-- <button type="button" class="btn btn-danger delete">
                      <i class="glyphicon glyphicon-trash"></i>
                      <span>Delete</span>
                  </button>
                  <input type="checkbox" class="toggle"> -->
                  <!-- The global file processing state -->
                  <span class="fileupload-process"></span>
              </div>
              <!-- The global progress state -->
              <div class="col-lg-5 fileupload-progress fade">
                  <!-- The global progress bar -->
                  <div class="progress progress-animated active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                      <div class="progress-bar progress-bar-success" style="width:0%; height:100%;"></div>
                  </div>
                  <!-- The extended global progress state -->
                  <div class="progress-extended">&nbsp;</div>
              </div>
          </div>
          <!-- The table listing the files available for upload/download -->
          <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
        </span>
        <!-- The blueimp Gallery widget -->
        <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
            <div class="slides"></div>
            <h3 class="title"></h3>
            <a class="prev">‹</a>
            <a class="next">›</a>
            <a class="close">×</a>
            <a class="play-pause"></a>
            <ol class="indicator"></ol>
        </div>
        <!-- The template to display files available for upload -->
        <script id="template-upload" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-upload fade">
                <td>
                    <span class="preview"></span>
                </td>
                <td>
                    <p class="name">{%=file.name%}</p>
                    <strong class="error text-danger"></strong>
                </td>
                <td>
                    <p class="size">Processing...</p>
                    <div class="progress progress-animated active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%; height:100%;"></div></div>
                </td>
                <td>
                    {% if (!i && !o.options.autoUpload) { %}
                        <button class="btn btn-primary start" disabled>
                            <i class="glyphicon glyphicon-upload"></i>
                            <span>Start</span>
                        </button>
                    {% } %}
                    {% if (!i) { %}
                        <button class="btn btn-warning cancel">
                            <i class="glyphicon glyphicon-ban-circle"></i>
                            <span>Cancel</span>
                        </button>
                    {% } %}
                </td>
            </tr>
        {% } %}
        </script>
        <!-- The template to display files available for download -->
        <script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-download fade">
                <td>
                    <span class="preview">
                        {% if (file.thumbnailUrl) { %}
                            <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                        {% } %}
                    </span>
                </td>
                <td>
                    <p class="name">
                        {% if (file.url) { %}
                            <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?\'data-gallery\':\'\'%}>{%=file.name%}</a>
                        {% } else { %}
                            <span>{%=file.name%}</span>
                        {% } %}
                    </p>
                    {% if (file.error) { %}
                        <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                    {% } %}
                </td>
                <td>
                    <span class="size">{%=o.formatFileSize(file.size)%}</span>
                </td>
                <td>
                    {% if (file.deleteUrl) { %}
                        <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields=\'{"withCredentials":true}\'{% } %}>
                            <i class="glyphicon glyphicon-trash"></i>
                            <span>Delete</span>
                        </button>
                        <input type="checkbox" name="delete" value="1" class="toggle">
                    {% } else { %}
                        <button class="btn btn-warning cancel">
                            <i class="glyphicon glyphicon-ban-circle"></i>
                            <span>Cancel</span>
                        </button>
                    {% } %}
                </td>
            </tr>
        {% } %}
        </script>';

    $radio = '';
//    if ($this->getOption('classified')) {
    foreach ($this->configColors as $key => $value) {
      $checked = $key == 0 ? 'checked' : '';
      if($hexCode = $value->getHexCode()){
        $defaultName = '';
        $style = 'background-color: #' . $hexCode . ';width: 20px; height: 20px; border-radius: 10px; display: inline-block; margin-bottom: -10px; margin-left: 10px; border:solid 1px #ddd';
      }else{
        $defaultName = 'Mặc định';
        $style = 'display: inline-block; vertical-align: middle; margin-top: 3px; margin-left: 10px;';
      }

      $radio .= '<div title="' . VtHelper::encodeOutput($value->getName()) . '" style="'.$style.'">'.$defaultName.'</div>
      <input ' . $checked . ' name="file-color" type="radio" value="' . $value->getId() . '" />';
    }
//    }

    return strtr($this->getOption('template'), array('%input%' => $input, '%file%' => $this->getFileAsTag($attributes), '%radio%' => $radio));
  }

  protected function getFileAsTag($attributes)
  {
    if ($this->getOption('is_image') && $this->getOption('file_src')) {
      $html = '';
//      if ($this->getOption('classified')) {
      $colorArr = $this->getOption('file_src');
      $baseForm = new BaseForm();
      $csrfToken = $baseForm->getCSRFToken('delete-file');
      foreach ($colorArr as $key => $color) {
        $colorImages = $color->getImages();
        $fileArr = $colorImages ? json_decode(htmlspecialchars_decode($colorImages), true) : array();
        foreach ($fileArr as $file) {
          $params = array(
            'src' => $file,
            'width' => 100,
            'height' => 100,
          );
          if ($this->getOption('style')) {
            $params['style'] = $this->getOption('style');
          }
          $img = $this->renderTag('img', array_merge($params, $attributes));
          $vtColor = $color->getVtColor();
          $span = '<span>' . $img . '<a title="' . sfContext::getInstance()->getI18N()->__('Xóa') . '" class="btn-remove" data-url="' . url_for($this->getOption('action_delete'),
              array('id' => $this->getOption('object_id'), 'index' => $vtColor->getId(), 'image' => $file, '_csrf_token' => $csrfToken)) . '"><i class="icon-trash"></i></a></span>';
          $divStyle = 'display: inline-block; background-color: #%color%; width: 20px; height: 20px; margin: 0px 0px 32px -22px; border-radius: 9px;';
          $divStyle = str_replace('%color%', $vtColor->getHexCode(), $divStyle);
          $divParams = array(
            'style' => $divStyle
          );
          $div = $this->renderContentTag('div', null, $divParams);
          $html .= false !== $file ? '<div class="image-wrapper">' . $span . $div . '</div>' : '';
        }
      }
//      } else {
//        $fileArr = json_decode($this->getOption('file_src'));
//        foreach ($fileArr as $file) {
//          $params = array(
//            'src' => $file,
//            'width' => 100,
//            'height' => 100,
//          );
//          if ($this->getOption('style')) {
//            $params['style'] = $this->getOption('style');
//          }
//          $img = $this->renderTag('img', array_merge($params, $attributes));
//          $html .= false !== $file ? $img : '';
//        }
//      }
      return $html;
    } else {
      return $this->getOption('file_src');
    }
  }

  public function getJavascripts()
  {
    return array(
      'jquery-1.11.3.min.js',
      '/jQuery-File-Upload-9.12.5/js/vendor/jquery.ui.widget.js',
      'backend/MultipleUpload/tmpl.min.js',
      'backend/MultipleUpload/load-image.all.min.js',
      'backend/MultipleUpload/canvas-to-blob.min.js',
      'backend/MultipleUpload/bootstrap.min.js',
      'backend/MultipleUpload/jquery.blueimp-gallery.min.js',
      '/jQuery-File-Upload-9.12.5/js/jquery.iframe-transport.js',
      '/jQuery-File-Upload-9.12.5/js/jquery.fileupload.js',
      '/jQuery-File-Upload-9.12.5/js/jquery.fileupload-process.js',
      '/jQuery-File-Upload-9.12.5/js/jquery.fileupload-image.js',
      '/jQuery-File-Upload-9.12.5/js/jquery.fileupload-validate.js',
      '/jQuery-File-Upload-9.12.5/js/jquery.fileupload-ui.js',
      '/jQuery-File-Upload-9.12.5/js/main.js',
      'backend/VtWidgetMultipleFileInput.js'
    );
  }

  public function getStylesheets()
  {
    return array(
      '//blueimp.github.io/Gallery/css/blueimp-gallery.min.css' => '',
      '/jQuery-File-Upload-9.12.5/css/jquery.fileupload.css' => '',
      '/jQuery-File-Upload-9.12.5/css/jquery.fileupload-ui.css' => '',
      '/jQuery-File-Upload-9.12.5/css/style.css' => ''
    );
  }

}