const msg = $('#titolo').parent();

// Get the modal
var modal = document.getElementById("dialog");

// Get the button that opens the modal
var btn = document.getElementById("annulla");

// Get the <span> element that closes the modal
var span = document.getElementById("close");

// When the user clicks on the button, open the modal
btn.onclick = function () {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function () {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

$(document).ready(function () {
    const x = $("#stato").val();

    const modifiche_ordine = $("#modifiche_ordine");
    const indirizzi = $("#indirizzi");
    const indirizzo_esistente = $("#indirizzo_esistente");
    const nuovo_indirizzo = $("#nuovo_indirizzo");
    const annulla_modifiche = $("#annulla_modifiche");
    const table_indirizzi = $("#table_indirizzi");
    const form_indirizzo = $("#form_indirizzo");

    const annulla = $("#annulla");
    const annullamento = $("#annullamento");
    const sospensione = $("#sospensione");
    const modifica = $("#modifica");

    annulla.hide();
    annullamento.hide();
    sospensione.hide();
    modifica.hide();

    modifiche_ordine.hide();
    indirizzi.hide();
    indirizzo_esistente.hide();
    nuovo_indirizzo.hide();
    annulla_modifiche.hide();
    table_indirizzi.hide();
    form_indirizzo.hide();

    if (x === "MEMORIZZATO" || x === "SOSPESO") {
        annulla.show();
    }

    if (x === "ANNULLATO") {
        annullamento.show();
    }
    if (x === "SOSPESO") {
        sospensione.show();
        modifica.show();
    }

    modifiche_ordine.hide();

    $("#conferma").click(function () {
        if (x === "MEMORIZZATO" || x === "SOSPESO") {
            $.ajax({
                type: "POST",
                url: "/orders/annulla",
                data: {
                    id: $("#id").val()
                },
                success: function (data) {
                    location.reload();
                }
            });
        } else {
            addAlert('error', msg, "Non puoi annullare l'ordine")
        }
    });

    modifica.click(function () {
        modifiche_ordine.show(500);
        indirizzi.hide(200);
        indirizzo_esistente.show();
        nuovo_indirizzo.show();
        annulla_modifiche.show();
    });

    indirizzo_esistente.click(function () {
        table_indirizzi.show(500);
        nuovo_indirizzo.hide(500);
    });

    nuovo_indirizzo.click(function () {
        $("#form_indirizzo").show(500);
        indirizzo_esistente.hide(500);
    });

    annulla_modifiche.click(function () {
        indirizzi.show(500);
        modifiche_ordine.hide(500)
        table_indirizzi.hide(500);
        form_indirizzo.hide(500)
    });
});