let pagination;//contenitore della paginazione
let currentItems; //prodotti correnti
let allItems; //tutti i prodotti
let currentCategoria; //categoria corrente
let currentPrezzo; //prezzo corrente
let currentOrdine; //ordine corrente
let cart; //icona del carrello

$(document).ready(function () {
    pagination = $('.flat-pagination'); //contenitore della paginazione
    currentItems = $('.product-item'); //prodotti correnti
    allItems = $('.product-item'); //tutti i prodotti
    currentCategoria = 'all'; //categoria corrente
    currentPrezzo = 1000; //prezzo corrente
    currentOrdine = 'nome-az'; //ordine corrente
    cart = $('#add-cart-header');

    const percentuale = $('#percentuale').val();
    const data_fine = $('#data-fine').val();

    $('.product').css('height', 'auto');

    //Imposta gli sconti per tutti i prodotti nella index
    $.each($('span[id^="percentuale"]'), function () {
        let id = $(this).attr('id').replace('percentuale', '');
        let percentuale = parseInt($(this).text().slice(1, -1));
        let amount = $('.amount' + id);
        showScontoIndex(id, percentuale, amount);
    });

    //Imposta gli sconti per tutti i prodotti nuovi nella home
    $.each($('span[id^="new_percentuale"]'), function () {
        let id = $(this).attr('id').replace('new_percentuale', '');

        let percentuale = parseInt($(this).text().slice(1, -1));
        let amount = $('.amount_new' + id);
        showScontoIndex(id, percentuale, amount);
    });



    //Imposta gli sconti per tutti i top prodotti nella home
    $.each($('span[id^="rec_percentuale"]'), function () {
        let id = $(this).attr('id').replace('rec_percentuale', '');

        let percentuale = parseInt($(this).text().slice(1, -1));
        let amount = $('.amount_rec' + id);
        showScontoIndex(id, percentuale, amount);
    });

    const recs = $('div[id^="rec"]');
    $.each(recs, function (index, value) {
        let id = value.id.replace('rec', '');
        let voto_medio = $('#voto-medio' + id).val();
        raty($(this), voto_medio);
    });

    // Aggiunge il prodotto ai preferiti dell'utente
    $('.heart').click(function () {
        let heart = $(this).find('i');
        let id = heart.data('id');
        addToLiked(id, heart);
    });

    // Aggiunta di un prodotto al carrello
    $('.cart-btn').click(function () {
        let id = $(this).find('i').data('id');
        let quantityS = $('#quantity');
        let quantity = quantityS ? quantityS.val() : 1;
        addToCart(id, quantity);
    });

    // Se è presente uno sconto aggiungo il prezzo scontato
    if (percentuale !== '' && data_fine !== '') {
        const prezzo_label = $('.dolar');
        const timer = $('.timer');
        showSconto(prezzo_label, timer, percentuale, data_fine);
    }

    // Comparsa dei filtri su mobile
    $('#filter').on('click', function () {
        $('.sidebar').slideToggle();
    });

    //filtro categorie
    $('li.thumb-new-categories').click(function () {
        currentCategoria = $(this).find('a').attr('id');
        applyFilter();
    });

    //filtro prezzo
    $('#filtro-prezzo-btn').click(function () {
        currentPrezzo = $('input[type="range"]').val();
        applyFilter();
    });

    // Ordinamento in base al prezzo
    $('#ordina-prezzo-cres').click(function () {
        currentOrdine = 'prezzo-cresc';
        applyFilter();
    });

    $('#ordina-prezzo-decr').click(function () {
        currentOrdine = 'prezzo-decr';
        applyFilter();
    });

    // Ordinamento in base al titolo
    $('#ordina-nome-az').click(function () {
        currentOrdine = 'nome-az';
        applyFilter();
    });

    $('#ordina-nome-za').click(function () {
        currentOrdine = 'nome-za';
        applyFilter();
    });

    $('#reset-filters').click(function () {
        currentCategoria = 'all';
        currentPrezzo = 1000;
        currentOrdine = 'nome-az';
        applyFilter();
    });
});



/*
* @Author: Domenico Santone
*
* Funzione che mostra su schermo lo sconto del prodotto, il prezzo finale e il tempo rimanente
* @param {Selector} prezzo_label - Selector del prezzo del prodotto
* @param {Selector} timer - Selector del timer
* @param {int} percentuale - Valore della percentuale di sconto
* @param {Date} data_fine - Data di scadenza dello sconto
*/
function showSconto(prezzo_label, timer, percentuale, data_fine) {
    //recupero il prezzo originale del prodotto
    let prezzo_base = parseFloat(prezzo_label.text().slice(0, -1)).toFixed(2);
    //calcolo il prezzo finale del prodotto
    let prezzo_scontato = prezzo_base * ((100 - percentuale) / 100);
    //mostro la percentuale di sconto e il prezzo finale
    prezzo_label.html('<p style="color: #c02323;">-' + percentuale + '%</p>' + prezzo_scontato.toFixed(2) + '€');
    //mostro il prezzo originale
    prezzo_label.after('<p>Prezzo consigliato: <s>' + parseFloat(prezzo_base).toFixed(2) + '€</s></p>');
    //Per il countdown
    let countDownDate = new Date(data_fine).getTime();
    //Aggiorna il countdown una volta al secondo
    setInterval(function () {
        // Data di oggi
        let now = new Date().getTime();
        // Trovo la distanza
        let distance = countDownDate - now;
        // Calcolo per i giorni, ore, minuti e secondi
        let days = Math.floor(distance / (1000 * 60 * 60 * 24));
        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);

        //Mostro il countdown
        timer.html('<p><strong style="color: #c02323;">Scade tra:</strong> ' + days + ' giorni ' + hours + ' ore ' + minutes + ' minuti ' + seconds + ' secondi</p>');
    }, 1000);
}

/*
* @Author: Domenico Santone, Davide De Acetis, Raluca Mihaela Bujoreanu
*
* Funzione che mostra su schermo lo sconto per ogni prodotto nella lista
* @param {Selector} prezzo_label - Selector del prezzo del prodotto
* @param {Selector} timer - Selector del timer
* @param {int} percentuale - Valore della percentuale di sconto
* @param {Date} data_fine - Data di scadenza dello sconto
*/
function showScontoIndex (id, percentuale, amount) {
    if (!isNaN(percentuale)) {
        let prezzo_base = amount.text().slice(0, -1);
        let prezzo_scontato = prezzo_base - (prezzo_base * (percentuale / 100));

        amount.html(`<p>${prezzo_scontato.toFixed(2)} € &nbsp;</p>`);
        amount.after(`<p style="color: black; font-size: .7em;">Prezzo consigliato: <s>${parseFloat(prezzo_base).toFixed(2)}€</s></p>`);
    }
}

/*
* @Author: Domenico Santone, Davide De Acetis, Raluca Mihaela Bujoreanu
*
* Funzione per l'aggiunta di un prodotto al carrello
* @param {int} id - Id del prodotto da aggiungere
* @param {int} quantity - Quantità del prodotto da aggiungere
*/
function addToCart(id, quantity) {
    $.ajax({
        type: 'POST',
        url: '/cart/add',
        data: {
            'id': id,
            'quantity': quantity
        },
        success: function (data) {
            let response = JSON.parse(data);
            if (response['success'] === "success") {
                //Se l'aggiornamento ha successo faccio lampeggiare di rosso il simbolo del carrello
                cart.addClass('text-danger');
                setTimeout(function () {
                    cart.removeClass('text-danger');
                }, 300);
                setTimeout(function () {
                    cart.addClass('text-danger');
                }, 600);
                setTimeout(function () {
                    cart.removeClass('text-danger');
                }, 900);
                setTimeout(function () {
                    cart.addClass('text-danger');
                }, 1200);
                setTimeout(function () {
                    cart.removeClass('text-danger');
                }, 1500);
            }
        }
    });
}

/*
* @Author: Domenico Santone
*
* Funzione per l'aggiunta di un prodotto ai preferiti
* @param {int} id - Id del prodotto da aggiungere
*/
function addToLiked(id, heart) {
    $.ajax({
        type: 'POST',
        url: '/products/' + id + '/like',
        data: {
            //Controllo se il prodotto è già nei preferiti o no
            'like': heart.hasClass('fa-heart') ? 0 : 1
        },
        success: function (data) {
            let response = JSON.parse(data);
            //Se l'aggiornamento ha successo eseguo lo switch del simbolo del cuore
            if (response['success'] === 'Like') {
                heart.removeClass('fa-heart-o').addClass('fa-heart');
            } else if (response['success'] === 'Dislike') {
                heart.removeClass('fa-heart').addClass('fa-heart-o');
            }
        }
    });
}

/*
* @Author: Domenico Santone
*
* Funzione per impostare le stelle del prodotto
* @param {Selector} stelle - Selettore di dove andranno messe le stelle
* @param {int} voto - Numero di stelle inserite
*/
function raty(stelle, voto) {
    stelle.raty({
        score: voto,
        readOnly: true,
        starOff: 'fa fa-star-o',
        starOn: 'fa fa-star text-warning',
        starHalf: 'fa fa-star-half-o text-warning',
        starType: 'i'
    });
}

/*
* @Author: Domenico Santone, Davide De Acetis, Raluca Mihaela Bujoreanu
*
* Funzione per la paginazione dei prodotti nella index
* @param {Selector} items - Collezione di tutti i prodotti
* @param {bool} filter - Indica se si sta chiamando la funzione per il filtro o no
*/
function paginate(items, filter = false) {
    let numItems = items.length;
    let perPage = 9;
    let pages = Math.ceil(numItems / perPage) + 1;
    let currentPage = 0;

    //svuota la pagina
    pagination.empty();

    if (filter) {   //se sto filtrando
        $('.product').empty();
        $.each(items, function () {
            $(this).show();
            $(this).css({'position': 'relative', 'top': '0', 'left': '0'});
            $('.product').append($(this));
        });
    } else {
        items.slice(perPage).hide();
    }

    //creo i link per la paginazione
    for (let i = 1; i < pages; i++) {
        if (i === 1) {
            pagination.append('<li class="active"><a href="#" id="da_attivare" class=" hvr-shutter-out-horizontal">' + i + '</a></li>');
        } else {
            pagination.append('<li><a href="#" class=" hvr-shutter-out-horizontal">' + i + '</a></li>');
        }
    }

    //aggiungo il listener al click dei link per caricare la giusta pagina
    $('.flat-pagination li').on('click', function () {
        let $this = $(this);
        $('li').removeClass('active');
        $this.addClass('active');
        currentPage = $this.index();
        let startItem = currentPage * perPage;
        let endItem = startItem + perPage;
        $(items).hide().slice(startItem, endItem).show().css({'position': 'relative', 'top': '', 'left': ''});
    });
}

//Funzione per la ricerca dei prodotti tramite le categorie
function filterCategories(items, categoria) {
    if (categoria === 'all') {
        return items;
    } else {
        return items.filter(function (element) {
            return $(element).find('input[name=categoria]').val() === categoria;
        });
    }
}

//Funzione per la ricerca dei prodotti tramite il prezzo
function filterPrice(items, price) {
    if (price === '0') {
        return items;
    } else {
        return items.filter(function (element) {
            return parseFloat($(element).find('span[class^="amount"]').text().slice(0, -1)) <= price;
        });
    }
}

//Funzione per l'ordinamento dei prodotti tramite prezzo o nome
function selectOrdine(items, ordine) {
    switch (ordine) {
        case 'prezzo-cresc':
            return orderByPrice(items);
        case 'prezzo-decr':
            return orderByPriceReverse(items);
        case 'nome-az':
            return orderByName(items);
        case 'nome-za':
            return orderByNameReverse(items);
        default:
            return items;
    }
}

function orderByPrice(items) {
    return [...items].sort(function (a, b) {
        return parseFloat($(a).find('span[class^="amount"]').text().replace('€', '')) - parseFloat($(b).find('span[class^="amount"]').text().replace('€', ''));
    });
}

function orderByPriceReverse(items) {
    return [...items].sort(function (a, b) {
        return parseFloat($(b).find('span[class^="amount"]').text().replace('€', '')) - parseFloat($(a).find('span[class^="amount"]').text().replace('€', ''));
    });
}

function orderByName(items) {
    return [...items].sort(function (a, b) {
        return $(a).find('span[class^="product-title"]').text().toLowerCase().localeCompare($(b).find('span[class^="product-title"]').text().toLowerCase());
    });
}

function orderByNameReverse(items) {
    return [...items].sort(function (a, b) {
        return $(b).find('span[class^="product-title"]').text().toLowerCase().localeCompare($(a).find('span[class^="product-title"]').text().toLowerCase());
    });
}

function applyFilter() {
    currentItems = selectOrdine(allItems, currentOrdine);
    currentItems = filterCategories(currentItems, currentCategoria);
    currentItems = filterPrice(currentItems, currentPrezzo);
    paginate(currentItems, true);
    $('#da_attivare').trigger('click');
}
