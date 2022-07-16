$(document).ready(function () {
    const address_form = $('#new-address-form');
    const address_form_array = address_form.find('input');
    const msg_span = $('#cart-msg');
    const thead = $('thead');
    const table = $('table');
    const tbody = $('tbody');


    //Se non ci sono prodotti nel carrello mostra un messaggio
    let tr = $('#cart-table-body').find($('tr[id^="tr"]'));
    if (tr.attr('id').replace('tr', '').length === 0) {
        tr.remove();
    }

    //Al caricamento del carrello vengono calcolati tutti i valori totali da mostrare all'utente
    $.each($('li[id^="quantity"]'), function (index, value) {
        let id = $(value).attr('id').toString().replace('quantity', '');
        let product_name = $('#nome' + id);
        let dimensione = $('#dimensione' + id);
        let totale = $('#totale' + id);
        let order_recap = $('#order-recap');

        //dimensione del singolo prodotto
        let dimensioneOriginale = parseFloat($('#dimensioneOriginale' + id).val()).toFixed(2);
        //quantità del prodotto selezionata
        let quantity = parseInt($(value).text());
        //prezzo del singolo prodotto
        let prezzo = parseFloat($('#prezzo' + id).text()).toFixed(2);

        //prezzo totale calcolato con la quantità selezionata
        totale.text((parseFloat(prezzo) * quantity).toFixed(2) + '€');
        //dimensione totale calcolata con la quantità selezionata
        dimensione.text((dimensioneOriginale * quantity).toFixed(2) + "l");

        //aggiorna il recap del carrello
        order_recap.after(
            '<tr class="order-recap-row" data-id="' + id + '">' +
            '<td style="width: 12rem;">' + product_name.text() + '</td>' +
            '<td class="text-center" data-id="quantita' + id + '">' + quantity + '</td>' +
            '<td class="woocommerce-Price-amount amount text-end" data-id="totale' + id + '">' + (parseFloat(prezzo) * quantity).toFixed(2) + '&euro;</td>' +
            '</tr>'
        );
    });

    //chiamo la funzione che aggiorna il totale del carrello
    updateTotal();

    //Incremento e decremento delle quantità di un prodotto
    $('li i').click(function () {
        let button = $(this);
        let id = button.attr('id').toString();
        if (~id.indexOf("qplus")) {
            id = id.replace('qplus', '');
        } else {
            id = id.replace('qminus', '');
        }
        let dimensioneOriginale = parseFloat($('#dimensioneOriginale' + id).val()).toFixed(2);
        let dimensione = $('#dimensione' + id);
        let quantity = $('#quantity' + id);
        let totale = $('#totale' + id);
        let prezzo = parseFloat($('#prezzo' + id).text()).toFixed(2);
        let oldQ = parseInt(quantity.text());
        let newQ = 0;

        if (button.attr("id") === ("qplus" + id)) {
            newQ = oldQ + 1;
        } else if (button.attr("id") === ("qminus" + id)) {
            // Don't allow decrementing below zero
            if (oldQ > 0 && oldQ !== 1) {
                newQ = oldQ - 1;
            }
        }

        if (newQ > 0) {
            $.ajax({
                url: '/cart/' + id + '/edit',
                type: 'POST',
                data: {
                    id: id,
                    quantity: newQ,
                },
                success: function (data) {
                    let response = JSON.parse(data);
                    if (response['success']) {
                        quantity.text(newQ);
                        totale.text((parseFloat(prezzo) * newQ).toFixed(2) + '€');
                        dimensione.text((dimensioneOriginale * newQ).toFixed(2) + "l");
                        let table_recap_row = $('.order-table').find('tr[data-id="' + id + '"]');
                        table_recap_row.find('td[data-id="quantita' + id + '"]').text(newQ);
                        table_recap_row.find('td[data-id="totale' + id + '"]').text((parseFloat(prezzo) * newQ).toFixed(2) + '€');
                        updateTotal();
                    } else if (response['error']) {
                        addAlert('error', msg_span, response['error']);
                    } else {
                        addAlert('error', msg_span, response['warning']);
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        } else {
            addAlert('error', msg_span, 'La quanti&agrave; deve essere superiore a 0');
        }
    });

    //Manda l'utente nella tab per il checkout
    $('#check-out-btn').click(function () {
        if ($('.dataTables_empty').text() === "Nessun record trovato" || $('#cart-table-body').children().length === 0) {
            addAlert('error', msg_span, 'Nessun elemento nel carrello.')
        } else {
            $('#tab-reviews-btn').trigger('click');
        }
    });

    //Di default la form di inserimento indirizzo è nascosta
    address_form.hide();

    if ($('div.address-saved input[name="address"]').first().attr('value') === '') {
        $('div.address-saved input[name="address"]').first().parent().remove()
    }

    //Se si seleziona un nuovo indirizzo viene mostrata la form d'inserimento
    $("input[name='address']").change(function () {
        if ($(this).val() === '0') {
            address_form.show();
            $.each(address_form_array, function (index, value) {
                $(value).attr('required', true);
            });
        } else {
            address_form.hide();
            $.each(address_form_array, function (index, value) {
                $(value).attr('required', false);
            });
        }
    });

    /*-------------------------------------------------MODALE---------------------------------------------------------*/
    //Se si cerca di rimuovere un prodotoo dal carrello, viene mostrata la form di conferma di eliminazione
    let delete_url = ''; //global variable
    $('#delete-confirmation').on('show.bs.modal', function (e) {
        $('#delete-modal-text').html('Sei sicuro di voler rimuovere il prodotto dal tuo carrello?');
        delete_url = $(e.relatedTarget).data('id'); //fetch value of `data-id` attribute load it to global variable
    });

    //rimuove il prodotto dal carrello
    $('#remove-button').click(function () {
        remove(delete_url);
        addAlert('success', msg_span, 'Prodotto eliminato con successo');
    });


    //rimuove il prodotto dal carrello
    $('#remove-all-button').click(function () {
        $.each($('td[id^="remove"]'), function (index, value) {
            let id = $(value).attr('id').toString().replace('remove', '');
            delete_url = '/cart/' + id + '/remove';
            remove(delete_url);
        });
        addAlert('success', msg_span, 'Carrello svuotato con successo');
        $('#close-modal-all').trigger('click');
    });

    /*
    * @Author: Davide De Acetis
    *
    * Aggiorna il totale del carrello
    * @param {int} id - Id del prodotto da aggiungere
    * @param {int} quantity - Quantità del prodotto da aggiungere
    */
    function updateTotal() {
        let total = 0.00;
        let cart_total = $('#cart-total');
        $.each($('td[id^="totale"]'), function (index, value) {
            total += parseFloat($(value).text());
        });
        cart_total.html(total.toFixed(2) + ' &euro;');
        let order_recap_total = $('#order-recap-total');
        order_recap_total.text(cart_total.text());
    }

    /*
    * @Author: Davide De Acetis
    *
    * Rimuove un prodotto dal carrello
    * @param {String} delete_url - Url della delete con l'id del prodotto da rimuovere
    */
    function remove(delete_url) {
        $.ajax({
            url: delete_url,
            type: 'POST',
            success: function (data) {
                $('#delete-confirmation').modal('hide');
                let response = JSON.parse(data);

                if (response['success']) {
                    let id = delete_url.replace('/cart/', '').replace('/remove', '');
                    $('#tr' + id).remove();
                    $('tr[data-id="' + id + '"]').remove();
                    if ($('#cart-table-body').children().length === 0) {
                        thead.children().remove();
                        tbody.children().remove();
                        thead.html('<tr><th scope="col">Non ci sono prodotti nel carrello</th></tr>');
                        tbody.append("<tr class='odd'><td colspan='6' class='dataTables_empty'>Nessun record trovato</td></tr>")
                        $('#' + table.attr('aria-describedby')).text('Nessun record trovato');
                    }
                } else if (response['error']) {
                    addAlert('error', msg_span, response['error'])
                } else {
                    addAlert('error', msg_span, response['warning'])
                }
                updateTotal();
            },
            error: function (data) {
                console.log(data);
            }
        });
    }
});