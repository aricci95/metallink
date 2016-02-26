$(document).ready(function() {
    $(document).scroll(function() {
         $(window).scroll(function () {
            var loading     = $(".loading");
            var end         = (loading.attr('data-end') === "true");
            if(!end) {
                var ajaxProcess = (loading.attr('data-show') === "true");
                if(!ajaxProcess && ($(window).scrollTop() + $(window).height() >= ($(document).height() - 800))) {
                    loading.attr('data-show', 'true');
                    var page    = loading.attr('data-href');
                    var offset  = loading.attr('data-offset');
                    var option  = loading.attr('data-option');
                    var newOffset = parseInt(offset) + 1;
                    var url     = '';
                    if(option > 0) {
                        url = page + '/more/' + newOffset + '/' + option;
                    } else {
                        url = page + '/more/' + newOffset;
                    }
                    var results = $(".results");
                    $.get(
                        url, '',
                        function(data) {
                            if(data !== '') {
                                tmp = $(data);
                                tmp.hide();
                                results.append(tmp);
                                tmp.fadeIn();
                                loading.attr('data-offset', newOffset);
                                loading.attr('data-show', 'false');
                            } else {
                                loading.attr('data-end', true);
                            }
                        },
                        'html'
                    );
                }
            }
        })
    });
});
