<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";


function show(): void
{
    global $mysqli;
    $main = setupMainUser();
    $main->setContent("title", "Il mio profilo");
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/account.html");
    $modal = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/modal.html");

    //IL MIO ACCOUNT
    $info = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/info.html");

    $user = $_SESSION['user'];
    foreach ($user as $key => $value) {
        $info->setContent($key, $value);
    }

    $body->setContent("info", $info->get());

    //I MIEI ORDINI
    $colnames = array(
        "Numero ordine",
        "Data",
        "Stato",
        "Totale",
        "Indirizzo di spedizione",
    );
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/table.html");
    foreach ($colnames as $colname) {
        $table->setContent("colname", $colname);
    }
    $specific_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/specific_tables/ordini.html");

    $oid = $mysqli->query("SELECT ordini.id, u.email as utente, ordini.data, ordini.stato, ordini.totale, ordini.numero_ordine, 
                                            CONCAT(isp.indirizzo,' ', isp.citta,' ', isp.cap,' ', isp.provincia,' ',isp.nazione) as indirizzo_spedizione,
                                            CONCAT(ifa.indirizzo,' ', ifa.citta,' ', ifa.cap,' ', ifa.provincia,' ', ifa.nazione) as indirizzo_fatturazione 
                                    FROM tdw_ecommerce.ordini 
                                        JOIN tdw_ecommerce.users as u on u.id=ordini.user_id 
                                        JOIN tdw_ecommerce.indirizzi as isp on isp.id= ordini.indirizzi_spedizione 
                                        JOIN tdw_ecommerce.indirizzi as ifa on ifa.id= ordini.indirizzi_fatturazione 
                                    WHERE ordini.user_id = {$user['id']}");
    do {
        $ordini = $oid->fetch_assoc();
        if ($ordini) {
            foreach ($ordini as $key => $value) {
                $specific_table->setContent($key, $value);
            }
        }
    } while ($ordini);
    $table->setContent("specific_table", $specific_table->get());
    $table->setContent("modal", $modal->get());
    $body->setContent("ordini", $table->get());

    //I MIEI INDIRIZZI
    $colnames = array(
        "Indirizzo",
        "Città",
        "CAP",
        "Provincia",
        "Nazione",
        "Azioni"
    );
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/table.html");
    $table->setContent("button", '<div class="pb-5"></div><a href="/addresses/0" class="themesflat-button outline ol-accent margin-top-table hvr-shutter-out-horizontal" style="font-size: 11px" type="submit" id="modifica_indirizzo">
    AGGIUNGI UN NUOVO INDIRIZZO </a></div>');
    foreach ($colnames as $colname) {
        $table->setContent("colname", $colname);
    }
    $specific_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/indirizzi.html");

    $oid = $mysqli->query("SELECT * FROM indirizzi WHERE users_id = {$user['id']} AND valido = 1");
    do {
        $indirizzi = $oid->fetch_assoc();
        if ($indirizzi) {
            foreach ($indirizzi as $key => $value) {
                $specific_table->setContent($key, $value);
            }
        }
    }while ($indirizzi);
    $table->setContent("specific_table", $specific_table->get());
    $body->setContent("indirizzi", $table->get());

    //LE MIE RECENSIONI
    $colnames = array(
        "Voto",
        "Commento",
        "Prodotto",
        "Azioni"
    );
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/table.html");
    foreach ($colnames as $colname) {
        $table->setContent("colname", $colname);
    }
    $specific_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/recensioni.html");
    $id = $_SESSION['user']['id'];
    $oid = $mysqli->query("SELECT r.id, r.voto, r.commento, r.prodotti_id as prodotto_id, p.nome as nome_prodotto FROM recensioni as r JOIN prodotti as p on p.id=r.prodotti_id WHERE users_id = '$id'");
    do {
        $recensioni = $oid->fetch_assoc();
        if ($recensioni) {
            foreach ($recensioni as $key => $value) {
                $specific_table->setContent($key, $value);
            }
        }
    }while ($recensioni);
    $table->setContent("specific_table", $specific_table->get());
    $body->setContent("recensioni", $table->get());


    //I MIEI PREFERITI
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
    $specific_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/preferiti.html");
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
    $body->setContent("preferiti", $table->get());


    $main->setContent("content", $body->get());
    $main->close();
}

function edit()
{
    global $mysqli;
    $nome = $_POST["nome"];
    $cognome = $_POST["cognome"];
    $email = $_POST["email"];
    $numero_telefono = $_POST["numero_telefono"];
    $email_u = $_SESSION['user']['email'];
    $response = array();
    if ($numero_telefono != "" && $nome != "" && $cognome != "" && $email != "") {
        $mysqli->query("UPDATE tdw_ecommerce.users SET nome = '$nome', cognome = '$cognome', email = '$email', email = '$email', telefono='$numero_telefono' WHERE email = '$email_u'");
        $_SESSION['user']['email'] = $email;
        if ($mysqli->affected_rows == 1) {
            $response['success'] = "Profilo modificato con successo";
        } elseif ($mysqli->affected_rows == 0) {
            $response['warning'] = "Nessun dato modificato";
        } else {
            $response['error'] = "Errore nella modifica del profilo";
        }
    } else {
        $response['error'] = "Errore nella modifica del profilo";
    }
    exit(json_encode($response));
}

function cambio_password()
{
    global $mysqli;
    $password_vecchia = $_POST["password_v"];
    $password_nuova = $_POST["password_n"];
    $password_nuova_conferma = $_POST["password_c"];
    $email_u = $_SESSION['user']['email'];
    $response = array();
    if ($password_nuova != "" && $password_nuova_conferma != "" && $password_vecchia != "") {
        $oid = $mysqli->query("SELECT * FROM tdw_ecommerce.users WHERE email = '$email_u'");
        $utente = $oid->fetch_assoc();
        if ($utente) {
            $password_vecchia = crypto($password_vecchia);
            if ($password_vecchia === $utente['password']) {
                if ($password_nuova == $password_nuova_conferma) {
                    $mysqli->query("UPDATE tdw_ecommerce.users SET password = '" . crypto($password_nuova) . "' WHERE email = '$email_u'");
                    if ($mysqli->affected_rows == 1) {
                        $response['success'] = "Password modificata con successo";
                    } elseif ($mysqli->affected_rows == 0) {
                        $response['warning'] = "Nessun dato modificato";
                    } else {
                        $response['error'] = "Errore nella modifica della password";
                    }
                } else {
                    $response['error'] = "Le password non corrispondono";
                }
            } else {
                $response['error'] = "La password vecchia non corrisponde";
            }
        } else {
            $response['error'] = "Errore nella modifica della password";
        }
    } else {
        $response['error'] = "Errore nella modifica della password";
    }
    exit(json_encode($response));

}

function crypto($pass): string
{
    return md5(md5($pass));
}
