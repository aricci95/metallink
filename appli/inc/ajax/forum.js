$(document).ready(function() {
    $.fn.scrollTo = function( target, options, callback ){
        if(typeof options == 'function' && arguments.length == 2){ callback = options; options = target; }
        var settings = $.extend({
          scrollTarget  : target,
          offsetTop     : 50,
          duration      : 500,
          easing        : 'swing'
        }, options);
        return this.each(function(){
          var scrollPane = $(this);
          var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
          var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
          scrollPane.animate({scrollTop : scrollY }, parseInt(settings.duration), settings.easing, function(){
            if (typeof callback == 'function') { callback.call(this); }
          });
        });
    }

    $("#feed").scrollTo($("#end"));

    function resize() {
      var newSize = (($(document).height() + 320) / 3);
       $('#feed').css('height', newSize);
       $('#connectedUsers').css('height', newSize);
    }

    function refreshFeed() {
        var lastId     = $('.tmp').attr('data-id');
        var scrollBottom = false;

        $.post('forum/refreshFeed', {id : lastId},
            function(data) {
                if(data != 404) {
                   messages = $("ul.messages");
                   messages.append(data);

                   messages.find('li').each(function( index ) {
                     lastId = $( this ).attr('data-id');
                     scrollBottom = true;
                   });

                   $('.tmp').attr('data-id', lastId);

                   if(scrollBottom && $("#autoScroll").prop('checked')) {
                       $("#feed").scrollTo($("#end"));
                   }
                }
            },
            'html'
        );
    }

    function refreshUsers() {
        $.post('forum/refreshUsers', null,
            function(data) {
                if(data !== null) {
                   messages = $("ul.users");
                   console.log(data);
                   messages.html(data);
                }
            },
            'html'
        );
    }

    function send() {
        refreshFeed();
        var inputMessage = $('.message');
        var message = inputMessage.val();
        var login   = $('.tmp').attr('data-login');

        if($("#autoScroll").prop('checked')) {
          $("#feed").scrollTo($("#end"));
        }

        $.post('forum/save', { content : message },
            function(insertId) {
              $("#feed").find("ul.messages").append('<li data-id="' + insertId + '"><b>'+login+' :</b> '+message+'</li>');
              inputMessage.val('');
              $('.tmp').attr('data-id', insertId);
            },
            'html'
        );
    }

    $("#forum").on('click', '#notification', function(e) {
        var inputNotification = $('#notification');

        if (inputNotification.prop('checked')) {
            notification = 1;
        } else {
            notification = 0;
        }

        $.post('forum/setNotification', { notification : notification },
            null,
            'html'
        );
    });


    $( window ).resize(function() {
        resize();
    });

    $("#forum").on('click', '.send', function(e) {
        e.preventDefault();
        e.stopPropagation();
        send();
    });

    $(document).keypress(function(e) {
        if(e.which == 13 && $('#autoEnter').prop('checked')) {
            send();
        }
    });


    var refreshFeedIntervalId  = setInterval(refreshFeed, 8000);
    var refreshUsersIntervalId = setInterval(refreshUsers, 12000);
    resize();
});
