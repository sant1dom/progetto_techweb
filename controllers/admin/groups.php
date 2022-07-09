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
            if ($groups["group_name"] == "ADMIN") {
                $groups_table->setContent("id", 1);
                $groups_table->setContent("group_name", "ADMIN");
                $groups_table->setContent("actions", "<div class='g-2 text-center'>-</div>");

            } else if ($groups["group_name"] == "UTENTE") {
                $groups_table->setContent("id", 2);
                $groups_table->setContent("group_name", "UTENTE");
                $groups_table->setContent("actions", "<div class='g-2 text-center'>-</div>");

            } else {
                $actions = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/groups/actions.html");
                $groups_table->setContent("id", $groups["id"]);
                $groups_table->setContent("group_name", $groups["group_name"]);
                $actions->setContent("id", $groups["id"]);
                $groups_table->setContent("actions", $actions->get());
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

        if ($group['group_name'] != "ADMIN" && $group['group_name'] != "UTENTE") {
            $show->setContent("edit", "
                <button class='btn btn-primary btn-sm mx-3' id='edit' name='edit' value='edit' type='submit'>
                        <span class='fe fe-edit fs-14'></span> Modifica gruppo
                </button>");
        } else {
            $show->setContent("edit", "Gruppo predefinito");
        }


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


function delete()
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];

    if ($id != 1 && $id != 2) {
        $mysqli->query("DELETE FROM tdw_ecommerce.`groups` WHERE id = $id");
        if ($mysqli->affected_rows == 1) {
            $response['success'] = "Gruppo eliminato con successo";
        } else {
            $response['error'] = "Errore nell'eliminazione del gruppo";
        }
    } else {
        $response['error'] = "Impossibile eliminare i gruppi predefiniti";
    }
    exit(json_encode($response));
}

function create()
{
    global $mysqli;
    $response = array();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['nome'])) {
            $nome = $_POST["nome"];
            $oid = $mysqli->query("SELECT id FROM tdw_ecommerce.groups WHERE group_name = '$nome'");
            if ($oid->num_rows != 0) {
                $response['error'] = "Nome già esistente";
                exit(json_encode($response));
            }

            if (isset($_POST["powers"])) {
                $powers = $_POST["powers"];
            } else {
                $powers = null;
            }

            $mysqli->query("INSERT INTO tdw_ecommerce.groups (group_name) VALUES ('$nome')");
            if ($mysqli->affected_rows == 1) {
                $id = $mysqli->insert_id;
                if ($powers != null) {
                    foreach ($powers as $power) {
                        $mysqli->query("INSERT INTO tdw_ecommerce.services_has_groups (groups_id, services_id) VALUES ($id, $power)");
                    }
                }
                $response['redirect'] = "/admin/groups";
            } else {
                $response['error'] = "Errore nella creazione del gruppo";
            }
            exit(json_encode($response));
        }
    } else {
        $main = setupMainAdmin();
        $edit = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/groups/edit.html");

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
                                $power_tmp->setContent("checked", "");
                                $power_tmp->setContent('id', $group_powers['id']);
                                $power_tmp->setContent('description', $group_powers['description']);
                            }
                        } while ($group_powers);
                        //inserisco il singolo potere nella lista dei poteri del tag
                        $powers_tmp->setContent("power", $power_tmp->get());
                    }
                }
            } while ($group_tags);
            $edit->setContent("powers", $powers_tmp->get());
            $main->setContent("content", $edit->get());
            $main->close();
        }
    }
}

function edit()
{
    if (!(isset($_POST['id']) && isset($_POST['nome']))) {
        Header("Location: /admin/groups");
    }

    if ($_POST["id"] == 1 || $_POST["id"] == 2) {
        $response['error'] = "Impossibile modificare il gruppo predefinito";
        exit(json_encode($response));
    }

    global $mysqli;
    $response = array();

    $id = $_POST["id"];
    $nome = $_POST["nome"];

    if (isset($_POST["powers"])) {
        $powers = $_POST["powers"];
    } else {
        $powers = null;
    }

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

        $oid = $mysqli->query("SELECT id FROM services WHERE tag = 'Gestione gruppi'");
        $gestione_gruppi = array();
        do {
            $potere_gruppo = $oid->fetch_assoc();
            if ($potere_gruppo) {
                $gestione_gruppi[] = $potere_gruppo['id'];
            }
        } while ($potere_gruppo);

        $mysqli->query("DELETE FROM tdw_ecommerce.`services_has_groups` 
                                    WHERE groups_id = $id AND services_id                                          
                                    NOT IN ($gestione_gruppi[0], $gestione_gruppi[1], 
                                            $gestione_gruppi[2], $gestione_gruppi[3], 
                                            $gestione_gruppi[4])");
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