let delete_url = ''; //variabile globale che memorizza la url da eliminare con il modale

$(document).ready(function () {
    //DICHIARAZIONE VARIABILI
    const msgInfo = $('#messaggio-errore-info');
    const msgIndirizzi = $('#messaggio-errore-indirizzi');
    const msgRecensioni = $('#messaggio-errore-recensioni');

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

    const indirizzo_id = $("#indirizzo_id");
    const indirizzo = $("#indirizzo");
    const citta = $("#citta");
    const cap = $("#cap");
    const provincia = $("#provincia");
    const nazione = $("#nazione");

    const address_form = $('#new-address-form');
    const address_form_array = address_form.find('input');
    const add_indirizzo_button = $('#add-indirizzo-button');
    const add_indirizzo_button_container = $('#add-indirizzo-button-container');
    const submit_indirizzo_button = $('#submit-indirizzo-button');
    const annulla_indirizzo_button = $('#annulla-indirizzo-button');
    const tab_indirizzi = $('#tab-indirizzi');
    const table = tab_indirizzi.find('table');
    const tbody = table.find('tbody');

    //Controllo delle tab per la pagina account
    $('.tab').click(function () {
        $('.tab').removeClass('active');
        $(this).addClass('active');
        $('div[id^="tab"]').hide(200);
        $($(this).data('id')).show(200);
        hidePasswordForm();
        hideAddressForm();
        disableEditForm();
    });

    /*-------------------------------------------INFORMAZIONI---------------------------------------------------------*/
    //Click del pulsante per annullare la modifica della password
    annulla_password_button.click(function () {
        hidePasswordForm();
    });

    //Click del pulsante per la modifica della password
    edit_password_button.click(function () {
        if ($(this).val() === 'edit-password') {
            showPasswordForm();
        } else {
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
                                    addAlert('success', msgInfo, response['success']);
                                } else if (response['error']) {
                                    addAlert('error', msgInfo, response['error']);
                                } else {
                                    addAlert('warning', msgInfo, response['warning']);
                                }
                            }
                        });
                    } else {
                        addAlert('error', msgInfo, 'La nuova password non coincide con la conferma');
                    }
                } else {
                    addAlert('error', msgInfo, 'La password deve avere almeno 8 caratteri');
                }
            } else {
                addAlert('error', msgInfo, 'Compilare tutti i campi correttamente');
            }
        }
    });

    //Click del pulsante l'annullamento dell'editing delle info di un utente
    annulla_edit_button.click(function () {
        disableEditForm();
    });

    //Click del pulsante per la modifica delle info
    edit_info_button.click(function () {
        if ($(this).val() === 'edit-info') {
            enableEditForm();
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

                                addAlert('success', msgInfo, response['success']);
                            } else if (response['error']) {
                                addAlert('error', msgInfo, response['error']);
                            } else {
                                addAlert('warning', msgInfo, response['warning']);
                            }
                        }
                    });
                } else {
                    addAlert('warning', msgInfo, 'Inserisci una email valida');
                }
            } else {
                addAlert('warning', msgInfo, 'I campi non possono essere vuoti');
            }
        }
    });

    //Abilita la modifica delle info
    function enableEditForm() {
        password_button_container.hide();
        annulla_button_container.show();
        edit_info_button.val('save');
        edit_info_button.html('<span class="fe fs-14"></span> SALVA</m>');

        nome.attr("readonly", false);
        cognome.attr("readonly", false);
        telefono.attr("readonly", false);
        email.attr("readonly", false);
    }

    //Disabilita la modifica delle info
    function disableEditForm() {
        annulla_button_container.hide();
        password_button_container.show();
        edit_info_button.val('edit-info');
        edit_info_button.html('<span class="fe fs-14"></span>AGGIORNA INFORMAZIONI');

        nome.attr("readonly", true);
        cognome.attr("readonly", true);
        telefono.attr("readonly", true);
        email.attr("readonly", true);
    }

    //Mostra la form per la modifica della password
    function showPasswordForm() {
        cambio_password_form.show(500);
        annulla_password_button_container.show();
        modifica_button_container.hide();
        edit_password_button.html('<span class="fe fs-14"></span> SALVA</m>');
        edit_password_button.val('salva-password');

        password_v.attr("readonly", false);
        password_n.attr("readonly", false);
        password_c.attr("readonly", false);
    }

    //Nasconde la form per la modifica della password
    function hidePasswordForm() {
        cambio_password_form.hide(500);
        edit_password_button.html('<span class="fe fs-14"></span> MODIFICA LA PASSWORD');
        edit_password_button.val('edit-password');
        annulla_password_button_container.hide();
        modifica_button_container.show();

        password_v.attr("readonly", true);
        password_n.attr("readonly", true);
        password_c.attr("readonly", true);
    }

    /*--------------------------------------------INDIRIZZI-----------------------------------------------------------*/
    //Nasconde la form per la modifica dell'indirizzo al caricamento della vista
    hideAddressForm();



    $('.edit-indirizzo').on('click', function () {
        let id = $(this).parent().data('id').replace('addresses', '');

        indirizzo_id.val(id);

        $.each($(this).parent().children(), function (index) {
            switch (index) {
                case 0:
                    indirizzo.val($(this).text().trim());
                    break;
                case 1:
                    citta.val($(this).text().trim());
                    break;
                case 2:
                    cap.val($(this).text().trim());
                    break;
                case 3:
                    provincia.val($(this).text().trim());
                    break;
                case 4:
                    nazione.val($(this).text().trim());
                    break;
                default:
                    break;
            }
        })
        showAddressForm();
    });

    //Click del pulsante per l'aggiunta di un indirizzo
    add_indirizzo_button.click(function () {
        showAddressForm();
    });

    //Click del pulsante per annullare l'aggiunta di un indirizzo
    annulla_indirizzo_button.click(function () {
        hideAddressForm();
    });

    //Click del pulsante per salvare l'indirizzo
    submit_indirizzo_button.click(function () {
        showAddressForm();
        if (indirizzo.val() !== "" && citta.val() !== "" && cap.val() !== "" && provincia.val() !== "" && nazione.val() !== "") {
            if (cap.val().length === 5 && provincia.val().length === 2) {
                if (indirizzo_id.val() === "") {
                    $.ajax({
                        url: '/addresses/create',
                        type: 'POST',
                        data: {
                            indirizzo: indirizzo.val(),
                            citta: citta.val(),
                            cap: cap.val(),
                            provincia: provincia.val(),
                            nazione: nazione.val()
                        },
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response["success"]) {
                                addTableRow(response["id"], indirizzo.val(), citta.val(), cap.val(), provincia.val(), nazione.val());
                                hideAddressForm();
                                addAlert("success", msgIndirizzi, response["success"]);
                            } else if (response['error']) {
                                addAlert("error", msgIndirizzi, response["error"]);
                            } else {
                                addAlert("warning", msgIndirizzi, response["warning"]);
                            }
                        },
                    });
                } else {
                    $.ajax({
                        url: '/addresses/' + indirizzo_id.val() + '/edit',
                        type: 'POST',
                        data: {
                            indirizzo: indirizzo.val(),
                            citta: citta.val(),
                            cap: cap.val(),
                            provincia: provincia.val(),
                            nazione: nazione.val()
                        },
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response["success"]) {
                                updateTableRow(indirizzo_id.val(), indirizzo.val(), citta.val(), cap.val(), provincia.val(), nazione.val());
                                hideAddressForm();
                                addAlert("success", msgIndirizzi, response["success"]);
                            } else if (response['error']) {
                                addAlert("error", msgIndirizzi, response["error"]);
                            } else {
                                addAlert("warning", msgIndirizzi, response["warning"]);
                            }
                        },
                    });
                }
            } else {
                addAlert("error", msgIndirizzi, "Il CAP deve avere 5 caratteri e la provincia 2");
            }
        } else {
            addAlert('error', msgIndirizzi, 'Compilare tutti i campi');
        }
        if ($(this).val() === 'salva') {
            hideAddressForm();
        }
    });

    //Funzione che aggiunge una riga della tabella con i dati dell'indirizzo
    function addTableRow(id, indirizzo, citta, cap, provincia, nazione) {
        let first_tr = tbody.find('tr').first().find('td')
        let style = 'odd';

        if (first_tr.text() !== 'Nessun record trovato') {
            let tr = tbody.find('tr').last();
            if (tr.hasClass('odd') === true) {
                style = 'even';
            } else {
                style = 'odd';
            }
        } else {
            first_tr.parent().remove();
            $('#' + table.attr('aria-describedby')).text('Pagina 1 di 1');
        }

        tbody.append("<tr class='text-start pointer " + style + "' data-id='addresses" + id + "'>" +
            "    <td class='edit-indirizzo dtr-control sorting_1' style='vertical-align: middle; !important; width: 15rem' tabindex='0'>" +
            indirizzo +
            "    </td>" +
            "    <td class='edit-indirizzo' style='vertical-align: middle; !important;'>" +
            citta +
            "    </td>" +
            "    <td class='edit-indirizzo' style='vertical-align: middle; !important;'>" +
            cap +
            "    </td>" +
            "    <td class='edit-indirizzo' style='vertical-align: middle; !important;'>" +
            provincia +
            "    </td>" +
            "    <td class='edit-indirizzo' style='vertical-align: middle; !important;'>" +
            nazione +
            "    </td>" +
            "    <td class='elm-btn flex align-items-center'>" +
            "        <a class='themesflat-button outline ol-accent margin-top-table hvr-shutter-out-horizontal pointer'" +
            "           style='font-size: 13px; margin: 0; padding-left: 20px; padding-right: 20px;'" +
            "           data-bs-toggle='modal'" +
            "           data-bs-target='#delete-confirmation'" +
            "           data-bs-original-title='Cancella'" +
            "           data-title='Sei sicuro di voler eliminare questo indirizzo?'" +
            "           data-id='" + id + "'" +
            "           data-href='/addresses/" + id + "/delete'><span" +
            "                class='fe fe-trash-2 fs-14'></span></a>" +
            "    </td>" +
            "</tr>");
    }

    //Funzione che aggiorna una riga della tabella con i dati dell'indirizzo aggiornato
    function updateTableRow(id, indirizzo, citta, cap, provincia, nazione) {
        let tr = tbody.find('tr[data-id="addresses' + id + '"]');
        $.each(tr.children(), function (index) {
            switch (index) {
                case 0:
                    $(this).text(indirizzo);
                    break;
                case 1:
                    $(this).text(citta);
                    break;
                case 2:
                    $(this).text(cap);
                    break;
                case 3:
                    $(this).text(provincia);
                    break;
                case 4:
                    $(this).text(nazione);
                    break;
                default:
                    break;
            }
        })
    }

    //Funzione che nasconde la form per l'aggiunta di un indirizzo
    function hideAddressForm() {
        indirizzo_id.val("");
        address_form.hide(500);
        add_indirizzo_button_container.show(500);
        $.each(address_form_array, function (index, value) {
            $(value).attr('required', false);
            $(value).val('');
        });
    }

    //Funzione che mostra la form per l'aggiunta di un indirizzo
    function showAddressForm() {
        add_indirizzo_button_container.hide(500);
        address_form.show(500);
        $.each(address_form_array, function (index, value) {
            $(value).attr('required', true);
        });
    }

    /*----------------------------------------------MODALE------------------------------------------------------------*/

    //Se si cerca di rimuovere un indirizzo, viene mostrata la form di conferma di eliminazione
    $('#delete-confirmation').on('show.bs.modal', function (e) {
        let message = $(e.relatedTarget).data('title');
        $('#delete-modal-text').html(message);
        delete_url = $(e.relatedTarget).data('href'); //fetch value of `data-id` attribute load it to global variable
    });

    //Conferma del modale
    $('#remove-button').click(function () {
        deleteWithModal(delete_url);
    });

    //Gestisce il modale per la pagina account
    function deleteWithModal(delete_url) {
        $.ajax({
            url: delete_url,
            type: 'POST',
            success: function (data) {
                $('#delete-confirmation').modal('hide');
                let response = JSON.parse(data);
                if (response['success']) {
                    if (delete_url.includes('addresses')) {
                        let tab_indirizzi = $('#tab-indirizzi');
                        const table = tab_indirizzi.find('table');
                        const tbody = table.find('tbody');
                        const id = delete_url.split('/')[2];

                        tbody.find('tr[data-id="addresses' + id + '"]').remove();

                        if (tbody.children().length === 0) {
                            $('#' + table.attr('aria-describedby')).text('Nessun record trovato');
                            tbody.append("<tr class='odd'><td colspan='6' class='dataTables_empty'>Nessun record trovato</td></tr>")
                        }
                        addAlert("success", msgIndirizzi, response['success']);

                    } else if (delete_url.includes('reviews')) {
                        let tab_indirizzi = $('#tab-recensioni');
                        const table = tab_indirizzi.find('table');
                        const tbody = table.find('tbody');
                        const id = delete_url.split('/')[2];

                        tbody.find('tr[data-id="reviews' + id + '"]').remove();

                        if (tbody.children().length === 0) {
                            $('#' + table.attr('aria-describedby')).text('Nessun record trovato');
                            tbody.append("<tr class='odd'><td colspan='6' class='dataTables_empty'>Nessun record trovato</td></tr>")
                        }

                        addAlert("success", msgRecensioni, response['success']);
                    }
                } else {
                    $('#error .modal-body .text-danger').text(response['error']);
                    $('#error').modal('show');
                }
            }
        });
    }
})