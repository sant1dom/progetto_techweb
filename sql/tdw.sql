
CREATE DATABASE  IF NOT EXISTS `tdw_ecommerce` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `tdw_ecommerce`;
-- MySQL dump 10.13  Distrib 8.0.29, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: tdw_ecommerce
-- ------------------------------------------------------
-- Server version	8.0.29

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorie` (
                             `id` int NOT NULL AUTO_INCREMENT,
                             `nome` varchar(45) NOT NULL,
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorie`
--

LOCK TABLES `categorie` WRITE;
/*!40000 ALTER TABLE `categorie` DISABLE KEYS */;
/*!40000 ALTER TABLE `categorie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups` (
                          `id` int NOT NULL AUTO_INCREMENT,
                          `group_name` varchar(45) NOT NULL,
                          PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `immagini`
--

DROP TABLE IF EXISTS `immagini`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `immagini` (
                            `id` int NOT NULL AUTO_INCREMENT,
                            `nome_file` varchar(45) NOT NULL,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `immagini`
--

LOCK TABLES `immagini` WRITE;
/*!40000 ALTER TABLE `immagini` DISABLE KEYS */;
/*!40000 ALTER TABLE `immagini` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `indirizzi`
--

DROP TABLE IF EXISTS `indirizzi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `indirizzi` (
                             `id` int NOT NULL AUTO_INCREMENT,
                             `indirizzo` varchar(45) NOT NULL,
                             `citta` varchar(45) NOT NULL,
                             `cap` varchar(5) NOT NULL,
                             `provincia` varchar(2) NOT NULL,
                             `nazione` varchar(45) NOT NULL,
                             `users_id` int NOT NULL,
                             PRIMARY KEY (`id`),
                             KEY `fk_indirizzi_users1_idx` (`users_id`),
                             CONSTRAINT `fk_indirizzi_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `indirizzi`
--

LOCK TABLES `indirizzi` WRITE;
/*!40000 ALTER TABLE `indirizzi` DISABLE KEYS */;
/*!40000 ALTER TABLE `indirizzi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metodi_pagamento`
--

DROP TABLE IF EXISTS `metodi_pagamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodi_pagamento` (
                                    `id` int NOT NULL AUTO_INCREMENT,
                                    `numero_carta` varchar(45) NOT NULL,
                                    `nome_proprietario` varchar(45) NOT NULL,
                                    `scadenza_carta` varchar(5) NOT NULL,
                                    `cvv` int unsigned NOT NULL,
                                    `users_id` int NOT NULL,
                                    PRIMARY KEY (`id`),
                                    KEY `fk_metodi_pagamento_users_idx` (`users_id`),
                                    CONSTRAINT `fk_metodi_pagamento_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodi_pagamento`
--

LOCK TABLES `metodi_pagamento` WRITE;
/*!40000 ALTER TABLE `metodi_pagamento` DISABLE KEYS */;
/*!40000 ALTER TABLE `metodi_pagamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ordini`
--

DROP TABLE IF EXISTS `ordini`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ordini` (
                          `id` int NOT NULL AUTO_INCREMENT,
                          `user_id` int NOT NULL,
                          `data` varchar(45) NOT NULL,
                          `stato` varchar(45) NOT NULL,
                          `totale` float NOT NULL,
                          `numero_ordine` varchar(45),
                          `indirizzi_fatturazione` int NOT NULL,
                          `indirizzi_spedizione` int NOT NULL,
                          `metodi_pagamento` int NOT NULL,
                          `motivazione` text,
                          PRIMARY KEY (`id`),
                          KEY `fk_ordini_users1_idx` (`user_id`),
                          KEY `fk_ordini_indirizzi1_idx` (`indirizzi_fatturazione`),
                          KEY `fk_ordini_indirizzi2_idx` (`indirizzi_spedizione`),
                          KEY `fk_ordini_metodi_pagamento1_idx` (`metodi_pagamento`),
                          CONSTRAINT `fk_ordini_indirizzi1` FOREIGN KEY (`indirizzi_fatturazione`) REFERENCES `indirizzi` (`id`),
                          CONSTRAINT `fk_ordini_indirizzi2` FOREIGN KEY (`indirizzi_spedizione`) REFERENCES `indirizzi` (`id`),
                          CONSTRAINT `fk_ordini_metodi_pagamento1` FOREIGN KEY (`metodi_pagamento`) REFERENCES `metodi_pagamento` (`id`),
                          CONSTRAINT `fk_ordini_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordini`
--

LOCK TABLES `ordini` WRITE;
/*!40000 ALTER TABLE `ordini` DISABLE KEYS */;
/*!40000 ALTER TABLE `ordini` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ordini_has_prodotti`
--

DROP TABLE IF EXISTS `ordini_has_prodotti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ordini_has_prodotti` (
                                       `ordini_id` int NOT NULL,
                                       `prodotti_id` int NOT NULL,
                                       `quantita` int NOT NULL DEFAULT '1',
                                       PRIMARY KEY (`ordini_id`,`prodotti_id`),
                                       KEY `fk_ordini_has_prodotti_prodotti1_idx` (`prodotti_id`),
                                       KEY `fk_ordini_has_prodotti_ordini1_idx` (`ordini_id`),
                                       CONSTRAINT `fk_ordini_has_prodotti_ordini1` FOREIGN KEY (`ordini_id`) REFERENCES `ordini` (`id`),
                                       CONSTRAINT `fk_ordini_has_prodotti_prodotti1` FOREIGN KEY (`prodotti_id`) REFERENCES `prodotti` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordini_has_prodotti`
--

LOCK TABLES `ordini_has_prodotti` WRITE;
/*!40000 ALTER TABLE `ordini_has_prodotti` DISABLE KEYS */;
/*!40000 ALTER TABLE `ordini_has_prodotti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prodotti`
--

DROP TABLE IF EXISTS `prodotti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prodotti` (
                            `id` int NOT NULL AUTO_INCREMENT,
                            `nome` varchar(45) NOT NULL,
                            `prezzo` float unsigned NOT NULL,
                            `dimensione` varchar(45) NOT NULL,
                            `quantita_disponibile` int unsigned NOT NULL,
                            `descrizione` text NOT NULL,
                            `produttori_id` int NOT NULL,
                            `provenienze_id` int NOT NULL,
                            `categorie_id` int NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `fk_prodotti_produttori1_idx` (`produttori_id`),
                            KEY `fk_prodotti_provenienze1_idx` (`provenienze_id`),
                            KEY `fk_prodotti_categorie1_idx` (`categorie_id`),
                            CONSTRAINT `fk_prodotti_categorie1` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`),
                            CONSTRAINT `fk_prodotti_produttori1` FOREIGN KEY (`produttori_id`) REFERENCES `produttori` (`id`),
                            CONSTRAINT `fk_prodotti_provenienze1` FOREIGN KEY (`provenienze_id`) REFERENCES `provenienze` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prodotti`
--

LOCK TABLES `prodotti` WRITE;
/*!40000 ALTER TABLE `prodotti` DISABLE KEYS */;
/*!40000 ALTER TABLE `prodotti` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `prodotti_has_immagini`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prodotti_has_immagini` (
                                         `prodotti_id` int NOT NULL,
                                         `immagini_id` int NOT NULL,
                                         PRIMARY KEY (`prodotti_id`,`immagini_id`),
                                         KEY `fk_prodotti_has_immagini_immagini1_idx` (`immagini_id`),
                                         KEY `fk_prodotti_has_immagini_prodotti1_idx` (`prodotti_id`),
                                         CONSTRAINT `fk_prodotti_has_immagini_immagini1` FOREIGN KEY (`immagini_id`) REFERENCES `immagini` (`id`),
                                         CONSTRAINT `fk_prodotti_has_immagini_prodotti1` FOREIGN KEY (`prodotti_id`) REFERENCES `prodotti` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `produttori`
--

DROP TABLE IF EXISTS `produttori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produttori` (
                              `id` int NOT NULL AUTO_INCREMENT,
                              `ragione_sociale` varchar(45) NOT NULL,
                              `partita_iva` varchar(11) NOT NULL,
                              `provenienza` varchar(45) NOT NULL,
                              `telefono` varchar(45) NOT NULL,
                              `email` varchar(45) NOT NULL,
                              `sito_web` varchar(45) DEFAULT NULL,
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produttori`
--

LOCK TABLES `produttori` WRITE;
/*!40000 ALTER TABLE `produttori` DISABLE KEYS */;
/*!40000 ALTER TABLE `produttori` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provenienze`
--

DROP TABLE IF EXISTS `provenienze`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provenienze` (
                               `id` int NOT NULL AUTO_INCREMENT,
                               `nazione` varchar(45) NOT NULL,
                               `regione` varchar(45) NOT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provenienze`
--

LOCK TABLES `provenienze` WRITE;
/*!40000 ALTER TABLE `provenienze` DISABLE KEYS */;
/*!40000 ALTER TABLE `provenienze` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recensioni`
--

DROP TABLE IF EXISTS `recensioni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recensioni` (
                              `id` int NOT NULL AUTO_INCREMENT,
                              `voto` int unsigned NOT NULL,
                              `commento` text,
                              `prodotti_id` int NOT NULL,
                              `users_id` int NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `fk_recensioni_prodotti1_idx` (`prodotti_id`),
                              KEY `fk_recensioni_users1_idx` (`users_id`),
                              CONSTRAINT `fk_recensioni_prodotti1` FOREIGN KEY (`prodotti_id`) REFERENCES `prodotti` (`id`),
                              CONSTRAINT `fk_recensioni_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recensioni`
--

LOCK TABLES `recensioni` WRITE;
/*!40000 ALTER TABLE `recensioni` DISABLE KEYS */;
/*!40000 ALTER TABLE `recensioni` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
                            `id` int NOT NULL AUTO_INCREMENT,
                            `tag` varchar(45) NOT NULL,
                            `description` varchar(255) NOT NULL,
                            `url` varchar(255) NOT NULL,
                            `script` varchar(255) NOT NULL,
                            `callback` varchar(255) NOT NULL,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services_has_groups`
--

DROP TABLE IF EXISTS `services_has_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services_has_groups` (
                                       `services_id` int NOT NULL,
                                       `groups_id` int NOT NULL,
                                       PRIMARY KEY (`services_id`,`groups_id`),
                                       KEY `fk_services_has_groups_groups1_idx` (`groups_id`),
                                       KEY `fk_services_has_groups_services1_idx` (`services_id`),
                                       CONSTRAINT `fk_services_has_groups_groups1` FOREIGN KEY (`groups_id`) REFERENCES `groups` (`id`),
                                       CONSTRAINT `fk_services_has_groups_services1` FOREIGN KEY (`services_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services_has_groups`
--

LOCK TABLES `services_has_groups` WRITE;
/*!40000 ALTER TABLE `services_has_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `services_has_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
                         `id` int NOT NULL AUTO_INCREMENT,
                         `nome` varchar(45) NOT NULL,
                         `cognome` varchar(45) NOT NULL,
                         `email` varchar(45) NOT NULL,
                         `password` varchar(45) NOT NULL,
                         `telefono` varchar(45) NOT NULL,
                         `ban` tinyint(1) NOT NULL DEFAULT '0',
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_has_groups`
--

DROP TABLE IF EXISTS `users_has_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_has_groups` (
                                    `users_id` int NOT NULL,
                                    `groups_id` int NOT NULL,
                                    PRIMARY KEY (`users_id`,`groups_id`),
                                    KEY `fk_users_has_groups_groups1_idx` (`groups_id`),
                                    KEY `fk_users_has_groups_users1_idx` (`users_id`),
                                    CONSTRAINT `fk_users_has_groups_groups1` FOREIGN KEY (`groups_id`) REFERENCES `groups` (`id`),
                                    CONSTRAINT `fk_users_has_groups_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_has_groups`
--

LOCK TABLES `users_has_groups` WRITE;
/*!40000 ALTER TABLE `users_has_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_has_groups` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `offerte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE offerte (
                              `id` int NOT NULL AUTO_INCREMENT,
                              `percentuale` int unsigned NOT NULL,
                              `data_inizio` datetime NOT NULL,
                              `data_fine` datetime NOT NULL,
                              `prodotti_id` int NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `fk_offerte_prodotti1_idx` (`prodotti_id`),
                              CONSTRAINT `fk_offerte_prodotti1` FOREIGN KEY (`prodotti_id`) REFERENCES `prodotti` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offerte`
--
LOCK TABLES `offerte` WRITE;
/*!40000 ALTER TABLE `offerte` DISABLE KEYS */;
/*!40000 ALTER TABLE `offerte` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-06-23 12:16:05
