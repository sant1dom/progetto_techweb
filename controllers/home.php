<?php

require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
require "include/dbms.inc.php";


function home(): void
{
    global $mysqli;
    $main = setupMainUser();
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/home.html");

    $oid = $mysqli->query("SELECT id, nome, prezzo
                                FROM prodotti 
                                WHERE prodotti.quantita_disponibile > 0 
                                ORDER BY data_inserimento DESC, prodotti.id  
                                LIMIT 10");

    do {
        $prodotto = $oid->fetch_assoc();
        if ($prodotto) {
            $image = $mysqli->query("SELECT nome_file FROM immagini WHERE immagini.prodotto_id = {$prodotto['id']} LIMIT 1");
            $image = $image->fetch_assoc();
            if (!$image) {
                $image = 'https://via.placeholder.com/500';
            } else {
                $image = '/uploads/' . $image["nome_file"];
            }
            $body->setContent('image', $image);
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
            $image = $mysqli->query("SELECT nome_file FROM immagini WHERE immagini.prodotto_id = {$prodotto['top_id']} LIMIT 1");
            $image = $image->fetch_assoc();
            if (!$image) {
                $image = 'https://via.placeholder.com/500';
            } else {
                $image = '/uploads/' . $image["nome_file"];
            }
            $body->setContent('top_image', $image);
            foreach ($prodotto as $key => $value) {
                $body->setContent($key, $value);
            }
        }
    } while ($prodotto);

    $main->setContent("title", "HOME");
    $main->setContent("content", $body->get());
    $main->close();
}
