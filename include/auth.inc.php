<?php

const ERROR_SCRIPT_PERMISSION = 100;
const ERROR_USER_NOT_LOGGED = 200;
const ERROR_OWNERSHIP = 200;

function crypto($pass): string
{
    return md5(md5($pass));
}

function isOwner($resource, $key = "id"): bool
{
    global $mysqli;

    //Controlla se il possessore della username esiste
    $oid = $mysqli->query("SELECT * FROM {$resource} WHERE {$key} = '{$_REQUEST[$key]}'");

    //Se l'utente non viene trovato
    if (!$oid) {
        return false;
    }

    $data = $oid->fetch_assoc();

    if ($data['email'] != $_SESSION['user']['email']) {
        Header("Location: error.php?code=" . ERROR_OWNERSHIP);
        exit;
    } else {
        return true;
    }
}

/**
 * Esegue la login di un utente.
 * @return void
 */
function doLogin(): void
{
    global $mysqli;

    //Se la post contiene email e password
    if (isset($_POST['email']) and isset($_POST['password'])) {

        //Ottiene l'utente dalla tabella users
        $oid = $mysqli->query("
            SELECT id, nome, cognome, email, telefono
            FROM users 
            WHERE email = '" . $_POST['email'] . "'
            AND password = '" . crypto($_POST['password']) . "'");


        //Se oid non è settato allora c'è un errore
        if (!$oid) {
            trigger_error("Generic error, level 21", E_USER_ERROR);
        }

        //Se viene restituito un numero di righe maggiore di 0 allora l'utente esiste
        if ($oid->num_rows > 0) {
            //Ottiene i dati dell'utente
            $user = $oid->fetch_assoc();
            createSession($user, $mysqli);
        }
    }
}

/**
 * Esegue la registrazione di un nuovo utente
 * @return void
 */
function doRegister(): void
{
    global $mysqli;

    //Se la post contiene tutti i campi
    if (isset($_POST['nome']) and
        isset($_POST['cognome']) and
        isset($_POST['email']) and
        isset($_POST['password']) and
        isset($_POST['telefono'])) {

        //Controlla se l'email è già presente
        $oid = $mysqli->query("SELECT id FROM users WHERE email = '{$_POST['email']}'");
        if (!$oid) {
            trigger_error("Generic error, level 21", E_USER_ERROR);
        }
        //Se oid non è settato allora un utente con questa email esiste già
        if ($oid->num_rows > 0) {
            return;
        } else {
            //Inserisce l'utente nel database
            $oid = $mysqli->query("INSERT INTO users (nome, cognome, email, password, telefono) 
                VALUES ('" . $_POST['nome'] . "', 
                '" . $_POST['cognome'] . "',
                '" . $_POST['email'] . "',
                '" . crypto($_POST['password']) . "',
                '" . $_POST['telefono'] . "')");

            if (!$oid) {
                trigger_error("Generic error, level 21", E_USER_ERROR);
            }

            $oid = $mysqli->query("SELECT id, nome, cognome, email, telefono FROM users WHERE email = '{$_POST['email']}'");

            if (!$oid) {
                trigger_error("Generic error, level 21", E_USER_ERROR);
            }

            if ($oid->num_rows > 0) {
                //Ottiene i dati dell'utente
                $user = $oid->fetch_assoc();
                //Da all'utente i permessi da user
                $oid = $mysqli->query("INSERT INTO users_has_groups (users_id, groups_id) VALUES ({$user['id']}, 2);");
                if (!$oid) {
                    trigger_error("Generic error, level 21", E_USER_ERROR);
                }
                createSession($user, $mysqli);
            }
        }
    }
}


/**
 * Crea una sessione per l'utente, usato sia per la login che per la registrazione
 * @param $user
 * @param mysqli $mysqli
 * @return void
 */
function createSession($user, mysqli $mysqli): void
{
    //Crea una sessione per l'utente
    $_SESSION['auth'] = true;
    $_SESSION['user'] = $user;


    //Ottiene i permessi dell'utente
    $oid = $mysqli->query("
                SELECT DISTINCT script, url FROM users 
                LEFT JOIN users_has_groups ON users_has_groups.users_id = users.id
                LEFT JOIN services_has_groups ON services_has_groups.groups_id = users_has_groups.groups_id 
                LEFT JOIN services
                ON services.id = services_has_groups.services_id
                WHERE email = '" . $_POST['email'] . "'");

    //Se oid non è settato allora c'è un errore
    if (!$oid) {
        trigger_error("Generic error, level 40", E_USER_ERROR);
    }

    //Imposta i permessi di accesso dell'utente
    do {
        $data = $oid->fetch_assoc();
        if ($data) {
            $scripts[$data['url']] = true;
        }
    } while ($data);

    $_SESSION['user']['script'] = $scripts;
    //if (!isset($_SESSION['user']['script'][basename($_SERVER['SCRIPT_NAME'])])) {
    if (!isset($_SESSION['user']['script'])) {
        unset($_SESSION['auth']);
        unset($_SESSION['user']);
    }
}
?>