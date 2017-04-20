<?php

/**
 * Kontrolleri, joka hoitaa ehdotettujen drinkkien käsittelyn. Sisältää muun
 * muassa toiminnot kaikkien ehdotusten listaamiseen, ehdotuksen hylkäämiseen
 * ja ehdotuksen hyväksymiseen. 
 */
class EhdotuksetController extends BaseController {

    /**
     * Metodi, joka hoitaa ehdotusten listaamisen
     */
    public static function ehdotuksetNakyma() {
        self::check_admin_logged_in();
        $drinkit = Drinkki::kaikkiHyvaksymattomat();
        foreach ($drinkit as $drinkki) {
            $drinkki->aineslista = Drinkinainesosat::haeAinesosat($drinkki->id);
        }
        View::make('ehdotus/ehdotusLista.html', array('drinkit' => $drinkit));
    }

    /**
     * Metodi, joka hylkää ehdotuksen ja poistaa sen.
     */
    public static function hylkaa() {
        self::check_admin_logged_in();
        $params = $_POST;
        $drinkki = Drinkki::etsiPerusteellaID($params['id']);
        $drinkki->poista();
        Redirect::to('/ehdotukset', array('message' => "Ehdotus hylätty!"));
    }

    /**
     * Metodi, joka hyväksyy ja lisää ehdotuksen tietokantaan.
     */
    public static function hyvaksy() {
        self::check_admin_logged_in();
        $params = $_POST;
        $drinkki = Drinkki::etsiPerusteellaID($params['id']);
        $drinkki->merkitseHyvaksytyksi();
        Redirect::to('/ehdotukset', array('message' => "Hyväksyminen onnistui!"));
    }

}
