<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function index(){
    global $mysqli;
    $colnames = array(
        "Nome",
        "Cognome",
        "Email",
        "Telefono",
        "Gruppi",
        "Status"
    );

    $oid = $mysqli->query("SELECT id, nome, ban, cognome, email, telefono FROM tdw_ecommerce.users");
    $main = setupMainAdmin();
    // Creazione del contenuto
    $crud = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/crud.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/table.html");
        // Riempimento della tabella
    $table->setContent("title", "Utenti");
    foreach ($colnames as $value) {
        $table->setContent("colname", $value);
    }
    $utenti_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/specific_tables/utenti.html");
    do {
        $users = $oid->fetch_assoc();
        if ($users) {
            foreach ($users as $key => $value) {
                $utenti_table->setContent($key, $value);
            }

            //Ottiene tutti i gruppi dell'utente e li inserisce nella tabella
            $groups = $mysqli->query("SELECT group_name FROM tdw_ecommerce.`groups`
                                                JOIN users_has_groups uhg on `groups`.id = uhg.groups_id
                                                JOIN users u on uhg.users_id = u.id
                                                WHERE u.id = " . $users["id"]);
            $groups_string = "";
            do {
                $group_result = $groups->fetch_assoc();
                if ($group_result) {
                    $groups_string = $groups_string . $group_result["group_name"] . ", "; //concatena i gruppi
                }
            } while ($group_result);
            $groups_string = substr($groups_string, 0, -2); //rimuove virgola finale
            $utenti_table->setContent("groups", $groups_string);
        }
    } while ($users);
    $table->setContent("sptable", $utenti_table->get());
    $crud->setContent("table", $table->get());
    $main->setContent("content", $crud->get());
    $main->close();
}

function show(){
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $utente = $mysqli->query("SELECT * FROM tdw_ecommerce.users WHERE id = $id");
    if ($utente->num_rows == 0) {
        header("Location: /admin/users");
    } else {
        $utente = $utente->fetch_assoc();
        $main = setupMainAdmin();
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/users/show.html");
        foreach ($utente as $key => $value) {
            $show->setContent($key, $value);
        }

        $groups = $mysqli->query("SELECT `groups`.id, group_name FROM tdw_ecommerce.`groups`");
        $user_groups = $mysqli->query("SELECT group_name FROM tdw_ecommerce.`groups`
                                                JOIN users_has_groups uhg on `groups`.id = uhg.groups_id
                                                JOIN users u on uhg.users_id = u.id
                                                WHERE u.id = " . $id);

        $user_groups_array = array();
        do {
            $user_group = $user_groups->fetch_assoc();
            if ($user_group) {
                $user_groups_array[] = $user_group["group_name"];
            }
        } while ($user_group);

        do {
            $group = $groups->fetch_assoc();
            if ($group) {
                if (!in_array($group["group_name"], $user_groups_array)) {
                    $show->setContent("select", "unselected");
                } else {
                    $show->setContent("select", "selected");
                }
                $show->setContent("group_id", $group["id"]);
                $show->setContent("group_name", $group["group_name"]);
            }
        } while ($group);

        $main->setContent("content", $show->get());
        $main->close();
    }
}

// Per il momento non funziona dobbiamo parlare di cosa succede se cancelliamo un utente
function delete(){
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $ban = $mysqli->query("SELECT ban FROM tdw_ecommerce.users WHERE id = $id");
    $ban = $ban->fetch_assoc();
    if ($ban['ban'] == 0) {
        $mysqli->query("UPDATE tdw_ecommerce.users SET ban = 1 WHERE id = $id");
    } else {
        $mysqli->query("UPDATE tdw_ecommerce.users SET ban = 0 WHERE id = $id");
    }
    $response = array();
    if($mysqli->affected_rows == 1){
        $response['success'] = "Status modificato con successo";
    } else {
        $response['error'] = "Errore nella modifica dello status dell'utente";
    }
    exit(json_encode($response));
}

function edit(){
    global $mysqli;
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $cognome = $_POST["cognome"];
    $email = $_POST["email"];
    $telefono = $_POST["telefono"];
    $response = array();
    if ($id != "" && $nome != "" && $cognome != "" && $email != "" && $telefono != "") {
        $mysqli->query("UPDATE tdw_ecommerce.users SET nome = '$nome', cognome = '$cognome', email = '$email', telefono = '$telefono' WHERE id = $id");

        if (isset($_POST["gruppi"])) {
            $groups = $_POST["gruppi"];
            $mysqli->query("DELETE FROM tdw_ecommerce.users_has_groups WHERE users_id = $id");
            foreach ($groups as $group) {
                $mysqli->query("INSERT INTO tdw_ecommerce.users_has_groups (users_id, groups_id) VALUES ($id, $group)");
            }
        }
        if($mysqli->affected_rows == 1){
            $response['success'] = "Utente modificato con successo";
        } elseif($mysqli->affected_rows == 0 ) {
            $response['warning'] = "Nessun dato modificato";
        } else {
            $response['error'] = "Errore nella modifica dell'utente";
        }

    } else {
        $response['error'] = "Errore nella modifica dell'utente";
    }
    exit(json_encode($response));
}