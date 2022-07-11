<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function like() {
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
    $user = $_SESSION['user']['id'];
    $response = array();
    if( $_POST['like'] === "0") {
        $mysqli->query("DELETE FROM tdw_ecommerce.users_has_prodotti_preferiti WHERE users_id = $user AND prodotti_id = $id");
        $response['success'] = 'Dislike';
    } else {
        $mysqli->query("INSERT INTO tdw_ecommerce.users_has_prodotti_preferiti (users_id, prodotti_id) VALUES ($user, $id)");
        $response['success'] = 'Like';
    }
    exit(json_encode($response));
}

function show() {
    global $mysqli;
    $main = setupMainUser();
    $main->setContent("title", "I miei prodotti preferiti");
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/table.html");
    $colnames = array(
        "Nome",
        "Prezzo",
        "Disponibilità",
        "Categoria",
        "Produttore",
        "Provenienza",
        "Like"
    );
    foreach ($colnames as $colname) {
        $table->setContent("colname", $colname);
    }
    $specific_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/specific_tables/likes.html");
    $oid = $mysqli->query("SELECT prodotti.id, prodotti.nome, prezzo, quantita_disponibile, ragione_sociale as produttore, nazione, regione, c.nome as categoria
                            FROM tdw_ecommerce.prodotti 
                                JOIN tdw_ecommerce.categorie c on c.id = prodotti.categorie_id 
                                JOIN tdw_ecommerce.produttori p on p.id = prodotti.produttori_id 
                                JOIN tdw_ecommerce.provenienze p2 on p2.id = prodotti.provenienze_id 
                            WHERE prodotti.id IN 
                                  (SELECT prodotti_id 
                                    FROM tdw_ecommerce.users_has_prodotti_preferiti 
                                    WHERE users_id = " . $_SESSION['user']['id'] . ")");
    do {
        $prodotto = $oid->fetch_assoc();
        if ($prodotto) {
            foreach ($prodotto as $key => $value) {
                $specific_table->setContent($key, $value);
            }
            $specific_table->setContent("provenienza", $prodotto['nazione'] . " " . $prodotto['regione']);
            $specific_table->setContent("disponibilità", $prodotto['quantita_disponibile'] > 0 ? "Disponibile" : "Non disponibile");
        }
    } while ($prodotto);
    $table->setContent("specific_table", $specific_table->get());
    $body->setContent("content", $table->get());
    $main->setContent("content", $body->get());
    $main->setContent("user", $_SESSION['user']);
    $main->close();
}