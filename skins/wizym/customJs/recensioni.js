/*
* @Author: Domenico Santone
*
* Azioni e funzioni per la gestione della show di un prodotto
* @param {int} id - Id del prodotto
* @param {int} voto - Voto della recensione
* @param {string} commento - Commento della recensione
*/
$(document).ready(function () {
    const voto_medio = parseFloat($('#voto-medio').val());
    const stelle = $('#stars');



    // Media delle recensioni del prodotto
    raty(stelle, voto_medio);

    // Creazione delle stelle per tutte le recensioni del prodotto
    $('.post-rating-stars').each(function () {
        if ($(this).attr('data-rating') !== "") {
            raty($(this), $(this).attr('data-rating'));
        } else {
            $(this).html('<h2>Nessuna recensione</h2>');
        }
    })

    // Se l'utente è loggato e non ha già inserito una recensione allora mostro la form
    if (document.getElementById("recensione").value === "true") {
        $('#recensione-form').show();
        $('#your-rating').raty({
            number: 5,
            readOnly: false,
            starOff: 'fa fa-star-o text-warning',
            starOn: 'fa fa-star text-warning',
            starHalf: 'fa fa-star-half-o text-warning',
            starType: 'i',
            target: '#voto',
            targetType: 'score',
            targetKeep: true
        });
    }

    // Inserimento di una recensione da parte di un utente
    $("#submit").click(function () {
        let voto = $("#voto").val();
        let commento = $("#comment").val();

        if (commento !== "" && voto !== "") {
            let id = $("#id_prodotto").val();
            addRecensione(id, voto, commento);
        } else {
            addAlert('error', $('#msg'), 'Uno o più campi non sono stati compilati');
        }
    })
});

/*
* @Author: Domenico Santone
*
* Aggiunge una recensione al prodotto
* @param {int} id - Id del prodotto
* @param {int} voto - Voto della recensione
* @param {string} commento - Commento della recensione
*/
function addRecensione(voto, commento, id) {
    $.ajax({
        type: "POST",
        url: "/reviews/add",
        data: {
            score: voto,
            comment: commento,
            id_prodotto: id
        },
        success: function (data) {
            let response = JSON.parse(data);
            if (response['success']) {
                window.location.reload();
            }
        }
    });
}