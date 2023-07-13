window.formsubmit = 0;
var bootstrapValAdd = 0;
var bootstrapValChange = 0;
window.cam_name = '';
window.group_name = '';

function indicateLoading(status) {
    if (status) {
        $('#span_created').hide();
        $('#span_creating').show();
    } else {
        $('#span_creating').hide();
        $('#span_created').show();
        updateAuthCredentials();
    } //endif
} //endfunction indicateLoading

function poll() {
    setTimeout(function() {
        $.ajax({url: "/poll-online", success: function(data) {
        }, dataType: "json", complete: pollCreation});
    }, 30000);
} //endfunction poll

function pollCreation() {
    $.ajax({
        type: "GET",
        url: "/check-creation",
        data: "get=1",
        cache: false,
        async: true,
        success: function (rsp) {
            if (rsp == '1') {
                indicateLoading(true);
            } else {
                indicateLoading(false);
            } //endif
            poll();
        }
    });
} //endfunction pollCreation

function updateCamGroups() {
    $.ajax({
        type: "GET",
        url: "/groupmanager-datasource-light",
        cache: false,
        async: true,
        success: function(rsp) {
            var resp = $.parseJSON(rsp);
            var html = '';
            for (var i=0; i<resp.length; i++) {
                html += '<label>';
                html += '<input type="checkbox" id="cccbox-'+resp[i].id+'" name="ccchangegroups[]" class="ccchangegroups" value="'+resp[i].id+'" />';
                html += '&nbsp;'+resp[i].name;
                html += '</label>&nbsp;&nbsp;';
            } //endfor
            if (resp.length == 0) {
                html += '<label style="font-weight: normal;"><i class="fa fa-info-circle"></i>&nbsp;&nbsp;Es existieren noch keine Gruppen.<br />Die Verwendung ist optional. Die Verwaltung erfolgt &uuml;ber den Gruppenreiter der Kameraverwaltung.</label>';
            } //endif
            $('#ccgroupsdiv').html(html);

            html = '';
            for (var j=0; j<resp.length; j++) {
                html += '<label>';
                html += '<input type="checkbox" name="caaddgroups[]" class="caaddgroups" value="'+resp[j].id+'" />';
                html += '&nbsp;'+resp[j].name;
                html += '</label>&nbsp;&nbsp;';
            } //endfor
            if (resp.length == 0) {
                html += '<label style="font-weight: normal;"><i class="fa fa-info-circle"></i>&nbsp;&nbsp;Es existieren noch keine Gruppen.<br />Die Verwendung ist optional. Die Verwaltung erfolgt &uuml;ber den Gruppenreiter der Kameraverwaltung.</label>';
            } //endif
            $('#cagroupsdiv').html(html);
        }
    });

    $.ajax({
        type: "GET",
        url: "/cammanager-datasource-light",
        cache: false,
        async: true,
        success: function(rsp) {
            var resp = $.parseJSON(rsp);
            var html = '';
            for (var i=0; i<resp.length; i++) {
                html += '<label>';
                html += '<input type="checkbox" id="grcbox-'+resp[i].id+'" name="grchangecams[]" class="grchangecams" value="'+resp[i].id+'" />';
                html += '&nbsp;'+resp[i].name;
                html += '</label>&nbsp;&nbsp;';
            } //endfor
            if (resp.length == 0) {
                html += '<label style="font-weight: normal;"><i class="fa fa-info-circle"></i>&nbsp;&nbsp;Sie haben noch keine Kameras hinzugef&uuml;gt.<br />Legen Sie diese &uuml;ber den Reiter "Ger&auml;te" der Kameraverwaltung an.</label>';
            } //endif
            $('#grchangecamsdiv').html(html);

            html = '';
            for (var j=0; j<resp.length; j++) {
                html += '<label>';
                html += '<input type="checkbox" name="graddcams[]" class="graddcams" value="'+resp[j].id+'" />';
                html += '&nbsp;'+resp[j].name;
                html += '</label>&nbsp;&nbsp;';
            } //endfor
            if (resp.length == 0) {
                html += '<label style="font-weight: normal;"><i class="fa fa-info-circle"></i>&nbsp;&nbsp;Sie haben noch keine Kameras hinzugef&uuml;gt.<br />Legen Sie diese &uuml;ber den Reiter "Ger&auml;te" der Kameraverwaltung an.</label>';
            } //endif
            $('#graddcamsdiv').html(html);
        }
    });
} //endfunction updateCamGroups

function addGroup() {
    window.formsubmit = 0;

    $('#graddname').val('');
    $('.graddcams').prop('checked', false);
    $('#addgroup-form').data('bootstrapValidator').resetForm();

    $('#addgroup-modal').modal('show');
} //endfunction addGroup

function addCam() {
    window.formsubmit = 0;

    $('#caname').val('');
    $('#cahost').val('');
    $('#caport').val('');
    $('#capath').val('');
    $('#cassl').prop('checked', false);
    $('#caauth').prop('checked', false);
    var causer = $('#causer');
    var capass = $('#capassword');
    $(causer).val('');
    $(capass).val('');
    $(causer).attr("disabled", "disabled");
    $(capass).attr("disabled", "disabled");
    $("#caresolution option[value='640x480']").attr('selected', true);
    $("#catype option[value='desec']").attr('selected', true);
    $('#addcam-form').data('bootstrapValidator').resetForm();

    $("#causerlabel").css({"opacity":"0.5"});
    $("#capasswordlabel").css({"opacity":"0.5"});
    bootstrapValAdd.enableFieldValidators('causer', false);
    bootstrapValAdd.enableFieldValidators('capassword', false);

    $('#addcam-modal').modal('show');
} //endfunction addCam

function changeGroupData(id) {
    $('.grchangecams').prop('checked', false);
    $('#grchangeid').val(id);
    window.formsubmit = 0;

    $.ajax({
        type: "POST",
        url: "/group-by-id",
        data: "id="+id,
        cache: false,
        async: true,
        success: function(response) {
            var group = $.parseJSON(response);
            $('#grchangename').val(group['name']);
            window.group_name = group['name'];

            $.ajax({
                type: "POST",
                url: "/groupmembers-by-gid",
                data: 'id='+id,
                cache: false,
                async: true,
                success: function(rsp) {
                    var resp = $.parseJSON(rsp);
                    for (var i=0; i<resp.length; i++) {
                        $("#grcbox-"+resp[i].camid).prop('checked', true);
                    } //endfor
                }
            });
        }
    });

    $('#changegroup-form').data('bootstrapValidator').resetForm();
    $('#changegroup-modal').modal('show');
} //endfunction changeCamData

function changeCamData(id) {
    $('.ccchangegroups').prop('checked', false);
    window.formsubmit = 0;
    $.ajax({
        type: "POST",
        url: "/cam-by-id",
        data: 'id='+id,
        cache: false,
        async: true,
        success: function(rsp) {
            var resp = $.parseJSON(rsp);

            $('#changecam-form').data('bootstrapValidator').resetForm();
            $('#ccid').val(resp['id']);
            $('#ccname').val(resp['name']);
            $('#cchost').val(resp['host']);
            $('#ccport').val(resp['port']);
            $('#ccpath').val(resp['path']);
            $('#ccresolution').val(resp['resolution']);
            $('#cctype').val(resp['type']);

            if (resp['ssl'] == '1') {
                $('#ccssl').prop('checked', true);
            } else {
                $('#ccssl').prop('checked', false);
            } //endif

            var ccuser = $('#ccuser');
            var ccpass = $('#ccpassword');
            if (resp['auth'] == '1') {
                $('#ccauth').prop('checked', true);
                $(ccuser).removeAttr("disabled");
                $(ccpass).removeAttr("disabled");
                $("#ccuserlabel").css({"opacity":"1.0"});
                $("#ccpasswordlabel").css({"opacity":"1.0"});
                $(ccuser).val(resp['user']);
                $(ccpass).val(resp['password']);
            } else {
                $('#ccauth').prop('checked', false);
                $(ccuser).attr("disabled", "disabled");
                $(ccpass).attr("disabled", "disabled");
                $("#ccuserlabel").css({"opacity":"0.5"});
                $("#ccpasswordlabel").css({"opacity":"0.5"});
                $(ccuser).val('');
                $(ccpass).val('');
            } //endif

            window.cam_name = resp['name'];

            $.ajax({
                type: "POST",
                url: "/groupmembers-by-cid",
                data: 'id='+id,
                cache: false,
                async: true,
                success: function(rsp) {
                    var resp = $.parseJSON(rsp);
                    for (var i=0; i<resp.length; i++) {
                        $("#cccbox-"+resp[i].groupid).prop('checked', true);
                    } //endfor
                }
            });
        }
    });

    $('#changecam-modal').modal('show');
} //endfunction changeCamData

function deleteGroup(id) {
    window.formsubmit = 0;
    $('#grdelid').val(id);
    $('#deletegroup-modal').modal('show');
} //endfunction deleteGroup

function deleteCam(id) {
    window.formsubmit = 0;
    $('#cdid').val(id);
    $('#deletecam-modal').modal('show');
} //endfunction deleteCam

$(document).ready(function() {
    updateCamGroups();
    poll();

    $("#cameratable").dataTable({
        "language": {
            "url": "/plugins/datatables/dataTables.german.lang"
        },
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "/cammanager-datasource",
            type: "POST"
        },
        "columns": [
            { "data": "id" },
            { "data": "name" },
            { "data": "type" },
            { "data": "host" },
            { "data": "port" },
            { "data": "path" },
            { "data": "ssl" },
            { "data": "auth" },
            { "data": "id" }
        ],
        "columnDefs": [
            {
                "className": "never",
                "targets": [0],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [2],
                "visible": true,
                "searchable": false,
                "render": function (data, type, row) {
                    if (data == 'desec') {
                        return 'DESEC C-UNIT';
                    } //endif
                    return 'IP Kamera';
                }
            },
            {
                "targets": [6],
                "visible": true,
                "searchable": false,
                "render": function (data, type, row) {
                    if (data == '1') {
                        return 'Ja';
                    } //endif
                    return 'Nein';
                }
            },
            {
                "targets": [7],
                "visible": true,
                "searchable": false,
                "render": function (data, type, row) {
                    if (data == '1') {
                        return 'Ja';
                    } //endif
                    return 'Nein';
                }
            },
            {
                "targets": [8],
                "searchable": false,
                "render": function (data, type, row) {
                    var html = '';
                    html += '<a class="camtabaction actiondel" href="javascript: deleteCam('+data+');" title="Kamera l&ouml;schen"><i class="fa fa-trash"></i></a>' +
                    '&nbsp;&nbsp;' +
                    '<a class="camtabaction actiondata" href="javascript: changeCamData('+data+');" title="Kameraeinstellungen bearbeiten"><i class="fa fa-edit"></i></a>';

                    return html
                }
            }
        ]
    });

    $("#grouptable").dataTable({
        "language": {
            "url": "/plugins/datatables/dataTables.german.lang"
        },
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "/groupmanager-datasource",
            type: "POST"
        },
        "columns": [
            { "data": "id" },
            { "data": "name" },
            { "data": "cams"},
            { "data": "id" }
        ],
        "columnDefs": [
            {
                "className": "never",
                "targets": [0],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [3],
                "searchable": false,
                "render": function (dat, type, row) {
                    var html = '';
                    html += '<a class="grouptabaction actiondel" href="javascript: deleteGroup('+dat+');" title="Gruppe l&ouml;schen"><i class="fa fa-trash"></i></a>' +
                    '&nbsp;&nbsp;' +
                    '<a class="grouptabaction actiondata" href="javascript: changeGroupData('+dat+');" title="Gruppe bearbeiten"><i class="fa fa-edit"></i></a>';

                    return html
                }
            }
        ]
    });

    $('#cd-btn').click(function() {
        $.ajax({
            type: "POST",
            url: "/cammanager-delete",
            data: "id=" + $('#cdid').val(),
            cache: false,
            async: true,
            success: function(rsp) {
                setTimeout($('#cameratable').DataTable().ajax.reload(), 10);
                setTimeout($('#grouptable').DataTable().ajax.reload(), 10);
            }
        });
        $('#deletecam-modal').modal('hide');
        indicateLoading(true);
        updateCamGroups();
    });

    $('#grdel-btn').click(function() {
        $.ajax({
            type: "POST",
            url: "/groupmanager-delete",
            data: "id=" + $('#grdelid').val(),
            cache: false,
            async: true,
            success: function(rsp) {
                setTimeout($('#grouptable').DataTable().ajax.reload(), 10);
            }
        });
        $('#deletegroup-modal').modal('hide');
        updateCamGroups();
    });

    $('#addgroup-form').bootstrapValidator({
        message: 'Ungültiger Wert',
        feedbackIcons: {
            valid: 'fa fa-ok',
            invalid: 'fa fa-remove',
            validating: 'fa fa-refresh'
        },
        live: 'enabled',
        submitButtons: '#gradd-btn',
        submitHandler: function(validator, form, submitButton) {
            if (!window.formsubmit) {
                $.ajax({
                    type: "POST",
                    url: "/groupmanager-add",
                    data: $('#addgroup-form').serialize(),
                    cache: false,
                    async: true,
                    success: function (rsp) {
                        setTimeout($('#grouptable').DataTable().ajax.reload(), 10);
                    }
                });
                window.formsubmit = 1;
                $('#addgroup-modal').modal('hide');
                updateCamGroups();
            } //endif
        },
        fields: {
            graddname: {
                message: 'Bitte vergeben Sie einen Namen für die Gruppe.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen für die Gruppe einen Namen vergeben.'
                    },
                    stringLength: {
                        min: 1,
                        max: 32,
                        message: 'Der Name muss zwischen 1 und 32 Zeichen lang sein.'
                    },
                    callback: {
                        message: 'Der Name ist bereits vergeben.',
                        callback: function (value, validator) {
                            var groupname = encodeURIComponent($('#graddname').val());
                            var response = $.ajax({
                                type: "GET",
                                url: "/groupname-exists/" + groupname,
                                cache: false,
                                async: false
                            }).responseText;
                            if (response == '1') {
                                return false;
                            } //endif
                            return true;
                        }
                    }
                }
            }
        }
    });

    var addcamform = $('#addcam-form');
    $(addcamform).bootstrapValidator({
        message: 'Ungültiger Wert',
        feedbackIcons: {
            valid: 'fa fa-ok',
            invalid: 'fa fa-remove',
            validating: 'fa fa-refresh'
        },
        live: 'enabled',
        submitButtons: '#ca-btn',
        submitHandler: function(validator, form, submitButton) {
            if (!window.formsubmit) {
                $.ajax({
                    type: "POST",
                    url: "/cammanager-add",
                    data: $('#addcam-form').serialize(),
                    cache: false,
                    async: true,
                    success: function (rsp) {
                        setTimeout($('#cameratable').DataTable().ajax.reload(), 10);
                        setTimeout($('#grouptable').DataTable().ajax.reload(), 10);
                    }
                });
                window.formsubmit = 1;
                $('#addcam-modal').modal('hide');
                indicateLoading(true);
                updateCamGroups();
            } //endif
        },
        fields: {
            caname: {
                message: 'Bitte vergeben Sie einen Namen für die Kamera.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen für die Kamera einen Namen vergeben.'
                    },
                    stringLength: {
                        min: 1,
                        max: 32,
                        message: 'Der Name muss zwischen 1 und 32 Zeichen lang sein.'
                    },
                    callback: {
                        message: 'Der Name ist bereits vergeben.',
                        callback: function (value, validator) {
                            var camname = encodeURIComponent($('#caname').val());
                            var response = $.ajax({
                                type: "GET",
                                url: "/camname-exists/" + camname,
                                cache: false,
                                async: false
                            }).responseText;
                            if (response == '1') {
                                return false;
                            } //endif
                            return true;
                        }
                    }
                }
            },
            cahost: {
                message: 'Bitte geben Sie einen Hostnamen oder eine IP-Adresse ein.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen einen Hostnamen oder eine IP-Adresse eingeben.'
                    },
                    stringLength: {
                        min: 1,
                        max: 32,
                        message: 'Der Hostname muss zwischen 1 und 32 Zeichen lang sein.'
                    }
                }
            },
            caport: {
                validators: {
                    notEmpty: {
                        message: 'Sie müssen einen TCP/IP Port angeben.'
                    },
                    callback: {
                        message: 'Dies ist kein gültiger TCP/IP Port.',
                        callback: function(value, validator) {
                            var port = $('#caport').val();
                            if (!$.isNumeric(port) || parseInt(port) < 1 || parseInt(port) > 65535) {
                                return false;
                            } //endif
                            return true;
                        }
                    }
                }
            },
            capath: {
                message: 'Bitte geben den Pfad zum Kamera Stream an.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen einen Pfad zum Kamera Stream angeben.'
                    },
                    stringLength: {
                        min: 3,
                        max: 32,
                        message: 'Der Pfad muss zwischen 1 und 32 Zeichen lang sein.'
                    }
                }
            },
            causer: {
                message: 'Bitte geben Sie einen Benutzernamen ein.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen einen Benutzernamen eingeben.'
                    },
                    stringLength: {
                        min: 3,
                        max: 32,
                        message: 'Der Benutzername muss zwischen 3 und 32 Zeichen lang sein.'
                    }
                }
            },
            capassword: {
                message: 'Sie müssen ein Passwort eingeben.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen ein Passwort eingeben.'
                    },
                    stringLength: {
                        min: 6,
                        max: 32,
                        message: 'Das Passwort muss zwischen 6 und 16 Zeichen lang sein.'
                    }
                }
            }
        }
    });

    bootstrapValAdd = $(addcamform).data('bootstrapValidator');

    $('#caauth').change(function() {
        if ($(this).prop('checked')) {
            $('#causer').removeAttr("disabled");
            $('#capassword').removeAttr("disabled");
            $("#causerlabel").css({"opacity":"1.0"});
            $("#capasswordlabel").css({"opacity":"1.0"});
            bootstrapValAdd.enableFieldValidators('causer', true);
            bootstrapValAdd.enableFieldValidators('capassword', true);
            bootstrapValAdd.validateField('causer');
            bootstrapValAdd.validateField('capassword');
        } else {
            var causer = $('#causer');
            var capass = $('#capassword');
            $(causer).val('');
            $(capass).val('');
            $(causer).attr("disabled", "disabled");
            $(capass).attr("disabled", "disabled");
            $("#causerlabel").css({"opacity":"0.5"});
            $("#capasswordlabel").css({"opacity":"0.5"});
            bootstrapValAdd.enableFieldValidators('causer', false);
            bootstrapValAdd.enableFieldValidators('capassword', false);
        } //endif
    });

    $('#changegroup-form').bootstrapValidator({
        message: 'Ungültiger Wert',
        feedbackIcons: {
            valid: 'fa fa-ok',
            invalid: 'fa fa-remove',
            validating: 'fa fa-refresh'
        },
        live: 'enabled',
        submitButtons: '#grchange-btn',
        submitHandler: function(validator, form, submitButton) {
            if (!window.formsubmit) {
                $.ajax({
                    type: "POST",
                    url: "/groupmanager-edit",
                    data: $('#changegroup-form').serialize(),
                    cache: false,
                    async: true,
                    success: function (rsp) {
                        setTimeout($('#grouptable').DataTable().ajax.reload(), 10);
                    }
                });
                window.formsubmit = 1;
                $('#changegroup-modal').modal('hide');
                updateCamGroups();
            } //endif
        },
        fields: {
            grchangename: {
                message: 'Bitte vergeben Sie einen Namen für die Gruppe.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen für die Gruppe einen Namen vergeben.'
                    },
                    stringLength: {
                        min: 1,
                        max: 32,
                        message: 'Der Name muss zwischen 1 und 32 Zeichen lang sein.'
                    },
                    callback: {
                        message: 'Der Name ist bereits vergeben.',
                        callback: function (value, validator) {
                            var groupname = encodeURIComponent($('#graddname').val());
                            if (encodeURIComponent(window.group_name) != groupname) {
                                var response = $.ajax({
                                    type: "GET",
                                    url: "/groupname-exists/" + groupname,
                                    cache: false,
                                    async: false
                                }).responseText;
                                if (response == '1') {
                                    return false;
                                } //endif
                            } //endif
                            return true;
                        }
                    }
                }
            }
        }
    });

    var changecamform = $('#changecam-form');
    $(changecamform).bootstrapValidator({
        message: 'Ungültiger Wert',
        feedbackIcons: {
            valid: 'fa fa-ok',
            invalid: 'fa fa-remove',
            validating: 'fa fa-refresh'
        },
        live: 'enabled',
        submitButtons: '#cc-btn',
        submitHandler: function(validator, form, submitButton) {
            if (!window.formsubmit) {
                $.ajax({
                    type: "POST",
                    url: "/cammanager-edit",
                    data: $('#changecam-form').serialize(),
                    cache: false,
                    async: true,
                    success: function (rsp) {
                        if (rsp == '1') {
                            indicateLoading(true);
                        } //endif
                        setTimeout($('#cameratable').DataTable().ajax.reload(), 10);
                        setTimeout($('#grouptable').DataTable().ajax.reload(), 10);
                    }
                });
                $('#changecam-modal').modal('hide');
                window.formsubmit = 1;
                updateCamGroups();
            } //endif
        },
        fields: {
            ccname: {
                message: 'Bitte vergeben Sie einen Namen für die Kamera.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen für die Kamera einen Namen vergeben.'
                    },
                    stringLength: {
                        min: 1,
                        max: 32,
                        message: 'Der Name muss zwischen 1 und 32 Zeichen lang sein.'
                    },
                    callback: {
                        message: 'Der Name ist bereits vergeben.',
                        callback: function (value, validator) {
                            var camname = encodeURIComponent($('#ccname').val());
                            if (encodeURIComponent(window.cam_name) != camname) {
                                var response = $.ajax({
                                    type: "GET",
                                    url: "/camname-exists/" + camname,
                                    cache: false,
                                    async: false
                                }).responseText;
                                if (response == '1') {
                                    return false;
                                } //endif
                            } //endif
                            return true;
                        }
                    }
                }
            },
            cchost: {
                message: 'Bitte geben Sie einen Hostnamen oder eine IP-Adresse ein.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen einen Hostnamen oder eine IP-Adresse eingeben.'
                    },
                    stringLength: {
                        min: 1,
                        max: 32,
                        message: 'Der Hostname muss zwischen 1 und 32 Zeichen lang sein.'
                    }
                }
            },
            ccport: {
                validators: {
                    notEmpty: {
                        message: 'Sie müssen einen TCP/IP Port angeben.'
                    },
                    callback: {
                        message: 'Dies ist kein gültiger TCP/IP Port.',
                        callback: function(value, validator) {
                            var port = $('#ccport').val();
                            if (!$.isNumeric(port) || parseInt(port) < 1 || parseInt(port) > 65535) {
                                return false;
                            } //endif
                            return true;
                        }
                    }
                }
            },
            ccpath: {
                message: 'Bitte geben den Pfad zum Kamera Stream an.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen einen Pfad zum Kamera Stream angeben.'
                    },
                    stringLength: {
                        min: 3,
                        max: 32,
                        message: 'Der Pfad muss zwischen 1 und 32 Zeichen lang sein.'
                    }
                }
            },
            ccuser: {
                message: 'Bitte geben Sie einen Benutzernamen ein.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen einen Benutzernamen eingeben.'
                    },
                    stringLength: {
                        min: 3,
                        max: 32,
                        message: 'Der Benutzername muss zwischen 3 und 32 Zeichen lang sein.'
                    }
                }
            },
            ccpassword: {
                message: 'Sie müssen ein Passwort eingeben.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen ein Passwort eingeben.'
                    },
                    stringLength: {
                        min: 6,
                        max: 32,
                        message: 'Das Passwort muss zwischen 6 und 16 Zeichen lang sein.'
                    }
                }
            }
        }
    });

    bootstrapValChange = $(changecamform).data('bootstrapValidator');

    $('#ccauth').change(function() {
        if ($(this).prop('checked')) {
            $('#ccuser').removeAttr("disabled");
            $('#ccpassword').removeAttr("disabled");
            $("#ccuserlabel").css({"opacity":"1.0"});
            $("#ccpasswordlabel").css({"opacity":"1.0"});
            bootstrapValChange.enableFieldValidators('ccuser', true);
            bootstrapValChange.enableFieldValidators('ccpassword', true);
            bootstrapValChange.validateField('ccuser');
            bootstrapValChange.validateField('ccpassword');
        } else {
            var ccuser = $('#ccuser');
            var ccpass = $('#ccpassword');
            $(ccuser).val('');
            $(ccpass).val('');
            $(ccuser).attr("disabled", "disabled");
            $(ccpass).attr("disabled", "disabled");
            $("#ccuserlabel").css({"opacity":"0.5"});
            $("#ccpasswordlabel").css({"opacity":"0.5"});
            bootstrapValChange.enableFieldValidators('ccuser', false);
            bootstrapValChange.enableFieldValidators('ccpassword', false);
        } //endif
    });
});