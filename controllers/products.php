<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

use Ds\Set;

function index()
{
    global $mysqli;

    $main = setupMainUser();
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/products/index.html");
    $title = "Prodotti";

    if (isset($_GET["search"])) {
        $search = $_GET["search"];
        $oid = $mysqli->query("
            SELECT prodotti.id, prodotti.nome, prezzo, c.nome as categoria, ragione_sociale as produttore, percentuale
            FROM tdw_ecommerce.prodotti
                     LEFT JOIN categorie c on prodotti.categorie_id = c.id
                     LEFT JOIN produttori p on p.id = prodotti.produttori_id
                     LEFT JOIN offerte o on prodotti.id = o.prodotti_id
            WHERE quantita_disponibile > 0 AND (prodotti.nome LIKE '%$search%' OR c.nome LIKE '%$search%' OR p.ragione_sociale LIKE '%$search%')");
    } else {
        $oid = $mysqli->query("
            SELECT prodotti.id, prodotti.nome, prezzo, c.nome as categoria, ragione_sociale as produttore, percentuale
            FROM tdw_ecommerce.prodotti
                     LEFT JOIN categorie c on prodotti.categorie_id = c.id
                     LEFT JOIN produttori p on p.id = prodotti.produttori_id
                     LEFT JOIN offerte o on prodotti.id = o.prodotti_id
            WHERE quantita_disponibile > 0");
    }

    $cat = array();

    do {
        $product = $oid->fetch_assoc();
        if ($product) {
            if (!isset($cat[$product["categoria"]])) {
                $cat[$product["categoria"]] = 1;
            } else {
                $cat[$product["categoria"]]++;
            }

            $image = $mysqli->query("SELECT nome_file as image FROM immagini JOIN prodotti p on immagini.prodotto_id = {$product['id']} LIMIT 1");
            $image = $image->fetch_assoc();
            if (!$image) {
                $product['image'] = 'https://via.placeholder.com/500';
            } else {
                $product['image'] = '/uploads/' . $image["image"];
            }
            $product['percentuale'] = $product['percentuale'] ? " <span class='sconto' id='percentuale{$product['id']}'>-{$product['percentuale']}%</span>" : "";
            foreach ($product as $key => $value) {
                $body->setContent($key, $value);
            }

            if (isset($_SESSION["user"])) {
                $like = $mysqli->query("SELECT * FROM users_has_prodotti_preferiti WHERE users_id = " . $_SESSION["user"]["id"] . " AND prodotti_id = " . $product["id"]);
                if ($like->num_rows == 0) {
                    $body->setContent("like", "
                                <div class='add-cart'>
                                    <a class='like2'><i class='fa fa-heart-o'></i></a>
                                </div>");
                } else {
                    $body->setContent("like", "
                                <div class='add-cart'>
                                    <a class='like2'><i class='fa fa-heart'></i></a>
                                </div>");
                }
            }
        }
    } while ($product);

    debug_to_console($cat);


    foreach ($cat as $key => $value) {
        $body->setContent("categoria_name", $key);
        $body->setContent("counter", $value);
    }

    $main->setContent("content", $body->get());
    $main->setContent("title", $title);
    $main->close();
}

function show()
{
    global $mysqli;
    $main = setupMainUser();
    $main->setContent("title", "Dettaglio Prodotto");
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/products/show.html");
    $likebutton = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/products/likebutton.html");
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
        header("Location: /products");
    } else {
        $prodotto = $prodotto->fetch_assoc();
        foreach ($prodotto as $key => $value) {
            if ($key !== "id") {
                $body->setContent($key, $value);
            } else {
                $likebutton->setContent($key, $value);
                $body->setContent($key, $value);
            }
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

    $immagine = $immagini->fetch_assoc();
    if (!$immagine) {
        $body->setContent('nome_file', "https://via.placeholder.com/500");
    } else {
        while ($immagine) {
            foreach ($immagine as $key => $value) {
                $body->setContent($key, "/uploads/" . $value);
            }
            $immagine = $immagini->fetch_assoc();
        }
    }

    if (isset($_SESSION['user'])) {
        $like = $mysqli->query("SELECT * 
                             FROM tdw_ecommerce.users_has_prodotti_preferiti 
                                WHERE users_id = " . $_SESSION['user']["id"] . " 
                                    AND prodotti_id = $id");
        if ($like->num_rows == 0) {
            $likebutton->setContent("start_value", "fa fa-heart-o");
        } else {
            $likebutton->setContent("start_value", "fa fa-heart");
        }
        $body->setContent("like", $likebutton->get());
    } else {
        $body->setContent("like", "");
    }
    //Controllo per vedere se un utente può effettuare una recensione
    if (isset($_SESSION['user']['email'])){
        $utente= $mysqli->query("SELECT id as id_utente
                                    FROM tdw_ecommerce.users 
                                    WHERE email = '".$_SESSION['user']['email']."'");
        $utente = $utente->fetch_assoc();
        $recensione= $mysqli->query("SELECT * 
                                    FROM tdw_ecommerce.ordini as o JOIN ordini_has_prodotti as op on o.id=op.ordini_id
                                    WHERE o.stato='CONSEGNATO' AND o.user_id = ".$utente['id_utente']." AND op.prodotti_id = '$prodotto[id]'");
        if ($recensione->num_rows == 0) {
            $body->setContent("recensione", "false");
        } else {
            $body->setContent("recensione", "true");
        }

    }
    $main->setContent("content", $body->get());
    $main->close();
}