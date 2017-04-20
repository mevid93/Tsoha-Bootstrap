<?php

/**
 * Kontrolleri, joka sisältää tarvittavat metodit ainesosien käsittelyyn.
 * Sisältää muun muassa metodit ainesosien listausnäkymän ja lisäysnäkymän
 * renderöintiin ja ainesosien lisäykseen ja poistoon. 
 */
class AinesosaController extends BaseController {

    /**
     * Metodi, joka hoitaa ainesosien listauksen ja näkymän luonnin.
     */
    public static function listausNakyma() {
        $ainekset = Ainesosa::kaikkiAakkosjarjestyksessa();
        View::make('ainesosa/ainesosaLista.html', array('ainekset' => $ainekset));
    }

    /**
     * Metodi, joka hoitaa ainesosien lisäyssivun renderöinnin.
     */
    public static function lisaysNakyma() {
        self::check_admin_logged_in();
        View::make('ainesosa/lisaaAinesosa.html');
    }

    /**
     * Metodi, joka hoitaa uuden drinkkityypin lisäämisen tietokantaan.
     */
    public static function lisaa() {
        self::check_admin_logged_in();
        $params = $_POST;
        $aines = new Ainesosa(array(
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus']
        ));
        $errors = $aines->virheet();
        if (count($errors) == 0) {
            $aines->tallenna();
            Redirect::to('/ainesosa', array('message' => "Ainesosa lisätty onnistuneesti"));
        } else {
            View::make('ainesosa/lisaaAinesosa.html', array('ainesosa' => $aines, 'errors' => $errors));
        }
    }

    /**
     * Metodi, joka hoitaa muokkausnäkymän renderöinnin.
     * 
     * @param integer $id ainesosan tunnus
     */
    public static function muokkausNakyma($id) {
        self::check_admin_logged_in();
        $aines = Ainesosa::etsiPerusteellaID($id);
        View::make('/ainesosa/muokkaus.html', array('ainesosa' => $aines));
    }

    /**
     * Metodi, joka päivittää muokkauksen tietokantaan.
     * 
     * @param integer $id ainesosan tunnus
     */
    public static function muokkaa($id) {
        self::check_admin_logged_in();
        $params = $_POST;
        $aines = new Ainesosa(array(
            'id' => $id,
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus']
        ));
        $errors = $aines->virheet();
        if (count($errors) == 0) {
            $aines->muokkaa();
            Redirect::to('/ainesosa', array('message' => "Ainesosan muokkaus onnistui"));
        } else {
            View::make('ainesosa/muokkaus.html', array('ainesosa' => $aines, 'errors' => $errors));
        }
    }

    /**
     * Metodi, joka hoitaa poistaa ainesosan tietokannasta.
     */
    public static function poista() {
        self::check_admin_logged_in();
        $params = $_POST;
        Ainesosa::poista($params['id']);
        Redirect::to('/ainesosa', array('message' => "Ainesosan poisto onnistui"));
    }

}
