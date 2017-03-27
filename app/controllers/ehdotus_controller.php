<?php

/*
 * Kontrolleri, joka hoitaa ehdotuksen käsittelyn.
 */

class EhdotusController extends BaseController {

    // metodi, joka hoitaa ainesosien ja drinkkityyppien listaamisen
    public static function index() {
        $tyypit = Drinkkityyppi::all();
        $ainekset = Ainesosa::all();
        View::make('ehdotus/ehdota.html', array('tyypit' => $tyypit, 'ainekset' => $ainekset));
    }

    // metodi, joka lisää ehdotuksen tietokantaan
    public static function lisaa() {
        // POST-pyynnön muuttujat sijaisevat $_POST nimisessä assosiaatiolistassa
        $params = $_POST;

        $drinkki = new Drinkki(array(
            'ensisijainennimi' => $params['nimi'],
            'drinkkityyppi' => Drinkkityyppi::findByName($params['tyyppi'])->id,
            'lasi' => $params['lasi'],
            'kuvaus' => $params['kuvaus'],
            'lampotila' => $params['lampotila'],
            'lisayspaiva' => "Tämä päivä",
            'hyvaksytty' => false,
            'lisaaja' => "Anonymous"  // automaattinen lisääjän nimen selvitys implementoitava
        ));

        $drinkki->tallenna();

        // Ohjataan käyttäjä sovelluksen etusivulle
        Redirect::to('/', array('message' => "Ehdotuksesi on lähetetty ylläpitäjän hyväksyttäväksi!"));
    }

}
