<?php

/*
 * Kontrolleri, joka sisältää tarvittavat metodit ainesosien käsittelyyn.
 * Sisältää muun muassa metodit ainesosien listausnäkymän ja lisäysnäkymän
 * renderöintiin ja ainesosien lisäykseen ja poistoon. 
 */

class AinesosaController extends BaseController {
    /*
     * Metodi, joka hoitaa ainesosien listauksen ja näkymän luonnin.
     */

    public static function listausNakyma() {
        $ainekset = Ainesosa::kaikki();
        View::make('ainesosa/ainesosaLista.html', array('ainekset' => $ainekset));
    }

    /*
     * Metodi, joka hoitaa ainesosien lisäyssivun renderöinnin.
     */

    public static function lisaysNakyma() {
        View::make('ainesosa/lisaaAinesosa.html');
    }

    /*
     * Metodi, joka hoitaa uuden drinkkityypin lisäämisen tietokantaan.
     */

    public static function lisaa() {
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

    /*
     * Metodi, joka hoitaa muokkausnäkymän renderöinnin.
     */

    public static function muokkausNakyma($id) {
        $aines = Ainesosa::etsiPerusteellaID($id);
        View::make('/ainesosa/muokkaus.html', array('ainesosa' => $aines));
    }

    /*
     * Metodi, joka päivittää muokkauksen tietokantaan.
     */

    public static function muokkaa($id) {
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

    /*
     * Metodi, joka hoitaa poistaa ainesosan tietokannasta.
     */

    public static function poista() {
        $params = $_POST;
        Ainesosa::poista($params['id']);
        Redirect::to('/ainesosa', array('message' => "Ainesosan poisto onnistui"));
    }

}
