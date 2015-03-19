/**
 * Created by elluminate on 18.03.15.
 */
$(function() {
    $('.vertical-progress_bar').each(function() {
            var $this = $(this);
            $this.animate({
                height: $this.attr('data-value')
            }, 300);
        }
    );
});

