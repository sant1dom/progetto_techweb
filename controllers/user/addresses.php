<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
function index()
{
    global $mysqli;
    $main = setupMainUser();
    $main->setContent("title", "I miei indirizzi");
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
    $colnames = array(
        "Indirizzo",
        "Città",
        "CAP",
        "Provincia",
        "Nazione",
    );
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/table.html");
    $table->setContent("button", '<div class="pb-5"></div><a href="/addresses/0" class="themesflat-button outline ol-accent margin-top-table hvr-shutter-out-horizontal" style="font-size: 11px" type="submit" id="modifica_indirizzo">
    AGGIUNGI UN NUOVO INDIRIZZO </a></div>');
    foreach ($colnames as $colname) {
        $table->setContent("colname", $colname);
    }
    $specific_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/specific_tables/indirizzi_utente.html");
    $email = $_SESSION['user']['email'];
    $oid = $mysqli->query("SELECT id FROM users WHERE email = '$email'");
    $utente = $oid->fetch_assoc();
    $oid = $mysqli->query("SELECT * FROM indirizzi WHERE users_id = '$utente[id]'");
    do {
        $indirizzi = $oid->fetch_assoc();
        if ($indirizzi) {
            foreach ($indirizzi as $key => $value) {
                $specific_table->setContent($key, $value);
            }
        }
    }while ($indirizzi);
    $table->setContent("specific_table", $specific_table->get());
    $body->setContent("content", $table->get());
    $main->setContent("content", $body->get());
    $main->close();
}
function show(){
    global $mysqli;
    $main = setupMainUser();
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
    if($id!=0) {
        $main->setContent("title", "Modifica indirizzo");
        $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/addresses/form_indirizzo.html");
        $email = $_SESSION['user']['email'];
        $oid = $mysqli->query("SELECT id, indirizzo, citta, cap, provincia,nazione FROM tdw_ecommerce.indirizzi WHERE id = '$id'");
        do {
            $indirizzo = $oid->fetch_assoc();
            if ($indirizzo) {
                foreach ($indirizzo as $key => $value) {
                    $show->setContent($key, $value);
                }
            }
        } while ($indirizzo);
        $controllo = "modifica indirizzo";
        $show->setContent("controllo", $controllo);
        $body->setContent("content", $show->get());
        $main->setContent("content", $body->get());
        $main->close();
    }else{
        $main->setContent("title", "Crea indirizzo");
        $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/addresses/form_indirizzo.html");
        $email = $_SESSION['user']['email'];
        $oid= $mysqli->query("SELECT id as id_utente FROM users WHERE email = '$email'");
        $utente = $oid->fetch_assoc();
        $show->setContent("id_utente", $utente['id_utente']);
        $controllo = "nuovo indirizzo";
        $show->setContent("controllo", $controllo);
        $body->setContent("content", $show->get());
        $main->setContent("content", $body->get());
        $main->close();
    }
}
function edit(){
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
    $indirizzo= $_POST['indirizzo'];
    $citta= $_POST['citta'];
    $cap= $_POST['cap'];
    $provincia= $_POST['provincia'];
    $nazione= $_POST['nazione'];
    $mysqli->query("UPDATE tdw_ecommerce.indirizzi SET indirizzo = '$indirizzo', citta = '$citta', cap = '$cap', provincia = '$provincia', nazione = '$nazione' WHERE id = '$id'");
    if($mysqli->affected_rows == 1){
        $response['success'] = "Indirizzo modificato con successo";
    } elseif($mysqli->affected_rows == 0) {
        $response['warning'] = "Nessun dato modificato";
    } else {
        $response['error'] = "Errore nella modifica dell'indirizzo";
    }
    exit(json_encode($response));
}
function create(){
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
    $indirizzo= $_POST['indirizzo'];
    $citta= $_POST['citta'];
    $cap= $_POST['cap'];
    $provincia= $_POST['provincia'];
    $nazione= $_POST['nazione'];
    $email = $_SESSION['user']['email'];
    $oid = $mysqli->query("SELECT id FROM users WHERE email = '$email'");
    $utente = $oid->fetch_assoc();
    $mysqli->query("INSERT INTO tdw_ecommerce.indirizzi SET indirizzo = '$indirizzo', citta = '$citta', cap = '$cap', provincia = '$provincia', nazione = '$nazione', users_id = '$utente[id]'");
    if($mysqli->affected_rows == 1){
        $response['success'] = "Indirizzo aggiunto con successo";
    } elseif($mysqli->affected_rows == 0) {
        $response['warning'] = "Nessun dato aggiunto";
    } else {
        $response['error'] = "Errore nell'aggiunta dell'indirizzo";
    }
    exit(json_encode($response));

}