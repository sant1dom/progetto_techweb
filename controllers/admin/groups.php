<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function index()
{
    global $mysqli;
    $colnames = array(
        "Nome",
    );

    $oid = $mysqli->query("SELECT id, group_name FROM tdw_ecommerce.`groups`");
    $main = setupMainAdmin();
    // Creazione del contenuto
    $crud = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/crud.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/table.html");
    // Riempimento della tabella
    $table->setContent("title", "Gruppi");
    foreach ($colnames as $value) {
        $table->setContent("colname", $value);
    }
    $groups_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/specific_tables/groups.html");
    do {
        $groups = $oid->fetch_assoc();
        if ($groups) {
            debug_to_console($groups);
            foreach ($groups as $key => $value) {
                debug_to_console($value);
                $groups_table->setContent($key, $value);

            }
        }
    } while ($groups);
    $table->setContent("sptable", $groups_table->get());
    $crud->setContent("table", $table->get());
    $main->setContent("content", $crud->get());
    $main->close();
}

function show()
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    //prende tutte le informazioni del gruppo da mostrare
    $group = $mysqli->query("SELECT DISTINCT * FROM tdw_ecommerce.`groups` WHERE id = $id");

    if ($group->num_rows == 0) {    //se non ci sono risultati reindirizza alla index dei gruppi
        header("Location: /admin/groups");
    } else {
        $main = setupMainAdmin();
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/groups/show.html");

        //Inserisco i dati del gruppo nel template
        $group = $group->fetch_assoc();
        $show->setContent("id", $group['id']);
        $show->setContent("group_name", $group['group_name']);

        //recupero tutti i tag dei servizi (eccetto i pubblici)
        $tags = $mysqli->query("SELECT DISTINCT services.tag FROM services WHERE services.tag NOT LIKE 'Public' AND services.tag NOT LIKE 'Gestione gruppi'");

        if ($tags->num_rows > 0) {
            //templete per la lista dei tag e dei loro poteri
            $powers_tmp = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/groups/powers.html");
            do {
                $group_tags = $tags->fetch_assoc();

                if ($group_tags) {
                    foreach ($group_tags as $value) { //per ogni tag seleziono tutte le operazioni associate
                        $powers = $mysqli->query("
                            SELECT services.id, services.description
                            FROM services
                            WHERE services.tag = '{$value}';"
                        );

                        $powers_tmp->setContent("tag", $group_tags['tag']); //inserisco il tag nel template come titolo
                        //templete per la lista dei poteri di un tag
                        $power_tmp = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/groups/power.html");

                        do {
                            $group_powers = $powers->fetch_assoc();
                            if ($group_powers) {
                                //controllo se il servizio è associato al gruppo
                                $check = $mysqli->query("
                                        SELECT services.id
                                        FROM services
                                        JOIN services_has_groups shg on services.id = shg.services_id
                                        WHERE services.tag = '{$value}' AND groups_id = $id AND services.id = {$group_powers['id']};");
                                $check = $check->fetch_assoc();
                                if ($check) {
                                    //se il servizio è associato al gruppo allora rendo la checkbox checked
                                    $power_tmp->setContent("checked", "checked");
                                } else {
                                    $power_tmp->setContent("checked", "");
                                }
                                //inserisco i campo nella checkbox
                                $power_tmp->setContent('id', $group_powers['id']);
                                $power_tmp->setContent('description', $group_powers['description']);
                            }
                        } while ($group_powers);
                        //inserisco il singolo potere nella lista dei poteri del tag
                        $powers_tmp->setContent("power", $power_tmp->get());
                    }
                }
            } while ($group_tags);

            $show->setContent("powers", $powers_tmp->get());

            $main->setContent("content", $show->get());
            $main->close();
        }
    }
}

//da creare la create
//da completare
function delete()
{
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
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Status modificato con successo";
    } else {
        $response['error'] = "Errore nella modifica dello status dell'utente";
    }
    exit(json_encode($response));
}

function edit()
{
    global $mysqli;
    $id = $_POST["id"];
    $nome = $_POST["nome"];

    if (isset($_POST["powers"])) {
        $powers = $_POST["powers"];
    } else {
        $powers = null;
    }

    $response = array();
    if ($id != "" && $nome != "") {
        $oid = $mysqli->query("SELECT group_name FROM `groups` WHERE id = $id");
        $oid = $oid->fetch_assoc();
        if ($oid['group_name'] != $nome) {
            $mysqli->query("UPDATE tdw_ecommerce.`groups` SET group_name = '$nome' WHERE id = $id");
            if ($mysqli->affected_rows == 0) {
                $response['error'] = "Errore nella modifica del nome del gruppo";
                exit(json_encode($response));
            }
        }

        $mysqli->query("DELETE FROM tdw_ecommerce.`services_has_groups` WHERE groups_id = $id");
        if ($powers != null) {
            foreach ($powers as $power) {
                $mysqli->query("INSERT INTO tdw_ecommerce.`services_has_groups` (services_id, groups_id) VALUES ($power, $id)");
            }
            if ($mysqli->affected_rows == 0) {
                $response['warning'] = "Nessun dato modificato";
                exit(json_encode($response));
            }
        }
        $response['success'] = "Gruppo modificato con successo";
    } else {
        $response['warning'] = "Campi mancanti";
    }
    exit(json_encode($response));
}