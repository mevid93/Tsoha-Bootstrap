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

$routes->get('/kirjaudu', function() {
    HelloWorldController::sign_in();
});

$routes->get('/rekisteroidy', function() {
    HelloWorldController::sign_up();
});

$routes->get('/asetukset', function() {
    HelloWorldController::change_settings();
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

$routes->get('/drinkki/1/muokkaa', function() {
    HelloWorldController::recipi_edit();
});


// POST metodit
// Ehdotuksen lisääminen tietokantaan
$routes->post('/ehdota', function() {
    EhdotusController::lisaa();
});

$routes->post('/ehdotukset/hylkaa', function() {
    EhdotuksetController::hylkaa();
});

$routes->post('/ehdotukset/hyvaksy', function() {
    EhdotuksetController::hyvaksy();
});

