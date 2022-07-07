# GRUPPI PER GLI UTENTI
INSERT INTO `groups` (id, group_name) VALUES (NULL, 'ADMIN');
INSERT INTO `groups` (id, group_name) VALUES (NULL, 'UTENTE');

# ASSEGNAZIONE GRUPPI AGLI UTENTI
# ADMIN:    admin@example.net     password
# UTENTE:   utente@example.net    password
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (1, 1);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (2, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (3, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (4, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (5, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (6, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (7, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (8, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (9, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (10, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (11, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (12, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (13, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (14, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (15, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (16, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (17, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (18, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (19, 2);
INSERT INTO `users_has_groups` (users_id, groups_id) VALUES (20, 2);

# ASSEGNAZIONE SUI DIRITTI DI ACCESSO ALLE PAGINE
# ADMIN:
DROP PROCEDURE IF EXISTS service_has_groups_admin;
DELIMITER //
CREATE PROCEDURE service_has_groups_admin()
    BEGIN
        DECLARE i INT DEFAULT (SELECT count(id) FROM services WHERE url LIKE '%admin%'); # numero di pagine di admin
        DECLARE j INT DEFAULT (SELECT id FROM services WHERE url LIKE '%admin%' LIMIT 1); # id della prima pagina di admin

        WHILE (i) > 0 DO
            INSERT INTO `services_has_groups` (services_id, groups_id) VALUES ((SELECT id FROM services WHERE url LIKE '%admin%' AND id = j), 1);
            SET J = J + 1; # prossima pagina di admin
            SET i = i - 1; # decremento il numero di pagine da processare
        END WHILE;
    END//
DELIMITER ;

CALL service_has_groups_admin();

# UTENTE:
DROP PROCEDURE IF EXISTS service_has_groups_user;
DELIMITER //
CREATE PROCEDURE service_has_groups_user()
BEGIN
    DECLARE pagine_pubbliche INT DEFAULT 4; # Numero di pagine pubbliche in testa alla tabella.
    DECLARE i INT DEFAULT (SELECT count(id) FROM services WHERE url NOT LIKE '%admin%') - pagine_pubbliche; # numero di pagine di utente
    DECLARE j INT DEFAULT pagine_pubbliche + 1; #prima pagina non pubblica dell'utente

    WHILE (i) > 0 DO
        INSERT INTO `services_has_groups` (services_id, groups_id) VALUES ((SELECT id FROM services WHERE url NOT LIKE '%admin%' AND id = j), 2);
        SET J = J + 1; # prossima pagina di utente
        SET i = i - 1; # decremento il numero di pagine da processare
    END WHILE;
END//
DELIMITER ;

CALL service_has_groups_user();