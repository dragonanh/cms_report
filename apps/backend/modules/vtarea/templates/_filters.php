
<div class="row-fluid">
    <div class="span9">
        <div class="control-group">
            <form action="<?php echo url_for('@vt_vt_area_confirm_import_excel') ?>" method="post" enctype="multipart/form-data">
                <div class="span11">
                    <?php echo $importForm->renderHiddenFields() ?>
                    <table>
                        <tr>
                            <td><b>Import excel <br><?php echo $importForm['file']->render() ?></td>
                            <td><br><button class="btn btn-warning" type="submit" name="_import"><i class="icon-upload icon-black"></i> Import </button></td>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($importForm->hasGlobalErrors()): ?>
                                    <?php echo $importForm->renderGlobalErrors() ?>
                                <?php endif; ?>
                                <?php echo $importForm['file']->hasError() ? $importForm['file']->renderError() : '' ?></td>
                            <td><?php echo $importForm['image']->hasError() ? $importForm['image']->renderError() : '' ?></td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="<?php echo url_for("@vt_vt_area_download_template")?>" class="btn">Tải file mẫu</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>

        </div>
    </div>
</div>
