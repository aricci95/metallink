$(document).ready(function() {
    $("#search_form").on('change', '#search_type', function(e) {
        $.post('search/criterias', {search_type : $('#search_type').val()},
            function(data) {
               $("#search_criterias").html(data);
            },
            'html'
        );
    });

    $("#search_form").on('change', 'select', function(e) {
        e.preventDefault();
        refresh();
    });

    $("#search_form").on('click', '#submit_button', function(e) {
        e.preventDefault();
        refresh();
    });

    function refresh() {
        $.post('search/getResults', {
                search_type : $('#search_type').val(),
                search_login : $('#search_login').val(),
                search_distance : $('#search_distance').val(),
                search_gender : $('#search_gender').val(),
                search_keyword : $('#search_keyword').val(),
                search_location : $('#search_location').val()
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
