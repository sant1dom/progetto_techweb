<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";


function show(){
    global $mysqli;
    $main = setupMainUser();
    $main->setContent("title", "Il mio profilo");
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
    $content=new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/personal_data.html");
    $email= $_SESSION['user']['email'];
    $oid = $mysqli->query("SELECT * FROM tdw_ecommerce.users WHERE email = '$email'");
    $utente= $oid->fetch_assoc();
    if($utente){
        foreach($utente as $key => $value){
            $content->setContent($key, $value);
        }
    }
    $body->setContent("content", $content->get());
    $main->setContent("content", $body->get());
    $main->close();
}
function edit(){
    global $mysqli;
    $nome = $_POST["nome"];
    $cognome = $_POST["cognome"];
    $email = $_POST["email"];
    $numero_telefono = $_POST["numero_telefono"];
    $email_u= $_SESSION['user']['email'];
    $response = array();
    if ($numero_telefono!= "" && $nome != "" && $cognome != "" && $email != "") {
        $mysqli->query("UPDATE tdw_ecommerce.users SET nome = '$nome', cognome = '$cognome', email = '$email', email = '$email' WHERE email = '$email_u'");
        $_SESSION['user']['email'] = $email;
        if($mysqli->affected_rows == 1){
            $response['success'] = "Profilo modificato con successo";
        } elseif($mysqli->affected_rows == 0) {
            $response['warning'] = "Nessun dato modificato";
        } else {
            $response['error'] = "Errore nella modifica del profilo";
        }
    } else {
        $response['error'] = "Errore nella modifica del profilo";
    }
    exit(json_encode($response));
}
function cambio_password(){
    global $mysqli;
    $password_vecchia = $_POST["password_vecchia"];
    $password_nuova = $_POST["password_nuova"];
    $password_nuova_conferma = $_POST["password_nuova_conferma"];
    $email_u= $_SESSION['user']['email'];
    $response = array();
    if ($password_nuova != "" && $password_nuova_conferma != "" && $password_vecchia != "") {
        $oid = $mysqli->query("SELECT * FROM tdw_ecommerce.users WHERE email = '$email_u'");
        $utente= $oid->fetch_assoc();
        if($utente){
            if(password_verify($password_vecchia, $utente['password'])){
                if($password_nuova == $password_nuova_conferma){
                    $mysqli->query("UPDATE tdw_ecommerce.users SET password = '" . password_hash($password_nuova, PASSWORD_DEFAULT) . "' WHERE email = '$email_u'");
                    if($mysqli->affected_rows == 1){
                        $response['success'] = "Password modificata con successo";
                    } elseif($mysqli->affected_rows == 0) {
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
