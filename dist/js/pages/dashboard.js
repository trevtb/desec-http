window.pollactive = true;
window.camwidget_atab = 'monitor';

function pollHook() {
    if (window.pollactive) {
        if (window.userwidget == '1') {
            userwidgetPollUsers();
        } //endif
        if (window.camwidget == '1' && window.camwidgetrefmode == 'poll') {
            reloadCamWidget();
        } //endif
        poll();
    } //endif
} //endfunction pollHook

function poll() {
    if (window.pollactive) {
        setTimeout(function () {
            $.ajax({
                url: "/poll-online", success: function (data) {
                }, dataType: "json", complete: pollHook
            });
        }, 30000);
    } //endif
} //endfunction poll

function userwidgetPollUsers() {
    camwidgetEnableSpinner();
    $.ajax({
        type: "GET",
        url: "/widget-users",
        data: "get=1",
        cache: false,
        async: true,
        success: function (rsp) {
            $('#userwidget .box-body').html(rsp);
            camwidgetDisableSpinner();
        }
    });
} //endfunction userwidgetPollUsers

function camwidgetSavePollMode() {
    var url = '';
    if (window.camwidgetrefmode == 'poll') {
        url = '/camwidget-polling-mode';
    } else {
        url = '/camwidget-live-mode';
    } //endif

    $.ajax({
        type: "GET",
        url: url,
        cache: false,
        async: true
    });
} //endfunction camwidgetSavePollMode

function camWidgetChangeCamId(id) {
    $('#camwidget-img').attr('data-id', id);
    $.ajax({
        type: "POST",
        url: '/camwidget-set-camid',
        data: 'id='+id,
        cache: false,
        async: true,
        success: function(rsp) {
            if (rsp == '0') {
                BootstrapDialog.show({
                    title: '<i class="fa fa-video-camera"></i> Kamera existiert nicht mehr',
                    message: 'Die ausgew&auml;hlte Kamera wurde gel&ouml;scht.',
                    buttons: [{
                        label: 'Ok',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            } //endif
            reloadCamWidget();
        }
    });
} //endfunction camWidgetChangeCamId

function reloadCamWidget() {
    camwidgetEnableSpinner();

    $.ajax({
        type: "GET",
        url: "/widget-camera",
        data: "get=1",
        cache: false,
        async: true,
        success: function (rsp) {
            $('#camerawidget_body').html(rsp);
            if (window.camwidget_atab == 'monitor') {
                $('#camwidget_settingstab').removeClass('active');
                $('#camwidget_monitortab').addClass('active');
                $('#camwidget_settings').removeClass('active');
                $('#camwidget_monitor').addClass('active');
            } else if (window.camwidget_atab == 'settings') {
                $('#camwidget_monitortab').removeClass('active');
                $('#camwidget_settingstab').addClass('active');
                $('#camwidget_monitor').removeClass('active');
                $('#camwidget_settings').addClass('active');
            } //endif
            camwidgetSetMode();
            createEventBindings('cam');
        }
    });
} //endfunction reloadCamWidget

function camwidgetEnableSpinner() {
    $('#camerawidget_reficon').addClass('fa-spin');
} //endfunction camwidgetEnableSpinner

function camwidgetDisableSpinner() {
    $('#camerawidget_reficon').removeClass('fa-spin');
} //endfunction camwidgetDisableSpinner

function camwidgetSetMode() {
    var id = $('#camwidget-img').attr("data-id");
    $.ajax({
        type: "POST",
        url: "/testcamcon",
        async: true,
        data: 'id='+id,
        success: function(response) {
            $('.camwidget_fails').hide();
            if (id == -1) {
                $('#camwidget-img').hide();
                $('#camwidget-nocamwarn').show();
                camwidgetDisableSpinner();
                return;
            } //endif
            if (response == 'login') {
                $('#camwidget-img').hide();
                $('#camwidget-loginwarn').show();
                camwidgetDisableSpinner();
                return;
            } else if (response == 'offline') {
                $('#camwidget-img').hide();
                $('#camwidget-offlinewarn').show();
                camwidgetDisableSpinner();
                return;
            } //endif
            if (!camwidgetCamIsCreated(id)) {
                $('#camwidget-img').hide();
                $('#camwidget-creatingwarn').show();
                camwidgetDisableSpinner();
                return;
            } //endif

            var camwidgetimg = $('#camwidget-img');
            $(camwidgetimg).show();
            if (window.camwidgetrefmode == 'live') {
                $(camwidgetimg).attr("src", "/cam/" + id).load(camwidgetDisableSpinner());
            } else if (window.camwidgetrefmode == 'poll') {
                $(camwidgetimg).attr("src", "/camframe-big/" + id + "/" + new Date().getTime()).load(camwidgetDisableSpinner());
            } //endif
        }
    });
} //endfunction camwidgetSetMode

function camwidgetCamIsCreated(id) {
    var response = $.ajax({
        type: "GET",
        url: "/cam-is-created/" + id,
        cache: false,
        async: false
    }).responseText;

    if(response == '1') {
        return true;
    } //endif
    return false;
} //endfunction camwidgetCamIsCreated

function reloadWidget(name) {
    window.pollactive = false;
    var unixtime_ms = new Date().getTime();
    while(new Date().getTime() < unixtime_ms + 100) {}

    if (name == 'cam') {
        updateAuthCredentials();
        $.ajax({
            type: "GET",
            url: "/cam/login",
            async: true,
            username: window.camuser,
            password: window.campass,
            data: 'camlogin=1'
        }).done(function() {
           reloadCamWidget();
        });
    } else if (name == 'user') {
        userwidgetPollUsers();
    } //endif

    window.pollactive = true;
} //endfunction reloadWidget

function createEventBindings(type) {
    if (type == 'cam') {
        $('#camwmodeRadio1').click(function() {
            window.camwidgetrefmode = 'live';
            camwidgetSavePollMode();
            reloadCamWidget();
        });

        $('#camwmodeRadio2').click(function() {
            window.camwidgetrefmode = 'poll';
            camwidgetSavePollMode();
            reloadCamWidget();
        });

        $('#camwidgetcamselect').change(function() {
            camWidgetChangeCamId($(this).val());
        });
    } //endif
} //endfunction createEventBindings

function setWidgetStatus(widget, status) {
    $.ajax({
        type: "POST",
        url: '/widget-setstatus',
        data: 'name='+widget+'&status='+status,
        cache: false,
        async: true
    });

    if (widget == 'users') {
        window.userwidget = status;
        if (status == '1') {
            $('#userwidget').show();
            startWidget('users');
        } else {
            stopWidget('users');
            $('#userwidget').hide();
        } //endif
    } else if (widget == 'cameras') {
        window.camwidget = status;
        if (status == '1') {
            $('#camerawidget').show();
            startWidget('cameras');
        } else {
            stopWidget('cameras');
            $('#camerawidget').hide();
        } //endif
    } //endif
} //endfunction setWidgetStatus

function startWidget(name) {
    if (name == 'users') {
        userwidgetPollUsers();
    } //endif

    if (name == 'cameras') {
        updateAuthCredentials();
        $.ajax({
            type: "GET",
            url: "/cam/login",
            async: true,
            username: window.camuser,
            password: window.campass,
            data: 'camlogin=1'
        }).done(function () {
            reloadCamWidget();
        });

        $('#camwidget_tabs li a').click(function() {
            var href = $(this).attr('href');
            if (href == '#camwidget_monitor') {
                window.camwidget_atab = 'monitor';
            } else if (href == '#camwidget_settings') {
                window.camwidget_atab = 'settings';
            } //endif
        });
    } //endif
} //endfunction startWidget

function stopWidget(name) {
    if (name == 'cameras') {
        $('#camwmodeRadio1').unbind();
        $('#camwmodeRadio2').unbind();
        $('#camwidgetcamselect').unbind();
        $('#camwidget_tabs li a').unbind();
    } //endif
} //endfunction stopWidget

$(document).ready(function() {
    if (window.userwidget == '1') {
        userwidgetPollUsers();
    } //endif

    poll();

    if (window.camwidget == '1') {
        updateAuthCredentials();
        $.ajax({
            type: "GET",
            url: "/cam/login",
            async: true,
            username: window.camuser,
            password: window.campass,
            data: 'camlogin=1'
        }).done(function () {
            reloadCamWidget();
        });

        $('#camwidget_tabs li a').click(function() {
            var href = $(this).attr('href');
            if (href == '#camwidget_monitor') {
                window.camwidget_atab = 'monitor';
            } else if (href == '#camwidget_settings') {
                window.camwidget_atab = 'settings';
            } //endif
        });
    } //endif

    $('.csbar_dashboard_cbox').change(function() {
        var name = $(this).attr('data-name');
        if ($(this).is(":checked")) {
            setWidgetStatus(name, 1);
        } else {
            setWidgetStatus(name, 0);
        } //endif
    });
});