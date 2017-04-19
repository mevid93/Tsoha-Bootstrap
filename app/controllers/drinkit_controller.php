<?php

/*
 * Kontrolleri, joka hoitaa drinkkien käsittelyn.
 */

class DrinkkiController extends BaseController {
    /*
     * Metodi, joka hoitaa drinkkien listaamisen nimen 
     * mukaan aakkosjärjestyksessä.
     */

    public static function listausNakymaAakkosjarjestyksessa() {
        $drinkit = Drinkki::etsiKaikkiHyvaksytytAakkosjarjestyksessa();
        foreach ($drinkit as $drinkki) {
            $drinkki->aineslista = Drinkinainesosat::haeAinesosat($drinkki->id);
        }
        View::make('drinkki/drinkkiLista.html', array('drinkit' => $drinkit));
    }

    /*
     * Metodi, joka hoitaa yksittäisen drinkin reseptinäkymän näyttämisen.
     */

    public static function naytaResepti($id) {
        $drinkki = Drinkki::etsiPerusteellaID($id);
        $drinkinainesosat = Drinkinainesosat::haeAinesosatOliot($id);
        if ($drinkki == null) {
            Redirect::to('/', array('message' => "Kyseistä drinkkiä ei ole olemassa!"));
        }
        View::make('drinkki/resepti.html', array('drinkki' => $drinkki, 'drinkinainesosat' => $drinkinainesosat));
    }

    /*
     * Metodi, joka listaa drinkit nimeen kohdituvan hakutermin perusteella.
     */

    public static function listaaHaunPerusteela() {
        $params = $_POST;
        if ($params['termi'] == null) {
            Redirect::to('/', array('errors' => array('Et syöttänyt hakutermiä!')));
        } elseif ($params['ehto'] == "nimi") {
            $drinkit = Drinkki::etsiNimenPerusteella($params['termi']);
            foreach ($drinkit as $drinkki) {
                $drinkki->aineslista = Drinkinainesosat::haeAinesosat($drinkki->id);
            }
            View::make('drinkki/drinkkiLista.html', array('drinkit' => $drinkit));
        }
        $drinkit = Drinkki::etsiAinesosanPerusteella($params['termi']);
        foreach ($drinkit as $drinkki) {
            $drinkki->aineslista = Drinkinainesosat::haeAinesosat($drinkki->id);
        }
        View::make('drinkki/drinkkiLista.html', array('drinkit' => $drinkit));
    }

    /*
     * Metodi, joka hoitaa drinkkien listaamisen haluttuun järjestykseen.
     */

    public static function listaaJarjestykseen() {
        $params = $_POST;
        if ($params['jarjestys'] == "2") {
            $drinkit = Drinkki::etsiKaikkiHyvaksytytDrinkkityypinPerusteella($params['jarjestys']);
            foreach ($drinkit as $drinkki) {
                $drinkki->aineslista = Drinkinainesosat::haeAinesosat($drinkki->id);
            }
            View::make('drinkki/drinkkiLista.html', array('drinkit' => $drinkit));
        }
        $drinkit = Drinkki::etsiKaikkiHyvaksytytAakkosjarjestyksessa();
        foreach ($drinkit as $drinkki) {
            $drinkki->aineslista = Drinkinainesosat::haeAinesosat($drinkki->id);
        }
        View::make('drinkki/drinkkiLista.html', array('drinkit' => $drinkit));
    }

    /*
     * Metodi, joka huolehtii drinkin muokkausnäkymän renderöinnistä. 
     */

    public static function muokkausNakyma($id) {
        parent::check_admin_logged_in();
        $tyypit = Drinkkityyppi::kaikki();
        $ainekset = Ainesosa::kaikki();
        $drinkki = Drinkki::etsiPerusteellaID($id);
        $muunimi1 = null;
        $muunimi2 = null;
        if (count($drinkki->muutnimet) > 0) {
            $muunimi1 = $drinkki->muutnimet[0];
        }
        if (count($drinkki->muutnimet) > 1) {
            $muunimi2 = $drinkki->muutnimet[1];
        }
        View::make('drinkki/muokkaus.html', array('drinkki' => $drinkki, 'tyypit' => $tyypit, 'ainekset' => $ainekset, 'muunimi1' => $muunimi1, "muunimi2" => $muunimi2));
    }

    /*
     * Metodi, joka hoitaa drinkin poistamisen tietokannasta. 
     */

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

    /*
     * Metodi, joka päivittää drinkin tietokannassa.
     */

    public static function paivita($id) {
        parent::check_admin_logged_in();
        $params = $_POST;
        $drinkki = Drinkki::etsiPerusteellaID($id);
        $drinkki->ensisijainennimi = $params['nimi'];
        $drinkki->drinkkityyppi = Drinkkityyppi::etsiPerusteellaNimi($params['tyyppi'])->id;
        $drinkki->lasi = $params['lasi'];
        $drinkki->kuvaus = $params['kuvaus'];
        $drinkki->lampotila = $params['lampotila'];

        $errors = $drinkki->virheet();

        if (count($errors) == 0) {
            $drinkki->paivita();
            $drinkki->drinkkityyppi = Drinkkityyppi::etsiPerusteellaNimi($params['tyyppi'])->nimi;
            Redirect::to('/drinkki/' . $id, array('drinkki' => $drinkki, 'message' => "Muutokset tallennettu!"));
        } else {
            $tyypit = Drinkkityyppi::kaikki();
            $ainekset = Ainesosa::all();
            $drinkki->drinkkityyppi = Drinkkityyppi::etsiPerusteellaID($id)->nimi;
            View::make('drinkki/muokkaus.html', array('drinkki' => $drinkki, 'tyypit' => $tyypit, 'ainekset' => $ainekset, 'errors' => $errors));
        }
    }

}
