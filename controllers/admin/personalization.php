<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/template.inc.php';

function personalization(){
    global $mysqli;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_FILES["logo"])){
            $filename = basename($_FILES["logo"]["tmp_name"]). "." . substr($_FILES["logo"]["name"], strpos($_FILES["logo"]["name"], ".") + 1);
            move_uploaded_file($_FILES["logo"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . "/uploads/".$filename);
            unlink($_SERVER['DOCUMENT_ROOT'] . "/uploads/".$mysqli->query("SELECT logo FROM tdw_ecommerce.personalizzazione WHERE id = 1")->fetch_assoc()["logo"] ?? "");
        } else {
            $filename = $mysqli->query("SELECT logo FROM tdw_ecommerce.personalizzazione WHERE id = 1")->fetch_assoc()["logo"];
        }
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $country = $_POST['country'];
        $descrizione = $_POST['descrizione'];
        $titolo_descrizione = $_POST['titolo_descrizione'];
        $descrizione_estesa = $_POST['descrizione_estesa'];
        if (isset($_FILES["immagine_about"])){
            $filename_about = basename($_FILES["immagine_about"]["tmp_name"]). "." . substr($_FILES["immagine_about"]["name"], strpos($_FILES["immagine_about"]["name"], ".") + 1);
            move_uploaded_file($_FILES["immagine_about"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . "/uploads/".$filename_about);
            unlink($_SERVER['DOCUMENT_ROOT'] . "/uploads/".$mysqli->query("SELECT immagine_about FROM tdw_ecommerce.personalizzazione WHERE id = 1")->fetch_assoc()["immagine_about"] ?? "");
        } else {
            $filename_about = $mysqli->query("SELECT immagine_about FROM tdw_ecommerce.personalizzazione WHERE id = 1")->fetch_assoc()["immagine_about"];
        }
        $mysqli->query("UPDATE tdw_ecommerce.personalizzazione SET logo = '{$filename}', phone = '{$phone}', email = '{$email}', address = '{$address}', country = '{$country}', descrizione = '{$descrizione}', titolo_descrizione = '{$titolo_descrizione}', descrizione_estesa = '{$descrizione_estesa}', immagine_about = '{$filename_about}'");
        $response = array();
        if ($mysqli->affected_rows > 0) {
            $response['success'] = "Personalizzazione aggiornata con successo";
        } else if ($mysqli->affected_rows == 0) {
            $response['warning'] = "Nessuna personalizzazione aggiornata";
        } else {
            $response['error'] = "Errore nell'aggiornamento della personalizzazione";
        }
        exit(json_encode($response));

    } else {
        $main = setupMainAdmin();
        $personalizzazione = $mysqli->query("SELECT * FROM tdw_ecommerce.personalizzazione WHERE id = 1")->fetch_assoc();
        $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/personalization/personalization.html");
        foreach ($personalizzazione as $key => $value) {
            $body->setContent($key, $value);
        }
        $main->setContent("content", $body->get());
        $main->close();
    }
}