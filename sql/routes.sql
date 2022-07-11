INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Public', 'Visualizza home', '/', 'home.php', 'home');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Public', 'Visualizza about', '/about', 'about.php', 'about');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Public', 'Visualizza contact', '/contacts', 'contacts.php', 'contacts');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Public', 'Visualizza login', '/login', 'auth/access.php', 'login');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Public', 'Effettua logout', '/logout', 'auth/access.php', 'logout');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Public', 'Effettua registrazione', '/register', 'auth/access.php', 'register');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Public', 'Visualizza prodotti', '/products', 'products.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Public', 'Visualizza prodotto', '/products/%', 'products.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Profilo','Visualizza profilo', '/myAccount', 'user/profile.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Profilo','Modifica profilo', '/myAccountEdit', 'user/profile.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Profilo','Modifica password', '/myAccountPassword', 'user/profile.php', 'cambio_password');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Cart', 'Visualizza carrello', '/cart', 'user/cart.php', 'cart');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Cart', 'Aggiorna quantità', '/cart/%/edit', 'user/cart.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Cart', 'Rimuovi articolo', '/cart/%/remove', 'user/cart.php', 'remove');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Ordini', 'Visualizza ordini', '/orders', 'user/orders.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Ordini', 'Visualizza ordine', '/orders/%', 'user/orders.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Ordini', 'Annulla ordine', '/orders/annulla', 'user/orders.php', 'annulla');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Ordini', 'Modifica ordine', '/orders/%/edit', 'user/orders.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Indirizzi', 'Visualizza indirizzi', '/addresses', 'user/addresses.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Indirizzi', 'Visualizza indirizzo', '/addresses/%', 'user/addresses.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Indirizzi', 'Visualizza indirizzo', '/addresses/%/edit', 'user/addresses.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Indirizzi', 'Visualizza indirizzo', '/addresses/create', 'user/addresses.php', 'create');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Like Prodotti', 'Toggle Like Prodotto', '/products/%/like', 'products.php', 'like');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Home admin', 'Visualizza home admin', '/admin', 'admin/index.php', 'admin');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Home admin', 'Visualizza home admin', '/admin', 'admin/orders.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione ordini', 'Visualizza ordini', '/admin/orders', 'admin/orders.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione ordini', 'Visualizza singolo ordine', '/admin/orders/%', 'admin/orders.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione ordini', 'Accetta un ordine', '/admin/orders/%/accetta_ordine', 'admin/orders.php', 'accetta_ordine');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione ordini', 'Modifica ordine', '/admin/orders/%/edit', 'admin/orders.php', 'edit_stato');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione ordini', 'Cancella ordine', '/admin/orders/%/delete', 'admin/orders.php', 'delete');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione utenti' , 'Visualizza utenti', '/admin/users', 'admin/users.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione utenti', 'Visualizza singolo utente', '/admin/users/%', 'admin/users.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione utenti', 'Modifica utente', '/admin/users/%/edit', 'admin/users.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione utenti', 'Cancella utente', '/admin/users/%/delete', 'admin/users.php', 'delete');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione prodotti', 'Visualizza prodotti', '/admin/products', 'admin/products.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione prodotti', 'Visualizza singolo prodotto', '/admin/products/%', 'admin/products.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione prodotti', 'Modifica prodotto', '/admin/products/%/edit', 'admin/products.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione prodotti', 'Cancella prodotto', '/admin/products/%/delete', 'admin/products.php', 'delete');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione categorie', 'Visualizza categorie', '/admin/categories', 'admin/categories.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione categorie', 'Visualizza singola categoria', '/admin/categories/%', 'admin/categories.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione categorie', 'Modifica categoria', '/admin/categories/%/edit', 'admin/categories.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione categorie', 'Cancella categoria', '/admin/categories/%/delete', 'admin/categories.php', 'delete');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione produttori', 'Visualizza produttori', '/admin/producers', 'admin/producers.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione produttori', 'Visualizza singolo produttore', '/admin/producers/%', 'admin/producers.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione produttori', 'Modifica produttore', '/admin/producers/%/edit', 'admin/producers.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione produttori', 'Cancella produttore', '/admin/producers/%/delete', 'admin/producers.php', 'delete');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione provenienze', 'Visualizza provenienze', '/admin/provenances', 'admin/provenances.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione provenienze', 'Visualizza singola provenienza', '/admin/provenances/%', 'admin/provenances.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione provenienze', 'Modifica provenienza', '/admin/provenances/%/edit', 'admin/provenances.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione provenienze', 'Cancella provenienza', '/admin/provenances/%/delete', 'admin/provenances.php', 'delete');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione recensioni', 'Visualizza recensioni', '/admin/reviews', 'admin/reviews.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione recensioni', 'Visualizza singola recensione', '/admin/reviews/%', 'admin/reviews.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione recensioni', 'Modifica recensione', '/admin/reviews/%/edit', 'admin/reviews.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione recensioni', 'Cancella recensione', '/admin/reviews/%/delete', 'admin/reviews.php', 'delete');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione gruppi', 'Visualizza gruppi', '/admin/groups', 'admin/groups.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione gruppi', 'Visualizza singolo gruppo', '/admin/groups/%', 'admin/groups.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione gruppi', 'Aggiungi gruppo', '/admin/groups/create', 'admin/groups.php', 'create');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione gruppi', 'Modifica gruppo', '/admin/groups/%/edit', 'admin/groups.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione gruppi', 'Cancella gruppo', '/admin/groups/%/delete', 'admin/groups.php', 'delete');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione servizi', 'Visualizza servizi', '/admin/services', 'admin/services.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione servizi', 'Visualizza singolo servizio', '/admin/services/%', 'admin/services.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione servizi', 'Modifica servizio', '/admin/services/%/edit', 'admin/services.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione servizi', 'Cancella servizio', '/admin/services/%/delete', 'admin/services.php', 'delete');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione immagini', 'Visualizza immagini', '/admin/products/%/images', 'admin/images.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione immagini', 'Visualizza singola immagine', '/admin/products/%/images/%', 'admin/images.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione immagini', 'Modifica immagini', '/admin/products/%/images/edit', 'admin/images.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione immagini', 'Cancella immagini', '/admin/products/%/images/%/delete', 'admin/images.php', 'delete');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione prodotto', 'Aggiungi prodotto', '/admin/products/create', 'admin/products.php', 'create');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione produttore', 'Aggiungi produttore', '/admin/producers/create', 'admin/producers.php', 'create');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione provenienza', 'Aggiungi provenienza', '/admin/provenances/create', 'admin/provenances.php', 'create');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione servizi', 'Aggiungi servizio', '/admin/services/create', 'admin/services.php', 'create');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione immagini', 'Aggiungi immagine', '/admin/products/%/images/create', 'admin/images.php', 'create');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione categorie', 'Aggiungi categoria', '/admin/categories/create', 'admin/categories.php', 'create');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione offerte', 'Visualizza offerte', '/admin/offers', 'admin/offers.php', 'index');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione offerte', 'Visualizza singola offerta', '/admin/offers/%', 'admin/offers.php', 'show');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione offerte', 'Modifica offerta', '/admin/offers/%/edit', 'admin/offers.php', 'edit');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione offerte', 'Cancella offerta', '/admin/offers/%/delete', 'admin/offers.php', 'delete');
INSERT INTO services (id, tag, description, url, script, callback) VALUES (NULL, 'Gestione offerte', 'Aggiungi offerta', '/admin/offers/create', 'admin/offers.php', 'create');
