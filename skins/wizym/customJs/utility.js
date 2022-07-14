const clickable_row = $(".clickable-row");

$(document).ready(function () {
    clickable_row.css("cursor", "pointer");
    clickable_row.click(function () {
        window.location = $(this).data("href");
    });

    $('.table').DataTable({
        "language": {
            "lengthMenu": "Mostra _MENU_ record per pagina",
            "zeroRecords": "Nessun record trovato",
            "info": "Pagina _PAGE_ di _PAGES_",
            "infoEmpty": "Nessun record trovato",
            "infoFiltered": "(filtrato da _MAX_ record totali)",
            "search": "Cerca",
            "paginate": {
                "previous": "Precedente",
                "next": "Successivo"
            }
        },
        "order": [[0, "asc"]]
    });

    $('.page-item.active .page-link').css({'background-color': '#c02323', 'border-color': '#c02323'});
});

//Rimuove gli alert con uno slideUp dopo X secondi
function removeAlert() {
    setTimeout(function () {
        $('div.alert').slideUp(200).alert('close');
    }, 3000);
}

//Aggiunge un alert con il messaggio passato e lo inserisce dopo il selettore parent passato.
//L'alert sparisce in automatico dopo X secondi
function addAlert(type, parent, message) {
    let color;
    switch (type) {
        case 'error':
            color = '#c02323';
            break;
        case 'success':
            color = '#23c02b';
            break;
        default:
            color = 'black';
    }
    parent.after('' +
        '<div class="alert alert-dismissible fade show m-0" role="alert" style="color:' + color + ';">' +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-hidden="true">×</button>' +
        message +
        '</div>'
    );

    removeAlert();
}