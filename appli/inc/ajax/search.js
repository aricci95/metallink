$(document).ready(function() {
    $("#search_form").on('click', '#search_type', function(e) {
        var type = 'user';

        $.post('search/criterias', {type : type},
            function(data) {
               $("#search_criterias").html(data);
            },
            'html'
        );
    });

    $("#search_form").on('click', '#submit_button', function(e) {
        e.preventDefault();

        var type = 'user';

        $.post('search/getResults', {
                type : type,
                search_login : $('#search_login').val(),
                search_distance : $('#search_distance').val(),
                search_gender : $('#search_gender').val()
            },
            function(data) {
               tmp = $(data);
               tmp.hide();
               $(".results").html(tmp);
               tmp.fadeIn();
            },
            'html'
        );
    });
});
