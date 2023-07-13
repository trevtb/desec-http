$(document).ready(function() {
   $('#loginform').submit(function() {
       var pass = $('#login-password');
       var hash = $.sha1($(pass).val());
       $(pass).val(hash);
   });
});