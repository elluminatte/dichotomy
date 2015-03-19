/**
 * Created by elluminate on 23.01.15.
 */
$( document ).ready(function() {
    $('#delModal').on('show.bs.modal', function(e) {
        $(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
    });
});


