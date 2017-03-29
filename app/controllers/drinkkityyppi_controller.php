<?php

/*
 * Kontrolleri, joka hoitaa drinkkityyppien käsittelyn.
 */

class DrinkkityyppiController extends BaseController {

    // metodi, joka hoitaa drinkkityypien listauksen ja näkymän luonnin
    public static function index() {
        $tyypit = Drinkkityyppi::kaikki();
        View::make('drinkkityyppi/drinkkityyppi_lista.html', array('tyypit' => $tyypit));
    }
    
    // metodi, joka ohjaa drinkkityypin lisys sivulle
    public static function lisaysNakyma() {
        View::make('drinkkityyppi/drinkkityyppi_lisays.html');
    }

    // metodi, joka hoitaa uuden drinkkityypin lisäämisen tietokantaan
    public static function lisaa() {
        // POST-pyynnön muuttujat sijaisevat $_POST nimisessä assosiaatiolistassa
        $params = $_POST;

        $tyyppi = new Drinkkityyppi(array(
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus']
        ));

        $tyyppi->tallenna();

        // Ohjataan käyttäjä sovelluksen drinkkityyppien hallintaan
        Redirect::to('/drinkkityyppi', array('message' => "Drinkkityyppi lisätty onnistuneesti"));
    }
    
    // metodi, joka hoitaa poistaa drinkkityypin tietokannasta
    public static function poista() {
        // POST-pyynnön muuttujat sijaisevat $_POST nimisessä assosiaatiolistassa
        $params = $_POST;
        Drinkkityyppi::poista($params['id']);

        // Ohjataan takaisin drinkkityyppien hallinta sivullle
        Redirect::to('/drinkkityyppi', array('message' => "Drinkkityypin poisto onnistui"));
    }
    
    // metodi, joka hoitaa näkyville muokkausnakyman
    public static function muokkausNakyma($id) {
        $tyyppi = Drinkkityyppi::find($id);
        // Ohjataan drinkkityyppien muokkaus sivullle
        View::make('/drinkkityyppi/muokkaus.html', array('tyyppi' => $tyyppi));
    }
    
    // metodi, joka päivittää muokkauksen tietokantaan
    public static function muokkaa() {
        // POST-pyynnön muuttujat sijaisevat $_POST nimisessä assosiaatiolistassa
        $params = $_POST;
        $tyyppi = new Drinkkityyppi(array(
            'id' => $params['id'],
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus']
        ));
        $tyyppi->muokkaa();
        // Ohjataan takaisin drinkkityyppien hallinta sivullle
        Redirect::to('/drinkkityyppi', array('message' => "Drinkkityypin muokkaus onnistui"));
    }

}
