window.user_accountname = '';
window.user_email = '';
window.formsubmit = 0;

function addUser() {
    window.formsubmit = 0;
    if (window.user_admin != '2') {
        $('#castatusgroup').hide();
    } //endif

    $('#caid').val('');
    $('#caaccountname').val('');
    $('#caemail').val('');
    $('#caname').val('');
    $('#casurname').val('');
    $('#capassword1').val('');
    $('#capassword2').val('');
    $('#castatus').val('0');

    $('#adduser-modal').modal('show');
} //endfunction addUser

function deleteUser(id) {
    window.formsubmit = 0;
    $('#duid').val(id);
    $('#deleteuser-modal').modal('show');
} //endfunction deleteUser

function changeUserPwd(id) {
    window.formsubmit = 0;
    $('#cpid').val(id);
    $('#changeupwd-modal').modal('show');
} //endfunction changeuserPwd

function changeUserData(id) {
    window.formsubmit = 0;
    if (window.user_admin != '2') {
        $('#custatusgroup').hide();
    } //endif

    $.ajax({
        type: "POST",
        url: "/user-by-id",
        data: 'id='+id,
        cache: false,
        async: true,
        success: function(rsp) {
            var resp = $.parseJSON(rsp);

            $('#cuid').val(resp['id']);
            $('#cuaccountname').val(resp['accountname']);
            $('#cuemail').val(resp['email']);
            $('#cuname').val(resp['name']);
            $('#cusurname').val(resp['surname']);
            $('#custatus').val(resp['admin']);
            window.user_accountname = resp['accountname'];
            window.user_email = resp['email'];
        }
    });
    $('#changeudata-modal').modal('show');
} //endfunction changeUserData

$(document).ready(function() {
    $('#du-btn').click(function() {
        $.ajax({
            type: "POST",
            url: "/usermanager-delete",
            data: "id=" + $('#duid').val(),
            cache: false,
            async: true,
            success: function(rsp) {
                setTimeout($('#usertable').DataTable().ajax.reload(), 10);
            }
        });
        $('#deleteuser-modal').modal('hide');
    });

    $('#adduser-modal').on('hidden.bs.modal', function () {
        $('#adduser-form').data('bootstrapValidator').resetForm();
        $('#castatusgroup').show();
    });

    var cupwdmodal = $('#changeupwd-modal');
    $(cupwdmodal).on('hidden.bs.modal', function () {
        $('#cppassword1').val('');
        $('#cppassword2').val('');
        $('#cupwd-form').data('bootstrapValidator').resetForm();
    });

    $(cupwdmodal).on('show.bs.modal', function () {
        $('#cppassword1').val('');
        $('#cppassword2').val('');
    });

    $('#changeudata-modal').on('hidden.bs.modal', function () {
        $('#cudata-form').data('bootstrapValidator').resetForm();
        window.user_accountname = '';
        window.user_email = '';
        $('#custatusgroup').show();
    });

    $("#usertable").dataTable({
        "language": {
            "url": "/plugins/datatables/dataTables.german.lang"
        },
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "/usermanager-datasource",
            type: "POST"
        },
        "columns": [
            { "data": "id" },
            { "data": "accountname" },
            { "data": "email" },
            { "data": "name" },
            { "data": "surname" },
            { "data": "admin" },
            { "data": "lastonline" },
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
                "targets": [5],
                "searchable": false,
                "render": function (data, type, row) {
                    if (row.admin == '1') {
                        return 'Administrator';
                    } else if (row.admin == '2') {
                        return 'Superadmin';
                    } else {
                        return 'Benutzer';
                    } //endif
                }
            },
            {
                "targets": [7],
                "searchable": false,
                "render": function (data, type, row) {
                    if (row.admin == '2' || (row.admin == '1' && window.user_admin != '2')) {
                        return '---';
                    } else {
                        var html = '';
                        html += '<a class="usertabaction actiondel" href="javascript: deleteUser('+data+');" title="Benutzer l&ouml;schen"><i class="fa fa-trash"></i></a>' +
                        '&nbsp;&nbsp;' +
                        '<a class="usertabaction actionpwd" href="javascript: changeUserPwd('+data+');" title="Passwort &auml;ndern"><i class="fa fa-lock"></i></a>' +
                        '&nbsp;&nbsp;' +
                        '<a class="usertabaction actiondata" href="javascript: changeUserData('+data+');" title="Daten &auml;ndern"><i class="fa fa-edit"></i></a>';

                        return html;
                    } //endif
                }
            }
        ]
    });

    $('#cupwd-form').bootstrapValidator({
        message: 'Ungültiger Wert',
        feedbackIcons: {
            valid: 'fa fa-ok',
            invalid: 'fa fa-remove',
            validating: 'fa fa-refresh'
        },
        live: 'enabled',
        submitButtons: '#cupwd-btn',
        submitHandler: function(validator, form, submitButton) {
            if (!window.formsubmit) {
                $.ajax({
                    type: "POST",
                    url: "/usermanager-change-password",
                    data: $('#cupwd-form').serialize(),
                    cache: false,
                    async: true
                });
                $('#changeupwd-modal').modal('hide');
                window.formsubmit = 1;
            } //endif
        },
        fields: {
            cppassword1: {
                message: 'Sie müssen ein Passwort eingeben.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen ein Passwort eingeben.'
                    },
                    identical: {
                        field: 'cppassword2',
                        message: 'Die Passwörter müssen übereinstimmen.'
                    },
                    stringLength: {
                        min: 6,
                        max: 32,
                        message: 'Das Passwort muss zwischen 6 und 16 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[A-Za-z0-9_\-.:!?*+#&%§<>ÄäÖöÜüÀÁáÂâÈèÉéÊêÙùÚúßÇç]+$/i,
                        message: 'Erlaubt sind nur Buchstaben, Zahlen und folgende Sonderzeichen: _-.:!?*+#&%§<>§'
                    }
                }
            },
            cppassword2: {
                message: 'Sie müssen das Passwort erneut eingeben.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen das Passwort erneut eingeben.'
                    },
                    identical: {
                        field: 'cppassword1',
                        message: 'Die Passwörter müssen übereinstimmen.'
                    },
                    stringLength: {
                        min: 6,
                        max: 32,
                        message: 'Das Passwort muss zwischen 6 und 16 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[A-Za-z0-9_\-.:!?*+#&%§<>ÄäÖöÜüÀÁáÂâÈèÉéÊêÙùÚúßÇç]+$/i,
                        message: 'Erlaubt sind nur Buchstaben, Zahlen und folgende Sonderzeichen: _-.:!?*+#&%§<>§'
                    }
                }
            }
        }
    });

    $('#adduser-form').bootstrapValidator({
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
                    url: "/usermanager-add",
                    data: $('#adduser-form').serialize(),
                    cache: false,
                    async: true,
                    success: function (rsp) {
                        $('#usertable').DataTable().ajax.reload();
                    }
                });
                $('#adduser-modal').modal('hide');
                window.formsubmit = 1;
            } //endif
        },
        fields: {
            caaccountname: {
                message: 'Bitte geben Sie einen Accountnamen ein.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen einen Accountnamen eingeben.'
                    },
                    stringLength: {
                        min: 3,
                        max: 32,
                        message: 'Der Accountname muss zwischen 3 und 32 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\-]+$/,
                        message: 'Der Accountname darf nur aus Buchstaben, Zahlen, sowie Binde- und Unterstrich bestehen.'
                    },
                    callback: {
                        message: 'Der Accountname existiert bereits.',
                        callback: function(value, validator) {
                            var username = encodeURIComponent($('#caaccountname').val());
                            var response = $.ajax({
                                type: "GET",
                                url: "/user-exists/" + username,
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
            caemail: {
                validators: {
                    notEmpty: {
                        message: 'Sie müssen Ihre Mailadresse angeben.'
                    },
                    emailAddress: {
                        message: 'Dies ist keine gültige Mailadresse.'
                    },
                    stringLength: {
                        min: 5,
                        max: 64,
                        message: 'Die Mailadresse muss zwischen 5 und 64 Zeichen lang sein.'
                    },
                    callback: {
                        message: 'Diese Mailadresse wurde bereits registriert.',
                        callback: function(value, validator) {
                            var mail = encodeURIComponent($('#caemail').val());
                            var response = $.ajax({
                                type: "GET",
                                url: "/email-exists/" + mail,
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
            caname: {
                message: 'Bitte geben Sie Ihren Vornamen ein.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen Ihren Vornamen eingeben.'
                    },
                    stringLength: {
                        min: 3,
                        max: 32,
                        message: 'Der Vorname muss zwischen 3 und 32 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZÄäÖöÜüÀÁáÂâÈèÉéÊêÙùÚúßÇç]+$/,
                        message: 'Der Vorname darf nur aus Buchstaben bestehen.'
                    }
                }
            },
            casurname: {
                message: 'Bitte geben Sie Ihren Nachnamen ein.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen Ihren Nachnamen eingeben.'
                    },
                    stringLength: {
                        min: 3,
                        max: 32,
                        message: 'Der Nachname muss zwischen 3 und 32 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZÄäÖöÜüÀÁáÂâÈèÉéÊêÙùÚúßÇç]+$/,
                        message: 'Der Nachname darf nur aus Buchstaben bestehen.'
                    }
                }
            },
            capassword1: {
                message: 'Sie müssen ein Passwort eingeben.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen ein Passwort eingeben.'
                    },
                    identical: {
                        field: 'capassword2',
                        message: 'Die Passwörter müssen übereinstimmen.'
                    },
                    stringLength: {
                        min: 6,
                        max: 32,
                        message: 'Das Passwort muss zwischen 6 und 16 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[A-Za-z0-9_\-.:!?*+#&%§<>ÄäÖöÜüÀÁáÂâÈèÉéÊêÙùÚúßÇç]+$/i,
                        message: 'Erlaubt sind nur Buchstaben, Zahlen und folgende Sonderzeichen: _-.:!?*+#&%§<>§'
                    }
                }
            },
            capassword2: {
                message: 'Sie müssen das Passwort erneut eingeben.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen das Passwort erneut eingeben.'
                    },
                    identical: {
                        field: 'capassword1',
                        message: 'Die Passwörter müssen übereinstimmen.'
                    },
                    stringLength: {
                        min: 6,
                        max: 32,
                        message: 'Das Passwort muss zwischen 6 und 16 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[A-Za-z0-9_\-.:!?*+#&%§<>ÄäÖöÜüÀÁáÂâÈèÉéÊêÙùÚúßÇç]+$/i,
                        message: 'Erlaubt sind nur Buchstaben, Zahlen und folgende Sonderzeichen: _-.:!?*+#&%§<>§'
                    }
                }
            },
            castatus: {
                validators: {
                    callback: {
                        message: 'Ungültiger Statuswert.',
                        callback: function(value, validator) {
                            return true;
                        }
                    }
                }
            }
        }
    });

    $('#cudata-form').bootstrapValidator({
        message: 'Ungültiger Wert',
        feedbackIcons: {
            valid: 'fa fa-ok',
            invalid: 'fa fa-remove',
            validating: 'fa fa-refresh'
        },
        live: 'enabled',
        submitButtons: '#cudata-btn',
        submitHandler: function(validator, form, submitButton) {
            if (!window.formsubmit) {
                $.ajax({
                    type: "POST",
                    url: "/usermanager-change-data",
                    data: $('#cudata-form').serialize(),
                    cache: false,
                    async: true,
                    success: function (rsp) {
                        $('#usertable').DataTable().ajax.reload();
                    }
                });
                $('#changeudata-modal').modal('hide');
                window.formsubmit = 1;
            } //endif
        },
        fields: {
            cuaccountname: {
                message: 'Bitte geben Sie einen Accountnamen ein.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen einen Accountnamen eingeben.'
                    },
                    stringLength: {
                        min: 3,
                        max: 32,
                        message: 'Der Accountname muss zwischen 3 und 32 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\-]+$/,
                        message: 'Der Accountname darf nur aus Buchstaben, Zahlen, sowie Binde- und Unterstrich bestehen.'
                    },
                    callback: {
                        message: 'Der Accountname existiert bereits.',
                        callback: function(value, validator) {
                            var username = encodeURIComponent($('#cuaccountname').val());
                            if (encodeURIComponent(window.user_accountname) != username) {
                                var response = $.ajax({
                                    type: "GET",
                                    url: "/user-exists/" + username,
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
            cuemail: {
                validators: {
                    notEmpty: {
                        message: 'Sie müssen Ihre Mailadresse angeben.'
                    },
                    emailAddress: {
                        message: 'Dies ist keine gültige Mailadresse.'
                    },
                    stringLength: {
                        min: 5,
                        max: 64,
                        message: 'Die Mailadresse muss zwischen 5 und 64 Zeichen lang sein.'
                    },
                    callback: {
                        message: 'Diese Mailadresse wurde bereits registriert.',
                        callback: function(value, validator) {
                            var mail = encodeURIComponent($('#cuemail').val());
                            if (encodeURIComponent(window.user_email) != mail) {
                                var response = $.ajax({
                                    type: "GET",
                                    url: "/email-exists/" + mail,
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
            cuname: {
                message: 'Bitte geben Sie Ihren Vornamen ein.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen Ihren Vornamen eingeben.'
                    },
                    stringLength: {
                        min: 3,
                        max: 32,
                        message: 'Der Vorname muss zwischen 3 und 32 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZÄäÖöÜüÀÁáÂâÈèÉéÊêÙùÚúßÇç]+$/,
                        message: 'Der Vorname darf nur aus Buchstaben bestehen.'
                    }
                }
            },
            cusurname: {
                message: 'Bitte geben Sie Ihren Nachnamen ein.',
                validators: {
                    notEmpty: {
                        message: 'Sie müssen Ihren Nachnamen eingeben.'
                    },
                    stringLength: {
                        min: 3,
                        max: 32,
                        message: 'Der Nachname muss zwischen 3 und 32 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZÄäÖöÜüÀÁáÂâÈèÉéÊêÙùÚúßÇç]+$/,
                        message: 'Der Nachname darf nur aus Buchstaben bestehen.'
                    }
                }
            },
            custatus: {
                validators: {
                    callback: {
                        message: 'Ungültiger Statuswert.',
                        callback: function(value, validator) {
                            return true;
                        }
                    }
                }
            }
        }
    });
});