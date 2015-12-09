$(document).ready(function() {

	$("input:file").change(function () {
       $("#formPhoto").submit();
     });

	$(".newPhoto").click(function (e) {
		e.preventDefault();
       	$("input[name='new_photo']").click();
     });

	$(".photoCollection").on('click', '.editPhoto', function(e) {
		e.preventDefault();
		var photo = $(e.target);
		if(!photo.hasClass("profilePhoto")) {
			var photoId  = photo.closest('.editPhoto').attr('data-photo-id');
			var photoUrl = photo.closest('.editPhoto').attr('data-photo-url');
			var typeId   = $('input[name="type_id"]').val();
			var keyId    = $('input[name="key_id"]').val();
			var photoUrl = photo.closest('.editPhoto').attr('data-photo-url');

	       	$.post("photo/setProfilePhoto", { photo_id : photoId, photo_url : photoUrl, type_id : typeId, key_id : keyId}, function(data) {
			    $(".photoCollection").html(data);
			    $.gritter.add({
						text:  'Photo principale définie',
						class_name : 'gritter-ok'
					});
	        });
       }
     });

	$(".photoCollection").on('click', '.removePhoto', function(e) {
		e.stopPropagation();
		e.preventDefault();
		var photo    = $(e.target).closest('.editPhoto');
		var photoId  = photo.attr('data-photo-id');
		var photoUrl = photo.attr('data-photo-url');
		var typeId   = $('input[name="type_id"]').val();
		var keyId    = $('input[name="key_id"]').val();
		var errorMessage = $('.errorMessage');
		var newPhoto = $('.newPhoto');
		$.post("photo/removePhoto", { photo_id : photoId, photo_url : photoUrl, type_id : typeId, key_id : keyId }, function(data) {
			photo.fadeOut();
			errorMessage.fadeOut();
			newPhoto.fadeIn();
			$.gritter.add({
				text:  'Photo supprimmée',
				class_name : 'gritter-ok'
			});
		});
     });
});
