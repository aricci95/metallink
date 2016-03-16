$(document).ready(function() {

    var type = $('.autocomplete').attr('data-type');

    $('.autocomplete').autocomplete({
        source : function(requete, reponse) {
            $.ajax({
                url : 'MLink/appli/index.php?page=suggest&action=' + type,
                dataType : 'json',
                data : {
                    value : $('.autocomplete').val(),
                },
                success : function(donnee) {
                    reponse($.map(donnee, function(ville) {
                        return {value : ville.ville_nom_reel + ' (' + ville.ville_code_postal + ')', ville_id : ville.ville_id};
                    }));
                }
            });
        },
        select: function (event, ui) {
            console.log(ui.item);
            $('.autocompleteValue').val(ui.item.ville_id);
        }
    });
});
