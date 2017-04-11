<?php

/*
 * Kontrolleri, joka hoitaa drinkkien käsittelyn.
 */

class DrinkkiController extends BaseController {

    // metodi, joka hoitaa drinkkien listaamisen nimen mukaan aakkosjärjestyksessä
    public static function index() {
        $drinkit = Drinkki::etsiKaikkiHyvaksytytAakkosjarjestyksessa();
        View::make('drinkki/drinkkiLista.html', array('drinkit' => $drinkit));
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
            Redirect::to('/', array('errors' => array('Et syöttänyt hakutermiä!')));
        } elseif ($params['ehto'] == "nimi") {
            $drinkit = Drinkki::etsiNimenPerusteella($params['termi']);
            View::make('drinkki/drinkkiLista.html', array('drinkit' => $drinkit));
        } elseif ($params['ehto'] == "aines") {
            $drinkit = Drinkki::etsiAinesosanPerusteella($params['termi']);
            View::make('drinkki/drinkkiLista.html', array('drinkit' => $drinkit));
        }
    }

    // metodi, joka hoitaa drinkkien listaamisen haluttuun järjestykseen
    public static function indexJarjestys() {
        $params = $_POST;
        if ($params['jarjestys'] == "2") {
            $drinkit = Drinkki::etsiKaikkiHyvaksytytDrinkkityypinPerusteella($params['jarjestys']);
            View::make('drinkki/drinkkiLista.html', array('drinkit' => $drinkit));
        } elseif ($params['jarjestys'] == "3") {
            $drinkit = Drinkki::etsiNimenPerusteella($params['termi']);
            View::make('drinkki/drinkkiLista.html', array('drinkit' => $drinkit));
        }
        //perustapaus
        $drinkit = Drinkki::etsiKaikkiHyvaksytytAakkosjarjestyksessa();
        View::make('drinkki/drinkkiLista.html', array('drinkit' => $drinkit));
    }

    // drinkin muokkauksesta huolehtiva metodi
    public static function muokkaa($id) {
        parent::check_admin_logged_in();
        $tyypit = Drinkkityyppi::kaikki();
        $ainekset = Ainesosa::kaikki();
        $drinkki = Drinkki::etsiPerusteellaID($id);
        $nimet = MuuNimi::etsiPerusteellaDrinkkiID($id);
        View::make('drinkki/muokkaus.html', array('drinkki' => $drinkki, 'tyypit' => $tyypit, 'ainekset' => $ainekset));
    }

    // drinkin päivitys tietokantaan
    public static function paivita($id) {
        parent::check_admin_logged_in();
        $params = $_POST;
        $drinkki = new Drinkki(array(
            'id' => $id,
            'ensisijainennimi' => $params['nimi'],
            'drinkkityyppi' => Drinkkityyppi::etsiPerusteellaNimi($params['tyyppi'])->id,
            'lasi' => $params['lasi'],
            'kuvaus' => $params['kuvaus'],
            'lampotila' => $params['lampotila'],
            'lisayspaiva' => "Tämä päivä",
            'hyvaksytty' => false,
            'lisaaja' => "Anonymous"  // automaattinen lisääjän nimen selvitys implementoitava
        ));

        $errors = $drinkki->virheet();

        if (count($errors) == 0) {
            $drinkki->paivita();
            // Ohjataan käyttäjä sovelluksen etusivulle
            Redirect::to('/drinkki/' . $id, array('drinkki' => $drinkki, 'message' => "Muutokset tallennettu!"));
        } else {
            $tyypit = Drinkkityyppi::kaikki();
            $ainekset = Ainesosa::all();
            // Drinkissä oli jotain vikaa
            View::make('drinkki/muokkaus.html', array('drinkki' => $drinkki, 'tyypit' => $tyypit, 'ainekset' => $ainekset, 'errors' => $errors));
        }
    }

    // drinkin poistamisesta huolehtiva metodi
    public static function poista($id) {
        parent::check_admin_logged_in();
        $drinkki = Drinkki::etsiPerusteellaID($id);
        $drinkki->poista();
        if ($drinkki->hyvaksytty) {
            $drinkit = Drinkki::etsiKaikkiHyvaksytytAakkosjarjestyksessa();
            Redirect::to('/drinkki', array('drinkit' => $drinkit, 'message' => "Drinkin poisto onnistui!"));
        }
        $drinkit = Drinkki::kaikkiHyvaksymattomat();
        Redirect::to('/ehdotukset', array('drinkit' => $drinkit, 'message' => "Drinkin poisto onnistui!"));
    }

}
