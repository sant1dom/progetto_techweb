<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
// Mostro la pagina di una produttore con i suoi prodotti
function show(): void
{
    global $mysqli;
    $main = setupMainUser();
    $id = explode("/", $_SERVER['REQUEST_URI'])[2];

    $main->setContent("title", "Produttore");

    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/producers/show.html");
    $category = $mysqli->query("SELECT ragione_sociale, provenienza, telefono, email, sito_web FROM tdw_ecommerce.produttori WHERE id = $id");

    $category = $category->fetch_assoc();
    if ($category) {
        foreach ($category as $key => $value) {
            $body->setContent($key, $value);
        }
    }

    $oid = $mysqli->query("SELECT prodotti.id, prodotti.nome, prezzo, percentuale
                                                FROM tdw_ecommerce.prodotti
                                                 LEFT JOIN tdw_ecommerce.offerte o on prodotti.id = o.prodotti_id
                                                WHERE quantita_disponibile > 0 
                                                  AND produttori_id = $id
                                                ORDER BY prodotti.nome");

    $prodotto = $oid->fetch_assoc();
    while ($prodotto) {
        $image = $mysqli->query("SELECT nome_file as image FROM tdw_ecommerce.immagini JOIN tdw_ecommerce.prodotti p on immagini.prodotto_id = {$prodotto['id']} ORDER BY immagini.id LIMIT 1 ");
        $image = $image->fetch_assoc();
        if (!$image) {
            $prodotto['image'] = 'https://via.placeholder.com/500';
        } else {
            $prodotto['image'] = '/uploads/' . $image["image"];
        }
        $prodotto['percentuale'] = $prodotto['percentuale'] ? " <span class='sconto' id='percentuale{$prodotto['id']}'>-{$prodotto['percentuale']}%</span>" : "";
        foreach ($prodotto as $key => $value) {
            $body->setContent($key, $value);
        }
        if (isset($_SESSION["user"])) {
            $like = $mysqli->query("SELECT * FROM tdw_ecommerce.users_has_prodotti_preferiti WHERE users_id = " . $_SESSION["user"]["id"] . " AND prodotti_id = " . $prodotto["id"]);
            if ($like->num_rows == 0) {
                $body->setContent("like", "
                                <div class='add-cart'>
                                    <a class='like2 heart' data-id='{$prodotto["id"]}'><i class='fa fa-heart-o'></i></a>
                                </div>");
            } else {
                $body->setContent("like", "
                                <div class='add-cart'>
                                    <a class='like2 heart' data-id='{$prodotto["id"]}'><i class='fa fa-heart'></i></a>
                                </div>");
            }
        }
        $prodotto = $oid->fetch_assoc();
    }
    $main->setContent("content", $body->get());
    $main->close();

}