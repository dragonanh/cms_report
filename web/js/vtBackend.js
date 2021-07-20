$(document).ready(function () {
    $('div.control-group input:text:not([readonly]):not([disabled])').eq(0).focus();
    $('div.sf_admin_form_row.error input:not([readonly]):not([disabled])').eq(0).focus();
    $('div.control-group.error input:not([readonly]):not([disabled])').eq(0).focus();

    $('.control-group h2').each(function (index, value) {
        var fieldError = $(this).siblings('fieldset').find('div.sf_admin_form_row.error').length;
        if(index != 0 && !fieldError){
            $(this).siblings('fieldset').hide();
        }
    });

    $('.control-group h2').on('click',function () {
        $(this).siblings('fieldset').slideToggle();
    });
});

function htmlEncode(value){
    //create a in-memory div, set it's inner text(which jQuery automatically encodes)
    //then grab the encoded contents back out.  The div never exists on the page.
    return $('<div/>').text(value).html();
}

function htmlDecode(value){
    return $('<div/>').html(value).text();
}

function checkRequestStatus(request){
    switch (request.status){
        case 401:
            alert('Phiên làm việc đã hết hạn vui lòng đăng nhập lại để tiếp tục.');
            window.location.reload(true);
            break
    }
}

function cutText(text){
    if (text.length > 30) {
        var ttext = text.substr(0, 30);
        var cutIndex = ttext.lastIndexOf(" ") <= 0 ? 30 : ttext.lastIndexOf(" ");
        ttext = ttext.substr(0, cutIndex)  + '...';
        return ttext;
    }
    return text;
}

function include(arr,obj) {
    return (arr.indexOf(obj) != -1);
}
