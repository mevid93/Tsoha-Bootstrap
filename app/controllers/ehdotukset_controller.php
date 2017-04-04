<?php

/*
 * Kontrolleri, joka hoitaa ehdotettujen drinkkien käsittelyn. Sisältää muun
 * muassa toiminnot kaikkien ehdotusten listaamiseen, ehdotuksen hylkäämiseen
 * ja ehdotuksen hyväksymiseen. 
 */

class EhdotuksetController extends BaseController {

    /*
     * Metodi, joka hoitaa ehdotusten listaamisen
     */
    public static function ehdotuksetNakyma() {
        $drinkit = Drinkki::kaikkiHyvaksymattomat();
        View::make('ehdotus/ehdotusLista.html', array('drinkit' => $drinkit));
    }

    /* 
     * Metodi, joka hylkää ehdotuksen ja poistaa sen.
     */
    public static function hylkaa() {
        $params = $_POST;
        $drinkki = Drinkki::etsiPerusteellaID($params['id']);
        $drinkki->poista();
        Redirect::to('/ehdotukset', array('message' => "Poisto onnistui!"));
    }

    /*
     * Metodi, joka hyväksyy ja lisää ehdotuksen tietokantaan.
     */
    public static function hyvaksy() {
        $params = $_POST;
        $drinkki = Drinkki::etsiPerusteellaID($params['id']);
        $drinkki->merkitseHyvaksytyksi();
        Redirect::to('/ehdotukset', array('message' => "Hyväksyminen onnistui!"));
    }

}
