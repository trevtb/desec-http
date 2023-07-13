window.camuser = '';
window.campass = '';

function updateAuthCredentials() {
    $.ajax({
        type: "GET",
        url: "/get-camlogin",
        cache: false,
        async: false,
        success: function(rsp) {
            var resp = $.parseJSON(rsp);
            window.camuser = resp.user;
            window.campass = resp.pass;
        }
    });
} //endfunction updateAuthCredentials

$(document).ready(function() {
   $('#sidebar-monitor-link').click(function(ev) {
       ev.preventDefault();
       updateAuthCredentials();
       $.ajax
       ({
           type: "GET",
           url: "/cam/login",
           async: false,
           username: window.camuser,
           password: window.campass,
           data: 'camlogin=1'
       }).done(function() {
           window.location = '/cam/monitor';
       });
   });
});