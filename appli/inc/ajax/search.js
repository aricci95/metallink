$(document).ready(function() {
    $("#search_form").on('change', 'select', function(e) {
        e.preventDefault();
        refresh();
    });

    $("#search_form").on('click', '#submit_button', function(e) {
        e.preventDefault();
        refresh();
    });

    function refresh() {
        $.post($('.loading').attr('data-href') + '/getResults', {
                search_login : $('#search_login').val(),
                search_distance : $('#search_distance').val(),
                search_gender : $('#search_gender').val(),
                search_age : $('#search_age').val(),
                search_keyword : $('#search_keyword').val()
            },
            function(data) {
               tmp = $(data);
               loading = $(".loading");
               tmp.hide();
               $(".results").html(tmp);
               loading.attr('data-offset', 0);
               loading.attr('data-end', 'false');
               loading.attr('data-show', 'false');
               tmp.fadeIn();
            },
            'html'
        );
    }
});
