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
        $maarat = Drinkinainesosat::ainesosienMaarat($id);
        View::make('drinkki/muokkaus.html', array('drinkki' => $drinkki, 'tyypit' => $tyypit, 'ainekset' => $ainekset, 'muunimi1' => $muunimi1, "muunimi2" => $muunimi2, 'maara1' => $maarat[0], 'maara2' => $maarat[1], 'maara3' => $maarat[2], 'maara4' => $maarat[3], 'maara5' => $maarat[4]));
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
        $drinkki = self::luoMuokattuDrinkki($params, $id);
        $muunimi1 = self::luoMuuNimi($params['muunimi1'], $id);
        $muunimi2 = self::luoMuuNimi($params['muunimi2'], $id);
        $errors = self::tarkastaVirheetOlioista($drinkki, $muunimi1, $muunimi2);
        $errors = array_merge($errors, EhdotusController::tarkistaAinesosatJaMaarat($params));
        if (count($errors) == 0) {
            self::suoritaTietokantaoperaatiot($drinkki, $muunimi1, $muunimi2, $params);
            $drinkki = Drinkki::etsiPerusteellaID($id);
            Redirect::to('/drinkki/' . $id, array('drinkki' => $drinkki, 'message' => "Muutokset tallennettu!"));
        } else {
            self::ohjaaTakaisinMuokkausNakymaan($drinkki, $muunimi1, $muunimi2, $errors, $params);
        }
    }

    /*
     * Apumetodi, joka muokkaa vanhaa drinkkiä ja palauttaa muokatun version.
     */

    private static function luoMuokattuDrinkki($params, $id) {
        $drinkki = Drinkki::etsiPerusteellaID($id);
        $drinkki->ensisijainennimi = $params['nimi'];
        $drinkki->drinkkityyppi = Drinkkityyppi::etsiPerusteellaNimi($params['tyyppi'])->id;
        $drinkki->lasi = $params['lasi'];
        $drinkki->kuvaus = $params['kuvaus'];
        $drinkki->lampotila = $params['lampotila'];
        return $drinkki;
    }

    /*
     * Apumetodi, joka luo muunimi olion.
     */

    private static function luoMuuNimi($nimi, $drinkkiID) {
        if ($nimi == null || $nimi == '') {
            return null;
        }
        $muunimi = new MuuNimi(array('nimi' => $nimi, 'drinkki' => $drinkkiID));
        return $muunimi;
    }

    /*
     * Apumetodi, joka tarkastaa virheet.
     */

    private static function tarkastaVirheetOlioista($drinkki, $muunimi1, $muunimi2) {
        $errors = array();
        $errors = $drinkki->virheet();
        if ($muunimi1 != null) {
            $errors = array_merge($errors, $muunimi1->virheet());
        }
        if ($muunimi2 != null) {
            $errors = array_merge($errors, $muunimi2->virheet());
        }
        return $errors;
    }

    /*
     * Apumetodi, jolla ohjaa käyttäjän takaisin muokkausnäkymään mikäli 
     * tapahtui virhe syötteissä.
     */

    private static function ohjaaTakaisinMuokkausNakymaan($drinkki, $muunimi1, $muunimi2, $errors, $params) {
        $tyypit = Drinkkityyppi::kaikki();
        $ainekset = Ainesosa::kaikkiAakkosjarjestyksessa();
        View::make('drinkki/muokkaus.html', array('muunimi1' => $muunimi1, 'muunimi2' => $muunimi2, 'drinkki' => $drinkki, 'tyypit' => $tyypit, 'ainekset' => $ainekset, 'errors' => $errors, 'maara1' => $params['maara1'], 'maara2' => $params['maara2'], 'maara3' => $params['maara3'], 'maara4' => $params['maara4'], 'maara5' => $params['maara5']));
    }

    /*
     * Apumetodi, joka suorittaa muokkaukset tietokantatauluihin.
     */
    
    private static function suoritaTietokantaoperaatiot($drinkki, $muunimi1, $muunimi2, $params){
        $drinkki->paivita();
        MuuNimi::poistaPerusteellaDrinkkiID($drinkki->id);
        if($muunimi1 != null){
            $muunimi1->tallenna();
        }
        if($muunimi2 != null){
            $muunimi2->tallenna();
        }
        Drinkinainesosat::poistaPerusteellaDrinkkiID($drinkki->id);
        EhdotusController::tallennaAinesosat($drinkki, $params);
    }
}
