/**
 * Created by elluminate on 24.02.15.
 */
$( document ).ready(function() {
    $("#eval_form_collapse").click(function() {
        var className;
        className = $("#eval_form").attr('class');
        if(className == 'collapse') {
            $("#eval_form").removeClass();
            $("#eval_form_collapse").text('Скрыть значения параметров');
        }
        else {
            $("#eval_form").addClass('collapse');
            $("#eval_form_collapse").text('Показать значения параметров');
        }
    });
});
