$(document).keypress(
    function(event){
     if (event.which == '13') {
        event.preventDefault();
      }
});
// jQuery(document).ready(function($) {
//     $(function(){
//         $('#number_container').slideDown('fast');
        
//         $('.number').on('change',function(){
//             console.log('Change event.');
//             var val = $('.number').val();
//             $('#the_number').text( val !== '' ? val : '(empty)' );
//         });
//         $('.number').change(function(){
//             console.log('Second change event...');
//         });
//         $('.number').number( true, 0 );
//     });
// });
function showLoading(){
    bootbox.dialog({
        closeButton : false,
        message : '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Loading...</div>',
        className : "resize"
    });
}

function hideLoading(){
    bootbox.hideAll();
}
function myBad(menu){
    bootbox.dialog({
        closeButton : false,
        message: "<font>Please select a data!<font>",
        title: menu,
        buttons: {
            main: {
                label: "Ok",
                className: "btn-sm btn-primary",
                callback: function() {}
            }
        },
        className : "resize"
    });
}

function needValue(menu,msg){
    bootbox.dialog({
        closeButton : false,
        message: msg,
        title: menu,
        buttons: {
            main: {
                label: "Ok",
                className: "btn-sm btn-primary",
                callback: function() {}
            }
        },
        className : "resize"
    });
}

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    return x1 + x2;
}

function logout() {
    document.location.href = "system/control/logout.php";
}

$(function() {
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    $('#datepicker,#datepicker2,#datepicker3').datepicker({startDate: "today"});
    $('.datepicker').datepicker({
        format: "dd-mm-yy",
        todayHighlight: true
    });
    $('#datepicker-pastdisabled').datepicker({startDate: "today"});
    $('#datepicker-startview1').datepicker({startView: 1});
});