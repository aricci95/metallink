$(document).ready(function() {
    $(".site").on('keyup', 'input.autocomplete', function(e) {
        var target = $(e.target);
        var root   = target.closest('span.autocomplete');
        var type   = root.attr('data-type');
        var url    = 'suggest/' + type + '/' + target.val();
        var showValue = target.attr('show-value');
        var addValue  = target.attr('add-value');
        var tableList = root.find('div');
        var list      = tableList.find('ul');
            
        if(target.val() != '') {
            list.html("");
            $.getJSON(url, {}, function(data) {
                if(data.length > 0) {
                    $.each(data, function(index, element) {
                        list.append('<li><a class="element" href="#" data-libel="' + element.libel + '" data-id="' + element.id + '">' + element.libel + ((showValue == 1) ? ' (' + element.value + ')' : '' )+ '</a></li>');
                    });
                    if(addValue) {
                        list.append('<li><hr/></li><li><a class="add" href="#">Ajouter à la liste</a></li>');
                    }
                    tableList.fadeIn();
                } else if(addValue) {
                    tableList.fadeIn();
                    list.append('<li><hr/></li><li><a class="add" href="#">Ajouter à la liste</b></li>');
                } else {
                    tableList.fadeOut(); 
                }
            });
        } else {
            tableList.fadeOut(); 
            list.html("");
            root.find('input[type="hidden"]').val('');
        }
    });

    $("ul.autocomplete").on('click', 'a.element', function(e) {
        e.preventDefault();
        var target      = $(e.target);
        var root        = target.closest('span.autocomplete');
        var tableList   = root.find('div.autocomplete');
        var input       = root.find('input[type="text"]');
        var hiddenInput = root.find('input[type="hidden"]');
        var hiddenInputId = target.attr('data-id');
        var inputVal      = target.attr('data-libel');
        
        tableList.find('ul').html("");
        tableList.hide();
        hiddenInput.val(hiddenInputId)
        input.val(inputVal);
    });

    $("ul.autocomplete").on('click', 'a.add', function(e) {
        e.preventDefault();
        var target      = $(e.target);
        var root        = target.closest('span.autocomplete');
        var type        = root.attr('data-type');
        var tableList   = root.find('div.autocomplete');
        var list        = tableList.find('ul');
        var input       = root.find('input[type="text"]');
        var hiddenInput = root.find('input[type="hidden"]');
        var inputVal      = target.attr('data-libel');
        
        $.post("suggest/add", { type : type, string : input.val() }, 
            function(insertId) {
                if(insertId == 500) {
                    $.gritter.add({
                        title: 'Erreur',
                        text:  'Une erreur est survenue',
                        class_name : 'gritter-err'
                    });
                } else {
                    $.gritter.add({
                        text:  'Element ajouté.',
                        class_name : 'gritter-ok'
                    });
                    hiddenInput.val(insertId);
                    tableList.hide(); 
                    list.html("");
                }
            }
        );
    });
});
