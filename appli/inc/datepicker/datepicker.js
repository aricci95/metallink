$(document).ready(function() {
    var timepicker = ($('#datetimepicker').attr('timepicker') === 'true') ? true : false;
    var format     = $('#datetimepicker').attr('format');
    $('.datetimepicker').datetimepicker({
                                         lang:'fr',
                                         timepicker: timepicker,
                                         format: format
                                        });
        
    $('.datetimepicker').click(function(e){
        $(e.target).datetimepicker('show'); //support hide,show and destroy command
    });
});