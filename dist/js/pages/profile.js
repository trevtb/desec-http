$(document).ready(function() {
    $('#userDataForm').bootstrapValidator({
        message: 'Ungültiger Wert',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        live: 'enabled',
        submitButtons: 'input[type="submit"]',
        submitHandler: function(validator, form, submitButton) {
            validator.defaultSubmit();
        },
        fields: {
            accountname: {
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
                            var username = encodeURIComponent($('#accountname').val());
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
            name: {
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
            surname: {
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
            email: {
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
                            var mail = encodeURIComponent($('#email').val());
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
            }
        }
    });

    $('#userPasswordForm').bootstrapValidator({
        message: 'Ungültiger Wert',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        live: 'enabled',
        submitButtons: 'input[type="submit"]',
        submitHandler: function(validator, form, submitButton) {
            validator.defaultSubmit();
        },
        fields: {
            password1: {
                validators: {
                    notEmpty: {
                        message: 'Sie müssen ein Passwort eingeben.'
                    },
                    identical: {
                        field: 'password2',
                        message: 'Die Passwörter stimmen nicht überein.'
                    },
                    stringLength: {
                        min: 6,
                        max: 32,
                        message: 'Das Passwort muss zwischen 6 und 16 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[A-Za-z0-9_\-.:!?*+#&%§<>ÄäÖöÜüÀÁáÂâÈèÉéÊêÙùÚúßÇç]+$/i,
                        message: 'Erlaubt sind nur Buchstaben, Zahlen und folgenden Sonderzeichen: _-.:!?*+#&%§<>§'
                    }
                }
            },
            password2: {
                validators: {
                    notEmpty: {
                        message: 'Sie müssen das Passwort erneut eingeben.'
                    },
                    identical: {
                        field: 'password1',
                        message: 'Die Passwörter stimmen nicht überein.'
                    },
                    stringLength: {
                        min: 6,
                        max: 32,
                        message: 'Das Passwort muss zwischen 6 und 16 Zeichen lang sein.'
                    },
                    regexp: {
                        regexp: /^[A-Za-z0-9_\-.:!?*+#&%§<>ÄäÖöÜüÀÁáÂâÈèÉéÊêÙùÚúßÇç]+$/i,
                        message: 'Erlaubt sind nur Buchstaben, Zahlen und folgenden Sonderzeichen: _-.:!?*+#&%§<>§'
                    }
                }
            }
        }
    });
});