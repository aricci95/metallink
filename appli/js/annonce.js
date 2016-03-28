$(document).ready(function() {
    $('#new').click(function(e) {
        e.preventDefault();
        $('#new').hide();
        $('#form').fadeIn();
    });

    $('#close').click(function(e) {
        e.preventDefault();
        $('#form').hide();
        $('#new').fadeIn();
    });
});
