<?php

// GET metodit

$routes->get('/', function() {
    EtusivuController::index();
});

$routes->get('/drinkki', function() {
    DrinkkiController::index();
});

$routes->get('/drinkki/:id', function($id) {
    DrinkkiController::naytaResepti($id);
});

$routes->get('/rekisteroidy', function() {
    KayttajaController::rekisteroitymisNakyma();
});

$routes->get('/asetukset', function() {
    KayttajaController::muokkausNakyma();
});

$routes->get('/ehdota', function() {
    EhdotusController::index();
});

$routes->get('/ehdotukset', function() {
    EhdotuksetController::index();
});

$routes->get('/kayttajat', function() {
    HelloWorldController::user_list();
});

$routes->get('/drinkkityyppi', function() {
    DrinkkityyppiController::index();
});

$routes->get('/drinkkityyppi/lisaa', function() {
    DrinkkityyppiController::lisaysNakyma();
});

$routes->get('/drinkkityyppi/muokkaa/:id', function($id) {
    DrinkkityyppiController::muokkausNakyma($id);
});

$routes->get('/drinkki/:id/muokkaa', function($id) {
    DrinkkiController::muokkaa($id);
});


// POST metodit

$routes->post('/', function(){
    DrinkkiController::listaaHaunPerusteela(); 
});

$routes->post('/drinkki', function(){
    DrinkkiController::indexJarjestys(); 
});

$routes->post('/drinkki/:id/muokkaa', function($id){
    DrinkkiController::paivita($id); 
});

$routes->post('/drinkki/:id/poista', function($id){
    DrinkkiController::poista($id); 
});

$routes->post('/drinkkityyppi/lisaa', function(){
    DrinkkityyppiController::lisaa(); 
});

$routes->post('/drinkkityyppi/muokkaa', function(){
    DrinkkityyppiController::muokkaa(); 
});

$routes->post('/drinkkityyppi/poista', function(){
    DrinkkityyppiController::poista(); 
});

$routes->post('/ehdota', function() {
    EhdotusController::lisaa();
});

$routes->post('/ehdotukset/hylkaa', function() {
    EhdotuksetController::hylkaa();
});

$routes->post('/ehdotukset/hyvaksy', function() {
    EhdotuksetController::hyvaksy();
});

$routes->post('/kirjaudu', function() {
    KayttajaController::hoidaSisaanKirjautuminen();
});

$routes->post('/kirjauduUlos', function() {
    KayttajaController::hoidaUlosKirjautuminen();
});

$routes->post('/asetukset', function() {
    KayttajaController::hoidaUlosKirjautuminen();
});

$routes->post('/poista', function() {
    KayttajaController::poistaKayttajatili();
});

$routes->post('/rekisteroidy', function() {
    KayttajaController::luoKayttajatili();
});

