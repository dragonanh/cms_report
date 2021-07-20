<style>
    .header{
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        margin-bottom: 30px;
    }
    .header-import{
        display: flex;
        margin-left: 30px;
    }
    .import-success, .import-error{
        width: 300px;
        display: flex;
        justify-content: space-between;
        align-content: center;
        border: 1px solid black;
        border-radius: 5px;
        padding: 10px;
    }
    .import-success{
        margin-right: 20px;
        background: #62c462ad;
    }
    .import-error{
        background: #ee5f5b8f;
    }
    .p-title{
        display: flex;
        align-items: center;
        font-size: 25px;
        margin-bottom: 0px;
    }
    .title-table{
        text-align: center;
        margin-bottom: 20px;
    }
    .submit-import a{
        background: #0088cc;
        color: white;
        line-height: 27px;
        margin-right: 20px;
    }
    .btn-back{
        background: #ccc ! important;
        color: black ! important;
    }
</style>
<div class="sf_admin_list">
    <div class="header">
        <div class="header-import">
            <div class="import-success">
                <h3>Dữ liệu import hợp lệ</h3>
                <p class="p-title"><?php echo $countSuccess ?></p>
            </div>
            <div class="import-error">
                <h3>Dữ liệu import không hợp lệ</h3>
                <p class="p-title"><?php echo count($dataErrors) ?></p>
            </div>
        </div>
        <div class="submit-import">
            <a href="<?php echo url_for("@vt_area_VtAreaImport")?>" class=" btn btn-back">Quay lại</a>
            <a href="<?php echo url_for("@vt_vt_area_import_excel")?>" class="btn">Xác nhận import</a>
        </div>
    </div>
    <div class="body">
        <div class="title-table">
            <h3> Danh sách dữ liệu import không hợp lệ</h3>
        </div>
        <table class="datatable table table-bordered table-striped" id="table_vt_area_code" style="margin-top: 5px !important;">
            <thead>
            <tr>
                <th>STT</th>
                <th>Code</th>
                <th>Parent Code</th>
                <th>Name</th>
                <th>Fullname</th>
                <th>Province</th>
                <th>District</th>
                <th>Precinct</th>
                <th>Street block</th>
                <th>Street</th>
                <th>Province name</th>
                <th>District name</th>
                <th>Precinct name</th>
                <th>Error</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($dataErrors)) :?>
                <?php foreach($dataErrors as $key => $value) :?>
                    <tr>
                        <td><?php echo $key ?></td>
                        <td><?php echo $value['code']?></td>
                        <td><?php echo $value['parent_code']?></td>
                        <td><?php echo $value['name']?></td>
                        <td><?php echo $value['full_name'] ?></td>
                        <td><?php echo $value['province'] ?></td>
                        <td><?php echo $value['district'] ?></td>
                        <td><?php echo $value['precinct'] ?></td>
                        <td><?php echo $value['street_block'] ?></td>
                        <td><?php echo $value['street'] ?></td>
                        <td><?php echo $value['province_name'] ?></td>
                        <td><?php echo $value['district_name'] ?></td>
                        <td><?php echo $value['precinct_name'] ?></td>
                        <td>
                            <?php
                            $error = '';
                            $connect = "\n";
                            foreach($value['errors'] as $e){
                                $error .=  $e . '.'.  "\n";
                            }
                            echo $error;
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    /* <![CDATA[ */
    $(function(){

// add multiple select / deselect functionality
        $("#sf_admin_list_batch_checkbox").click(function () {
            $('.sf_admin_batch_checkbox').attr('checked', this.checked);
        });

// if all checkbox are selected, check the selectall checkbox
// and viceversa
        $(".sf_admin_batch_checkbox").click(function(){

            if($(".sf_admin_batch_checkbox").length == $(".sf_admin_batch_checkbox:checked").length) {
                $("#sf_admin_list_batch_checkbox").attr("checked", "checked");
            } else {
                $("#sf_admin_list_batch_checkbox").removeAttr("checked");
            }

        });
    });

    /* ]]> */
</script>