<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
function index()
{
    global $mysqli;
    $main = setupMainUser();
    $main->setContent("title", "I miei ordini");
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
    $colnames = array(
        "Numero ordine",
        "Data",
        "Stato",
        "Totale",
        "Indirizzo di spedizione",
        "Indirizzo di fatturazione"
    );
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/table.html");
    foreach ($colnames as $colname) {
        $table->setContent("colname", $colname);
    }
    $specific_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/specific_tables/ordini_utente.html");
    $email = $_SESSION['user']['email'];
    $oid = $mysqli->query("SELECT id FROM users WHERE email = '$email'");
    $utente = $oid->fetch_assoc();
    $oid = $mysqli->query("SELECT ordini.id, u.email as utente, ordini.data, ordini.stato, ordini.totale, ordini.numero_ordine, CONCAT(isp.indirizzo,' ', isp.citta,' ', isp.cap,' ', isp.provincia,' ',isp.nazione) as indirizzo_spedizione,
    CONCAT(ifa.indirizzo,' ', ifa.citta,' ', ifa.cap,' ', ifa.provincia,' ', ifa.nazione) as indirizzo_fatturazione FROM tdw_ecommerce.ordini JOIN tdw_ecommerce.users as u on u.id=ordini.user_id JOIN tdw_ecommerce.indirizzi as isp on isp.id= ordini.indirizzi_spedizione JOIN tdw_ecommerce.indirizzi as ifa on ifa.id= ordini.indirizzi_fatturazione WHERE ordini.user_id = '$utente[id]'");
   do {
       $ordini = $oid->fetch_assoc();
       if ($ordini) {
           foreach ($ordini as $key => $value) {
               $specific_table->setContent($key, $value);
           }
       }
   }while ($ordini);
    $table->setContent("specific_table", $specific_table->get());
    $body->setContent("content", $table->get());
    $main->setContent("content", $body->get());
    $main->close();
}