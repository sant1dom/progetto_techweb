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
function show(){
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
    $ordine= $mysqli-> query("SELECT ordini.id, u.email as email_utente, u.nome as nome_utente, u.cognome as cognome_utente, u.telefono as utente_telefono, m.numero_carta as carta_utente, m.nome_proprietario as nome_carta,
       m.scadenza_carta as scadenza_carta, m.cvv as cvv, ordini.data, ordini.stato, ordini.totale, ordini.numero_ordine, ordini.motivazione, CONCAT(isp.indirizzo,' ', isp.citta,' ', isp.cap,' ', isp.provincia,' ',isp.nazione) as indirizzo_spedizione,
        CONCAT(ifa.indirizzo,' ', ifa.citta,' ', ifa.cap,' ', ifa.provincia,' ', ifa.nazione) as indirizzo_fatturazione FROM tdw_ecommerce.ordini 
            JOIN tdw_ecommerce.users as u on u.id=ordini.user_id JOIN tdw_ecommerce.indirizzi as isp on isp.id= ordini.indirizzi_spedizione JOIN tdw_ecommerce.indirizzi as ifa on ifa.id= ordini.indirizzi_fatturazione
            JOIN tdw_ecommerce.metodi_pagamento as m on m.id=ordini.metodi_pagamento WHERE ordini.id=".$id);
    $ordine = $ordine->fetch_assoc();
    $main = setupMainUser();
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
    $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/orders/show_ordine.html");
    foreach ($ordine as $key => $value) {
        $show->setContent($key, $value);
    }
    $table_prodotti= new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/specific_tables/prodotti_ordine.html");
    $oid = $mysqli->query("SELECT p.nome as nome_prodotto,p.id as id, p.prezzo as prezzo_prodotto, op.quantita as quantita_prodotto FROM tdw_ecommerce.ordini_has_prodotti as op JOIN tdw_ecommerce.prodotti as p on p.id=op.prodotti_id WHERE op.ordini_id=".$id);
    do {
        $prodotti = $oid->fetch_assoc();
        if ($prodotti) {
            foreach ($prodotti as $key => $value) {
                $table_prodotti->setContent($key, $value);
            }
        }
    } while ($prodotti);
    $show->setContent("table_prodotti", $table_prodotti->get());
    $body->setContent("content", $show->get());
    $main->setContent("title", "Ordine n°" . $ordine['numero_ordine']);
    $main->setContent("content", $body->get());
    $main->close();
}
function annulla(){
    global $mysqli;
    $id =$_POST['id'];
    $mysqli->query("UPDATE tdw_ecommerce.ordini SET stato='ANNULLATO', motivazione='Annullamento da parte del utente' WHERE id=".$id);
    $oid= $mysqli-> query("SELECT p.id as id_prodotto, op.quantita as quantita_prodotto FROM tdw_ecommerce.ordini_has_prodotti as op JOIN tdw_ecommerce.prodotti as p on p.id=op.prodotti_id WHERE op.ordini_id=".$id);
    $prodotti = $oid->fetch_assoc();
    if($prodotti){
        do {
            $mysqli->query("UPDATE tdw_ecommerce.prodotti SET quantita_disponibile=quantita_disponibile+".$prodotti['quantita_prodotto']." WHERE id=".$prodotti['id_prodotto']);
        } while ($prodotti = $oid->fetch_assoc());
    }
}