<?php

/*
 * Kontrolleri, joka hoitaa drinkkien käsittelyn.
 */

class DrinkkiController extends BaseController {

    // metodi, joka hoitaa drinkkien listaamisen nimen mukaan aakkosjärjestyksessä
    public static function index() {
        $drinkit = Drinkki::etsiKaikkiHyvaksytytAakkosjarjestyksessa();
        View::make('drinkki/drinkki_lista.html', array('drinkit' => $drinkit));
    }

    // metodi, joka hoitaa yksittäisen drinkin näkymän näyttämisen
    public static function naytaResepti($id) {
        $drinkki = Drinkki::etsiPerusteellaID($id);
        // jos kirjoittaa suoraan urlin  niin voi yrittää hakea reseptiä jota 
        // ei oikeasti ole olemassa. Tässä tilanteessa ohjataan etusivulle.
        if ($drinkki == null) {
            Redirect::to('/', array('message' => "Kyseistä drinkkiä ei ole olemassa!"));
        }
        View::make('drinkki/resepti.html', array('drinkki' => $drinkki));
    }

    // metodi, joka listaa drinkit nimeen kohdituvan hakutermin perusteella
    public static function listaaHaunPerusteela() {
        // POST-pyynnön muuttujat sijaisevat $_POST nimisessä assosiaatiolistassa
        $params = $_POST;
        if ($params['termi'] == null) {
            Redirect::to('/', array('message' => 'Et syöttänyt hakutermiä!'));
        } elseif ($params['ehto'] == "nimi") {
            $drinkit = Drinkki::etsiNimenPerusteella($params['termi']);
            View::make('drinkki/drinkki_lista.html', array('drinkit' => $drinkit));
        } elseif ($params['ehto'] == "aines") {
            $drinkit = Drinkki::etsiAinesosanPerusteella($params['termi']);
            View::make('drinkki/drinkki_lista.html', array('drinkit' => $drinkit));
        }
    }

    // metodi, joka hoitaa drinkkien listaamisen haluttuun järjestykseen
    public static function indexJarjestys() {
        $params = $_POST;
        if ($params['jarjestys'] == "2") {
            $drinkit = Drinkki::etsiKaikkiHyvaksytytDrinkkityypinPerusteella($params['jarjestys']);
            View::make('drinkki/drinkki_lista.html', array('drinkit' => $drinkit));
        } elseif ($params['jarjestys'] == "3") {
            $drinkit = Drinkki::etsiNimenPerusteella($params['termi']);
            View::make('drinkki/drinkki_lista.html', array('drinkit' => $drinkit));
        }
        //perustapaus
        $drinkit = Drinkki::etsiKaikkiHyvaksytytAakkosjarjestyksessa();  
        View::make('drinkki/drinkki_lista.html', array('drinkit' => $drinkit));
    }

}
