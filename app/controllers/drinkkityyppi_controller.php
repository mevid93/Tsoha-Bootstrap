<?php

/**
 * Kontrolleri, joka sisältää tarvittavat metodit drinkkityyppien käsittelyyn.
 * Sisältää muun muassa metodit drinkkityyppien listausnäkymän ja lisäysnäkymän
 * renderöintiin ja drinkkityyppien lisäykseen ja poistoon. 
 */
class DrinkkityyppiController extends BaseController {

    /**
     * Metodi, joka hoitaa drinkkityyppien listauksen ja näkymän luonnin.
     */
    public static function listausNakyma() {
        $tyypit = Drinkkityyppi::kaikki();
        View::make('drinkkityyppi/drinkkityyppiLista.html', array('tyypit' => $tyypit));
    }

    /**
     * Metodi, joka hoitaa drinkkityypin lisäys sivun renderöinnin.
     */
    public static function lisaysNakyma() {
        self::check_admin_logged_in();
        View::make('drinkkityyppi/drinkkityyppiLisays.html');
    }

    /**
     * Metodi, joka hoitaa uuden drinkkityypin lisäämisen tietokantaan.
     */
    public static function lisaa() {
        self::check_admin_logged_in();
        $params = $_POST;
        $tyyppi = new Drinkkityyppi(array(
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus']
        ));
        $errors = $tyyppi->virheet();
        if (count($errors) == 0) {
            $tyyppi->tallenna();
            Redirect::to('/drinkkityyppi', array('message' => "Drinkkityyppi lisätty onnistuneesti"));
        } else {
            View::make('drinkkityyppi/drinkkityyppiLisays.html', array('drinkkityyppi' => $tyyppi, 'errors' => $errors));
        }
    }

    /**
     * Metodi, joka hoitaa poistaa drinkkityypin tietokannasta.
     */
    public static function poista() {
        // POST-pyynnön muuttujat sijaisevat $_POST nimisessä assosiaatiolistassa
        self::check_admin_logged_in();
        $params = $_POST;
        Drinkkityyppi::poista($params['id']);

        // Ohjataan takaisin drinkkityyppien hallinta sivullle
        Redirect::to('/drinkkityyppi', array('message' => "Drinkkityypin poisto onnistui"));
    }

    /**
     * Metodi, joka hoitaa muokkausnäkymän renderöinnin.
     * 
     * @param integer $id drinkkityypin tunnus
     */
    public static function muokkausNakyma($id) {
        self::check_admin_logged_in();
        $tyyppi = Drinkkityyppi::etsiPerusteellaID($id);
        // Ohjataan drinkkityyppien muokkaus sivullle
        View::make('/drinkkityyppi/muokkaus.html', array('tyyppi' => $tyyppi));
    }

    /**
     * Metodi, joka päivittää muokkauksen tietokantaan.
     * 
     * @param integer $id drinkkityypin tunnus
     */
    public static function muokkaa($id) {
        self::check_admin_logged_in();
        $params = $_POST;
        $tyyppi = new Drinkkityyppi(array(
            'id' => $id,
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus']
        ));
        $errors = $tyyppi->virheet();
        if (count($errors) == 0) {
            $tyyppi->muokkaa();
            Redirect::to('/drinkkityyppi', array('message' => "Drinkkityypin muokkaus onnistui"));
        } else {
            View::make('drinkkityyppi/muokkaus.html', array('tyyppi' => $tyyppi, 'errors' => $errors));
        }
    }

}
