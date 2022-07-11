<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function index() {
    $main = setupMainUser();
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/products/index.html");
    $title = "Prodotti";
}

function show(){
    global $mysqli;
    $main = setupMainUser();
    $main->setContent("title", "Dettaglio Prodotto");
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/products/show.html");

    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
    $prodotto = $mysqli->query("SELECT prodotti.id,
                                           prodotti.nome as nome_prodotto,
                                           prezzo,
                                           dimensione,
                                           quantita_disponibile,
                                           descrizione,
                                           p.ragione_sociale as produttore,
                                           p2.nazione,
                                           p2.regione,
                                           c.nome as categoria
                                    FROM tdw_ecommerce.prodotti
                                        JOIN tdw_ecommerce.produttori p on p.id = prodotti.produttori_id
                                        JOIN tdw_ecommerce.provenienze p2 on prodotti.provenienze_id = p2.id
                                        JOIN tdw_ecommerce.categorie c on c.id = prodotti.categorie_id
                                    WHERE prodotti.id = $id;");

    if ($prodotto->num_rows == 0) {
        header("Location: /user/products");
    } else {
        $prodotto = $prodotto->fetch_assoc();
        foreach ($prodotto as $key => $value) {
            $body->setContent($key, $value);
        }
        $body->setContent("disponibilita", $prodotto['quantita_disponibile'] > 0 ? "Disponibile" : "Non disponibile");
    }
    // Controlla se il prodotto è in offerta
    $offerta = $mysqli->query("SELECT percentuale, data_inizio, data_fine
                                    FROM tdw_ecommerce.offerte
                                    WHERE prodotti_id = $id AND data_fine >= NOW() AND data_inizio <= NOW()");
    $offerta = $offerta->fetch_assoc();
    if ($offerta) {
        $body->setContent("percentuale", $offerta['percentuale']);
        $body->setContent("data_fine", $offerta['data_fine']);
    }
    // Per le recensioni del prodotto
    $recensioni = $mysqli->query("SELECT ROUND(AVG(voto),1) as voto_medio, COUNT(*) as numero
                                    FROM tdw_ecommerce.recensioni
                                    WHERE prodotti_id = $id");
    $recensioni = $recensioni->fetch_assoc();
    if ($recensioni) {
        $body->setContent("voto_medio", $recensioni['voto_medio']);
        $body->setContent("numero_recensioni", $recensioni['numero']);
    }

    // Per le recensioni nella tabella
    $recensioni = $mysqli->query("SELECT voto, commento, nome, cognome
                                    FROM tdw_ecommerce.recensioni 
                                        JOIN tdw_ecommerce.users u on u.id = recensioni.users_id
                                    WHERE prodotti_id = $id");
    do {
        $recensione = $recensioni->fetch_assoc();
        if ($recensione) {
            foreach ($recensione as $key => $value) {
                $body->setContent($key, $value);
            }
        }
    } while ($recensione);
    $immagini = $mysqli->query("SELECT nome_file
                                    FROM tdw_ecommerce.immagini
                                    WHERE prodotto_id = $id");
    do {
        $immagine = $immagini->fetch_assoc();
        if ($immagine) {
            foreach ($immagine as $key => $value) {
                $body->setContent($key, "/uploads/".$value);
            }
        }
    } while ($immagine);

    $main->setContent("content", $body->get());
    $main->close();
}
