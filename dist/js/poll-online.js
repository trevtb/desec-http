function poll() {
    setTimeout(function() {
        $.ajax({url: "/poll-online", success: function(data) {
        }, dataType: "json", complete: poll});
    }, 30000);
} //endfunction poll

$(document).ready(function() {
    $.ajax({url: "/poll-online", success: function(data) {
    }, dataType: "json", complete: poll});
});