$(document).ready(function() {
    $('.create').click(function() {
        $('#covoitCreate').fadeIn();
    });

    $('.delete').click(function(e) {
        e.preventDefault();
        var target = $(e.target);
        var covoiturage = target.closest('tr.covoiturage');
        $.post("covoit/delete", { value : target.attr('data-id') }, 
        function(data) {
            if(data == 500) {
                $.gritter.add({
                    title: 'Erreur',
                    text:  'Une erreur est survenue',
                    class_name : 'gritter-err'
                });
            } else {
                $.gritter.add({
                    text:  'Covoiturage supprimé.',
                    class_name : 'gritter-ok'
                });
                covoiturage.fadeOut();
            }
        });
    });
});
