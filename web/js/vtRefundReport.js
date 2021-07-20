/**
 * Created by anhbhv on 16/07/2016.
 */
$(document).ready(function () {

    $('.refundType12').change(function() {
        if ($('.refundType12').val() != 1) {
            $('.hidden-row').addClass('hidden');
        } else {
            $('.hidden-row').removeClass('hidden');
        }
    });
    $('.refundType123').change(function() {
        if ($('.refundType123').val() != 1) {
            $('.hidden-row').addClass('hidden');
        } else {
            $('.hidden-row').removeClass('hidden');
        }
    });
    $('.btn-cancel-refund').on('click', function () {
        if (confirm("Bạn có chắc chắn muốn từ chối")) {
            var postData = $(this).serializeArray();
            var uri = $(this).attr('data-url');
            var id = $(this).attr('data-id');
            $.post(uri, {
                    post: postData,
                    id: id,
                },
                function (data) {
                    var json = JSON.parse(data);
                    if (json.error.length) {
                        alert(json.error);
                    } else {
                        location.reload();
                    }
                });
            return false;
        }
    });
	
	var isRefund = 0;
    $('.btnViewDetail').on('click', function () {

        $('.hidden-row').addClass('hidden');
        $('#createcontact .error').hide();
        var uri = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        var tran_id = $(this).attr('data-tran_id');
        var pay_code = $(this).attr('data-pay_code');
        if(pay_code == 'VNPAY'){
            $('.row_originalRequestId').addClass('hidden');
        }else{
            $('.row_originalRequestId').removeClass('hidden');
        }
        
        $('#modalViewDetail').modal();

        $('#createcontact').on('submit', function (e) {
			if(isRefund === 0){
				isRefund = 1;
				$('#createcontact .btn-primary').addClass('disabled');
				$('#createcontact .btn-primary').prop('disabled', true);
				// var postData = $(this).serializeArray();
				// var property = document.getElementById("fileUpload").files[0];
				var form = new FormData(this);
				form.append('id', id);
				form.append('tran_id', tran_id);
				e.preventDefault();

				$.ajax({
						type: 'POST',
						url: uri,
						enctype: 'multipart/form-data',
						data: form,
						processData: false,
						contentType: false,
						statusCode: {
							401: function(){
								location.reload();
								return false;
							}
						},
						complete: function (data) {
							isRefund = 0;
							try{
								var json = JSON.parse(data.responseText);
							}catch (e) {
								$('#createcontact .btn-primary').removeClass('disabled');
								$('#createcontact .btn-primary').prop('disabled', false);
								$('#createcontact .error').removeClass('alert alert-success').addClass('alert alert-danger');
								$('#createcontact .error').html('').html('Gửi hoàn tiền thất bại');
							}

							$('#createcontact .error').show();
								if (json.error.length) {
									$('#createcontact .btn-primary').removeClass('disabled');
									$('#createcontact .btn-primary').prop('disabled', false);
									$('#createcontact .error').removeClass('alert alert-success').addClass('alert alert-danger');
									$('#createcontact .error').html('').html(json.error);
								} else {
									$('#createcontact .error').removeClass('alert alert-danger').addClass('alert alert-success');
									$('#createcontact .error').html('').html('Gửi hoàn tiền thành công!.');
									$('#createcontact').trigger("reset");
									setTimeout(function () {
										location.reload();
									}, 3000);
								}


						},
						fail: function (data) {
							isRefund = 0;
							var json = JSON.parse(data);
							$('#createcontact .btn-primary').removeClass('disabled');
							$('#createcontact .btn-primary').prop('disabled', false);
							$('#createcontact .error').removeClass('alert alert-success').addClass('alert alert-danger');
							$('#createcontact .error').html('').html(json.error);
						}
					}
				);
				return false;
			}
        });
    });
    
    $('.btnViewPayment').on('click', function () {

        $('.hidden-row').addClass('hidden');
        $('#createcontact_2 .error').hide();
        var uri = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        var tran_id = $(this).attr('data-tran_id');


        $('#modalViewPayment').modal();

        $('#createcontact_2').on('submit', function (e) {
            $('#createcontact_2 .btn-primary').addClass('disabled');
            $('#createcontact_2 .btn-primary').prop('disabled', true);
            var form = new FormData(this);
            form.append('id', id);
            form.append('tran_id', tran_id);
            e.preventDefault();

            $.ajax({
                    type: 'POST',
                    url: uri,
                    enctype: 'multipart/form-data',
                    data: form,
                    processData: false,
                    contentType: false,
                    statusCode: {
                        401: function(){
                            location.reload();
                            return false;
                        }
                    },
                    complete: function (data) {
                        try{
                            var json = JSON.parse(data.responseText);
                        }catch (e) {
                            $('#createcontact_2 .btn-primary').removeClass('disabled');
                            $('#createcontact_2 .btn-primary').prop('disabled', false);
                            $('#createcontact_2 .error').removeClass('alert alert-success').addClass('alert alert-danger');
                            $('#createcontact_2 .error').html('').html('Gửi hoàn điểm thất bại');
                            // setTimeout(function () {
                            //     location.reload();
                            // }, 2000);
                        }

                        $('#createcontact_2 .error').show();
                        if (json.error.length) {
                            $('#createcontact_2 .btn-primary').removeClass('disabled');
                            $('#createcontact_2 .btn-primary').prop('disabled', false);
                            $('#createcontact_2 .error').removeClass('alert alert-success').addClass('alert alert-danger');
                            $('#createcontact_2 .error').html('').html(json.error);
                        } else {
                            $('#createcontact_2 .error').removeClass('alert alert-danger').addClass('alert alert-success');
                            $('#createcontact_2 .error').html('').html('Gửi hoàn điểm thành công!.');
                            $('#createcontact_2').trigger("reset");
                            // setTimeout(function () {
                            //     location.reload();
                            // }, 3000);
                        }


                    },
                    fail: function (data) {
                        var json = JSON.parse(data);
                        $('#createcontact_2 .btn-primary').removeClass('disabled');
                        $('#createcontact_2 .btn-primary').prop('disabled', false);
                        $('#createcontact_2 .error').removeClass('alert alert-success').addClass('alert alert-danger');
                        $('#createcontact_2 .error').html('').html(json.error);
                    }
                }
            );
            return false;
        });
    });
    
    $('#modalViewDetail').on('hidden.bs.modal', function () {
        $(this).find('#createcontact')[0].reset();
    });

    $('#modalViewPayment').on('hidden.bs.modal', function () {
        $(this).find('#createcontact_2')[0].reset();
    })

    $('.btnShowTrans').on('click', function () {
        $('#modalViewDetailTransaction').modal();
        $.ajax({
            url: $(this).attr('data-url'),
            type: 'POST',
            cache: false,
            data: {
                tran_id: $(this).attr('data-tran_id')
            },
            success: function(result){
                var data = JSON.parse(result);
                if (data.error.length) {
                    $('#ajaxListResult .error').removeClass('alert alert-success').addClass('alert alert-danger');
                    $('#ajaxListResult .error').html('').html(data.error);
                }else {
                    $('#ajaxListResult').html(data.template);
                }
            },
            error: function(request, status, err) {
                $('#modalViewDetail').modal('hide');
                checkRequestStatus(request)
            }
        });
    });
});