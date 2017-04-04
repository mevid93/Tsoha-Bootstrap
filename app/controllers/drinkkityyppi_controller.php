<?php

/*
 * Kontrolleri, joka sisältää tarvittavat metodit drinkkityyppien käsittelyyn.
 * Sisältää muun muassa metodit drinkkityyppien listausnäkymän ja lisäysnäkymän
 * renderöintiin ja drinkkityyppien lisäykseen ja poistoon. 
 */

class DrinkkityyppiController extends BaseController {
    /*
     * Metodi, joka hoitaa drinkkityyppien listauksen ja näkymän luonnin.
     */

    public static function listausNakyma() {
        $tyypit = Drinkkityyppi::kaikki();
        View::make('drinkkityyppi/drinkkityyppiLista.html', array('tyypit' => $tyypit));
    }

    /*
     * Metodi, joka hoitaa drinkkityypin lisäys sivun renderöinnin.
     */

    public static function lisaysNakyma() {
        View::make('drinkkityyppi/drinkkityyppiLisays.html');
    }

    /*
     * Metodi, joka hoitaa uuden drinkkityypin lisäämisen tietokantaan.
     */

    public static function lisaa() {
        // POST-pyynnön muuttujat sijaisevat $_POST nimisessä assosiaatiolistassa
        $params = $_POST;

        $tyyppi = new Drinkkityyppi(array(
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus']
        ));

        $errors = $tyyppi->virheet();

        if (count($errors) == 0) {
            $tyyppi->tallenna();
            // Ohjataan käyttäjä sovelluksen drinkkityyppien hallintaan
            Redirect::to('/drinkkityyppi', array('message' => "Drinkkityyppi lisätty onnistuneesti"));
        }else{
            View::make('drinkkityyppi/drinkkityyppiLisays.html', array('drinkkityyppi' => $tyyppi, 'errors' => $errors));
        }
    }

    /*
     * Metodi, joka hoitaa poistaa drinkkityypin tietokannasta.
     */

    public static function poista() {
        // POST-pyynnön muuttujat sijaisevat $_POST nimisessä assosiaatiolistassa
        $params = $_POST;
        Drinkkityyppi::poista($params['id']);

        // Ohjataan takaisin drinkkityyppien hallinta sivullle
        Redirect::to('/drinkkityyppi', array('message' => "Drinkkityypin poisto onnistui"));
    }

    /*
     * Metodi, joka hoitaa muokkausnäkymän renderöinnin.
     */

    public static function muokkausNakyma($id) {
        $tyyppi = Drinkkityyppi::etsiPerusteellaID($id);
        // Ohjataan drinkkityyppien muokkaus sivullle
        View::make('/drinkkityyppi/muokkaus.html', array('tyyppi' => $tyyppi));
    }

    /*
     * Metodi, joka päivittää muokkauksen tietokantaan.
     */

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
