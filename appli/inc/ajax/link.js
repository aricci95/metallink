$(document).ready(function() {
    $(".whiteBox").on('click', '.link', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var link = $(e.target);
        link.attr('src', 'images/icone/loading.gif');
        var divLink           = link.closest('.divLink');
        var destinataire      = link.closest('.linkDestinataire');
        var destinataireId    = destinataire.attr('data-destinataire-id');
        var destinatairePhoto = '';
        if(destinataire.attr('data-destinataire-photo') !== '') {
            destinatairePhoto = '/MLink/photos/small/'+destinataire.attr('data-destinataire-photo');
        }
        var destinataireLogin = destinataire.attr('data-destinataire-login');
        var destinataireMessage  = destinataire.attr('data-destinataire-mail');
        var status      = parseInt(link.attr('data-status'));
        var gritterType = 'info';
        var gritterMsg  = 'Demande envoyée';

        if(status === 6 && divLink.closest('.blacklist').length > 0) {
            gritterType = 'ok';
            gritterMsg  = 'Utilisateur débloqué';
            link.closest('.divElement').fadeOut();
        } else if(status === 7) {
            gritterType = 'ok';
            gritterMsg  = 'Demande validée';
            // Si page de gestion des links
            if(divLink.closest('.recieved').length > 0)  {
                link.closest('.divElement').fadeOut();
            }
        } else if(status === 8) {
            gritterType = 'err';
            gritterMsg  = 'Demande refusée';
            link.closest('.divElement').fadeOut();
        }

        $.post("link/link", { destinataire_id : destinataireId,
                            destinataire_photo_url : destinatairePhoto,
                            destinataire_login : destinataireLogin,
                            destinataire_mail : destinataireMessage,
                            status : status },
        function(data) {
            if(data == 500) {
                $.gritter.add({
                    title: 'Erreur',
                    text:  'Une erreur est survenue',
                    class_name : 'gritter-err'
                });
                link.hide();
            } else {
                $.gritter.add({
                    title: destinataireLogin,
                    text:  gritterMsg,
                    class_name : 'gritter-'+gritterType,
                    image : destinatairePhoto
                });

                divLink.html(data);
            }
        });
    });
});
