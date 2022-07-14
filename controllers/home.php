<?php

require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
require "include/dbms.inc.php";


function home(): void
{
    global $mysqli;
    $main = setupMainUser();
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/home.html");

    $personalizzazione = $mysqli->query("SELECT titolo_descrizione, descrizione, immagine_about FROM personalizzazione WHERE id = 1")->fetch_assoc();
    if ($personalizzazione) {
        $body->setContent("titolo_descrizione", $personalizzazione["titolo_descrizione"]);
        $body->setContent("descrizione", $personalizzazione["descrizione"]);
        if ($personalizzazione["immagine_about"] != "") {
            $body->setContent("immagine_about", "/uploads/".$personalizzazione["immagine_about"]);
        } else {
            $body->setContent("immagine_about", "https://via.placeholder.com/500");
        }
    }

    foreach ($personalizzazione as $key => $value) {
        if ($key == "immagine_about" && $value != "") {
            $value = "/uploads/" . $value;
        } else {
            $value = "https://via.placeholder.com/500";
        }
        $body->setContent($key, $value);
    }

    $oid = $mysqli->query("SELECT prodotti.id, prodotti.nome, prodotti.prezzo, o.percentuale as sconto
                                FROM prodotti 
                                LEFT JOIN offerte o on prodotti.id = o.prodotti_id
                                WHERE prodotti.quantita_disponibile > 0 
                                ORDER BY data_inserimento DESC, prodotti.id  
                                LIMIT 10");

    do {
        $prodotto = $oid->fetch_assoc();
        if ($prodotto) {
            $prodotto['sconto'] = $prodotto['sconto'] ? " <span class='sconto' id='new_percentuale{$prodotto['id']}'>-{$prodotto['sconto']}%</span>" : "";

            $image = $mysqli->query("SELECT nome_file FROM immagini WHERE immagini.prodotto_id = {$prodotto['id']} LIMIT 1");
            $image = $image->fetch_assoc();
            if (!$image) {
                $image = 'https://via.placeholder.com/500';
            } else {
                $image = '/uploads/' . $image["nome_file"];
            }
            $body->setContent('image', $image);

            if (isset($_SESSION["user"])) {
                $like = $mysqli->query("SELECT * FROM users_has_prodotti_preferiti WHERE users_id = " . $_SESSION["user"]["id"] . " AND prodotti_id = " . $prodotto["id"]);
                if ($like->num_rows == 0) {
                    $body->setContent("like", "
                                <div class='add-cart'>
                                    <a class='like2 heart'><i class='fa fa-heart-o' data-id='{$prodotto["id"]}'></i></a>
                                </div>");
                } else {
                    $body->setContent("like", "
                                <div class='add-cart'>
                                    <a class='like2 heart'><i class='fa fa-heart' data-id='{$prodotto["id"]}'></i></a>
                                </div>");
                }
            }
            foreach ($prodotto as $key => $value) {
                $body->setContent($key, $value);
            }
        }
    } while ($prodotto);

    $oid = $mysqli->query("SELECT prodotti.id as top_id, prodotti.nome as top_nome, prodotti.prezzo as top_prezzo, ROUND(AVG(voto), 1) as voto_medio, 
                                        COUNT(*) as numero, percentuale as top_sconto
                                    FROM prodotti
                                             JOIN recensioni r on prodotti.id = r.prodotti_id
                                             LEFT JOIN offerte o on prodotti.id = o.prodotti_id
                                    WHERE prodotti.quantita_disponibile > 0
                                    GROUP BY prodotti.id, prodotti.nome, prodotti.prezzo
                                    ORDER BY voto_medio DESC, numero DESC, prodotti.nome
                                    LIMIT 10");

    do {
        $prodotto = $oid->fetch_assoc();
        if ($prodotto) {
            $prodotto['top_sconto'] = $prodotto['top_sconto'] ? " <span class='sconto' id='rec_percentuale{$prodotto['top_id']}'>-{$prodotto['top_sconto']}%</span>" : "";

            $image = $mysqli->query("SELECT nome_file FROM immagini WHERE immagini.prodotto_id = {$prodotto['top_id']} LIMIT 1");
            $image = $image->fetch_assoc();
            if (!$image) {
                $image = 'https://via.placeholder.com/500';
            } else {
                $image = '/uploads/' . $image["nome_file"];
            }
            $body->setContent('top_image', $image);

            if (isset($_SESSION["user"])) {
                $like = $mysqli->query("SELECT * FROM users_has_prodotti_preferiti WHERE users_id = " . $_SESSION["user"]["id"] . " AND prodotti_id = " . $prodotto["top_id"]);
                if ($like->num_rows == 0) {
                    $body->setContent("top_like", "
                                <div class='add-cart'>
                                    <a class='like2 heart'><i class='fa fa-heart-o' data-id='{$prodotto["top_id"]}'></i></a>
                                </div>");
                } else {
                    $body->setContent("top_like", "
                                <div class='add-cart'>
                                    <a class='like2 heart'><i class='fa fa-heart  data-id='{$prodotto["top_id"]}'></i></a>
                                </div>");
                }
            }
            foreach ($prodotto as $key => $value) {
                $body->setContent($key, $value);
            }
        }
    } while ($prodotto);

    $main->setContent("title", "HOME");
    $main->setContent("content", $body->get());
    $main->close();
}
