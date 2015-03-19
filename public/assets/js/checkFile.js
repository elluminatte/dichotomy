/**
 * Created by elluminate on 22.02.15.
 */
$( document ).ready(function() {
   $(":file").change(function() {
       var file;
       file = this.files[0];
       $.ajax({
           url: '/test',
           type: 'POST',
           success: function(data){
               alert(data);
           }
       });
   });
});
