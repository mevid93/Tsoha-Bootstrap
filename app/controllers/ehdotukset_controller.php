<?php

/*
 * Kontrolleri, joka hoitaa drinkkien käsittelyn.
 */

class EhdotuksetController extends BaseController {

    // metodi, joka hoitaa drinkkien listaamisen
    public static function index() {
        $drinkit = Drinkki::kaikkiHyvaksytyt();
        View::make('ehdotus/ehdotus_lista.html', array('drinkit' => $drinkit));
    }

    // metodi, joka lisää ehdotuksen tietokantaan
    public static function hylkaa() {
        // POST-pyynnön muuttujat sijaisevat $_POST nimisessä assosiaatiolistassa
        $params = $_POST;

        $drinkki = Drinkki::etsiPerusteellaID($params['id']);

        $drinkki->poista();

        // Ohjataan käyttäjä sovelluksen etusivulle
        Redirect::to('/ehdotukset', array('message' => "Poisto onnistui!"));
    }
    
    // metodi, joka lisää ehdotuksen tietokantaan
    public static function hyvaksy() {
        // POST-pyynnön muuttujat sijaisevat $_POST nimisessä assosiaatiolistassa
        $params = $_POST;

        $drinkki = Drinkki::etsiPerusteellaID($params['id']);

        $drinkki->merkitseHyvaksytyksi();

        // Ohjataan käyttäjä sovelluksen etusivulle
        Redirect::to('/ehdotukset', array('message' => "Hyväksyminen onnistui!"));
    }

}
