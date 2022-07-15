let delete_url = ''; //global variable

$(document).ready(function () {
    const msg = $('#messaggio-errore');

    const cambio_password_form = $('#cambio-password-form');

    const annulla_button_container = $("#annulla-button-container");
    const annulla_password_button_container = $("#annulla-password-button-container");
    const modifica_button_container = $("#modifica-button-container");
    const password_button_container = $("#password-button-container");

    const annulla_edit_button = $("#annulla-edit-button");
    const annulla_password_button = $("#annulla-password-button");
    const edit_info_button = $("#edit-info-button");
    const edit_password_button = $("#edit-password-button");

    const nome = $("#nome");
    const cognome = $("#cognome");
    const telefono = $("#numero_telefono");
    const email = $("#email");

    const password_v = $("#password_v");
    const password_n = $("#password_n");
    const password_c = $("#password_c");

    const param = window.location.search ? window.location.search.substring(1).replace('success=', '') : '';
    if (param === "true") {
        addAlert('success', msg, 'Modifica effettuata con successo');
    }

    //Se si cerca di rimuovere un indirizzo, viene mostrata la form di conferma di eliminazione
    $('#delete-confirmation').on('show.bs.modal', function (e) {
        let message = $(e.relatedTarget).data('title');
        $('#delete-modal-text').html(message);
        delete_url = $(e.relatedTarget).data('href'); //fetch value of `data-id` attribute load it to global variable
    });

    //rimuove il prodotto dal carrello
    $('#remove-button').click(function () {
        deleteWithModal(delete_url);
    });

    //Controllo delle tab
    $('.tab').click(function () {
        $('.tab').removeClass('active');
        $(this).addClass('active');
        $('div[id^="tab"]').hide();
        $($(this).data('id')).show();
    });

    //Click del pulsante per annullare la modifica della password
    annulla_password_button.click(function () {
        cambio_password_form.hide(500);
        edit_password_button.html('<span class="fe fs-14"></span> MODIFICA LA PASSWORD');
        edit_password_button.val('edit-password');
        annulla_password_button_container.hide();
        modifica_button_container.show();
        switchPasswordForm();
    });

    //Click del pulsante per la modifica della password
    edit_password_button.click(function () {
        if ($(this).val() === 'edit-password') {
            cambio_password_form.show(500);
            annulla_password_button_container.show();
            modifica_button_container.hide();
            switchPasswordForm();
            $(this).val('salva-password');
            $(this).html('<span class="fe fs-14"></span> SALVA</m>');
        } else {
            console.log(password_n.val());
            if (password_n.val() !== "" && password_c.val() !== "" && password_v.val() !== "") {
                if (password_n.val().length >= 8) {
                    if (password_n.val() === password_c.val()) {
                        // Faccio la chiamata e in base al risultato lo mostro a schermo
                        $.ajax({
                            type: 'POST',
                            url: '/myAccountPassword',
                            data: {
                                'password_v': password_v.val(),
                                'password_n': password_n.val(),
                                'password_c': password_c.val(),
                            },
                            success: function (data) {
                                let response = JSON.parse(data);
                                if (response['success']) {
                                    edit_password_button.val('edit-password');
                                    edit_password_button.html('<span class="fe fs-14"></span> MODIFICA LA PASSWORD');
                                    cambio_password_form.hide(500);
                                    annulla_password_button_container.hide();
                                    modifica_button_container.show();
                                    addAlert('success', msg, response['success']);
                                } else if (response['error']) {
                                    addAlert('error', msg, response['error']);
                                } else {
                                    addAlert('warning', msg, response['warning']);
                                }
                            }
                        });
                    } else {
                        addAlert('error', msg, 'La nuova password non coincide con la conferma');
                    }
                } else {
                    addAlert('error', msg, 'La password deve avere almeno 8 caratteri');
                }
            } else {
                addAlert('error', msg, 'Compilare tutti i campi correttamente');
            }
        }
    });


    annulla_edit_button.click(function () {
        annulla_button_container.hide();
        password_button_container.show();
        edit_info_button.val('edit-info');
        edit_info_button.html('<span class="fe fs-14"></span>AGGIORNA INFORMAZIONI');
        switchEditForm();
    });

    edit_info_button.click(function () {
        password_button_container.hide();
        annulla_button_container.show();


        if ($(this).val() === 'edit-info') {
            $(this).val('save');
            $(this).html('<span class="fe fs-14"></span> SALVA</m>');
            switchEditForm();
        } else {
            if (nome.val() !== '' && cognome.val() !== '' && telefono.val() !== '' && email.val() !== '') {
                if (email.val().search("@") !== -1 && email.val().search('.') !== -1) {
                    $.ajax({
                        type: 'POST',
                        url: '/myAccountEdit',
                        data: {
                            'nome': nome.val(),
                            'cognome': cognome.val(),
                            'numero_telefono': telefono.val(),
                            'email': email.val(),
                        },
                        success: function (data) {
                            let response = JSON.parse(data);
                            if (response['success']) {
                                edit_info_button.val('edit-info');
                                edit_info_button.show();
                                edit_info_button.html('<span class="fe fs-14"></span> AGGIORNA INFORMAZIONI');
                                annulla_button_container.hide();
                                password_button_container.show();
                                switchEditForm();

                                addAlert('success', msg, response['success']);
                            } else if (response['error']) {
                                addAlert('error', msg, response['error']);
                            } else {
                                addAlert('warning', msg, response['warning']);
                            }
                        }
                    });
                } else {
                    addAlert('warning', msg, 'Inserisci una email valida');
                }
            } else {
                addAlert('warning', msg, 'I campi non possono essere vuoti');
            }
        }
    });

    function switchEditForm() {
        nome.attr("readonly", !nome.attr('readonly'));
        cognome.attr("readonly", !cognome.attr('readonly'));
        telefono.attr("readonly", !telefono.attr('readonly'));
        email.attr("readonly", !email.attr('readonly'));
    }

    function switchPasswordForm() {
        password_v.attr("readonly", !password_v.attr('readonly'));
        password_n.attr("readonly", !password_n.attr('readonly'));
        password_c.attr("readonly", !password_c.attr('readonly'));
    }

    function deleteWithModal(delete_url) {
        $.ajax({
            url: delete_url,
            type: 'POST',
            success: function (data) {
                $('#delete-confirmation').modal('hide');
                let response = JSON.parse(data);
                if (response['success']) {
                    window.location.href = window.location.pathname + "?" + $.param({'success': 'true'});
                } else {
                    $('#error .modal-body .text-danger').text(response['error']);
                    $('#error').modal('show');
                }
            }
        });
    }
})