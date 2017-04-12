<?php

/*
 * Kontrolleri, jonka tarkoitus on hoitaa drinkkiehdotuksen käsittely.
 * Kontrollerilla on muun muassa metodit ehdotusnäkymän renderöintiin ja
 * ehdotuksen lisäämiseen tietokantaan.
 */

class EhdotusController extends BaseController {
    /*
     * Metodi, joka noutaa kaikki ainesosat ja drinkkityypit
     * ja antaa ne paramtereiksi kun muodostetaan drinkkien ehdottamiseen
     * suunniteltua näkymää.
     */

    public static function ehdotusNakyma() {
        self::check_logged_in();
        $tyypit = Drinkkityyppi::kaikki();
        $ainekset = Ainesosa::kaikkiAakkosjarjestyksessa();
        View::make('ehdotus/ehdota.html', array('tyypit' => $tyypit, 'ainekset' => $ainekset));
    }

    /*
     * Metodi, joka lisää käyttäjän luoman ehdotuksen tietokantaan, mikäli
     * ehdotuksessa ei ollut virheitä. Jos ehdotus sisältää virheitä, ohjataan
     * käyttäjä takaisin ehdotus näkymään ja virheilmoitukset listataan.
     */

    public static function lisaa() {
        self::check_logged_in();
        $params = $_POST;
        //luodaan array olioista
        $oliot = self::luoOlioArray($params);
        // tarkistetaan virheet
        $errors = self::tarkistaOlioidenVirheet($oliot);
        // tarkistetaan ainesosien virheet
        $errors = array_merge($errors, self::tarkistaAinesosatJaMaarat($params));
        // ohjataan käyttäjä eteenpäin virheiden määrän perusteella
        if (count($errors) == 0) {
            // tallennetaan oliot
            self::tallennaOliot($oliot);
            self::tallennaAinesosat($oliot[0], $params);
            Redirect::to('/', array('message' => "Ehdotuksesi on lähetetty ylläpitäjän hyväksyttäväksi!"));
        } else {
            // luodaan näkymä uudestaan 
            self::ohjaaTakaisinEhdotusNakymaan($oliot, $errors, $params);
        }
    }

    /*
     * Apumetodi, joka hoitaa drinkki olion luomisen. Eroaa hieman Drinkki::luoDrinkki
     * metodista, joten ei voi suoraan hyödyntää sitä.
     */

    private static function luoDrinkki($params) {
        $drinkki = new Drinkki(array(
            'ensisijainennimi' => $params['nimi'],
            'drinkkityyppi' => Drinkkityyppi::etsiPerusteellaNimi($params['tyyppi'])->id,
            'lasi' => $params['lasi'],
            'kuvaus' => $params['kuvaus'],
            'lampotila' => $params['lampotila'],
            'lisayspaiva' => "Tämä päivä",
            'hyvaksytty' => false,
            'lisaaja' => Kayttaja::haePerusteellaID($_SESSION['user'])->kayttajatunnus
        ));
        return $drinkki;
    }

    /*
     * Metodi, joka luo arrayn syötteenä saaduista parametreista ja palauttaa
     * sen.
     */

    private static function luoOlioArray($params) {
        $oliot = array();
        $oliot[] = self::luoDrinkki($params);
        if (strlen($params['muunimi1']) != 0) {
            $oliot[] = new MuuNimi(array('nimi' => $params['muunimi1']));
        }
        if (strlen($params['muunimi2']) != 0) {
            $oliot[] = new MuuNimi(array('nimi' => $params['muunimi2']));
        }
        return $oliot;
    }

    /*
     * Metodi, jolla voidaan tarkistaa kaikkien olioiden virheet. Tämän 
     * jälkeen palautetaan taulkko virheistä.
     */

    private static function tarkistaOlioidenVirheet($oliot) {
        $errors = array();
        foreach ($oliot as $olio) {
            $errors = array_merge($errors, $olio->virheet());
        }
        return $errors;
    }

    /*
     * Metodi, jolla voidaan tallentaa oliot. Oliot ovat aina tietyssä
     * järjestyksessä taulukossa.
     */

    private static function tallennaOliot($oliot) {
        // laitetaan kaikkien muunimi tyyppisten olioiden drinkki-id kuntoon.
        $drinkki = $oliot[0];
        $drinkki->tallenna();
        foreach ($oliot as $olio) {
            if (is_a($olio, 'MuuNimi')) {
                $olio->drinkki = $drinkki->id;
            }
        }
        // tallennetaan vielä kaikki oliot
        foreach ($oliot as $olio) {
            if (!is_a($olio, 'Drinkki')) {
                $olio->tallenna();
            }
        }
    }

    /*
     * Metodi, jolla ohjaa käyttäjän takaisin ehdotusnäkymään mikäli 
     * tapahtui virhe syötteissä.
     */

    private static function ohjaaTakaisinEhdotusNakymaan($oliot, $errors, $params) {
        $tyypit = Drinkkityyppi::kaikki();
        $ainekset = Ainesosa::kaikkiAakkosjarjestyksessa();
        // määritellään palautteavat muuttujat olioista.
        $drinkki = $oliot[0];
        $muunimi1 = null;
        $muunimi2 = null;
        if (count($oliot) > 1) {
            $muunimi1 = $oliot[1];
        }
        if (count($oliot) > 2) {
            $muunimi2 = $oliot[2];
        }
        View::make('ehdotus/ehdota.html', array('muunimi1' => $muunimi1, 'muunimi2' => $muunimi2, 'drinkki' => $drinkki, 'tyypit' => $tyypit, 'ainekset' => $ainekset, 'errors' => $errors, 'maara1' => $params['maara1'], 'maara2' => $params['maara2'], 'maara3' => $params['maara3'], 'maara4' => $params['maara4'], 'maara5' => $params['maara5']));
    }

    /*
     * Metodi, joka tarkastaa, että ainesosien määrä on vähintään yksi, ja että
     * jokaista ainesosaa kohden on annettu validi määrä.
     */

    private static function tarkistaAinesosatJaMaarat($params) {
        $ainekset = array();
        $errors = array();
        for ($x = 1; $x <= 5; $x++) {
            if ($params['aines' . $x] != null) {
                $errors = array_merge($errors, self::tarkistaMaara($params['maara' . $x]));
                if (in_array($params['aines' . $x], $ainekset)) {
                    $errors[] = "Sama ainesosa valittu useampaan otteeseen!";
                }
                $ainekset[] = $params['aines' . $x];
            }
        }
        if (count($ainekset) == 0) {
            $errors[] = "Vähintään yksi ainesosa on annettava";
        }
        return $errors;
    }

    /*
     * Metodi, joka tarkastaa, että ainesosien määrä on vähintään yksi, ja että
     * jokaista ainesosaa kohden on annettu validi määrä.
     */

    private static function tarkistaMaara($maara) {
        $errors = array();
        if ($maara == null || $maara == '') {
            $errors[] = "Ainesosien määriä puuttuu!";
        }
        if ($maara != null && (is_numeric($maara) == FALSE || $maara * 1 != (int) ($maara * 1))) {
            $errors[] = "Määrän tulee olla kokonaisluku!";
        }
        return $errors;
    }

    /*
     * Metodi, joka tarkastaa, että ainesosien määrä on vähintään yksi, ja että
     * jokaista ainesosaa kohden on annettu validi määrä.
     */

    private static function tallennaAinesosat($drinkki, $params) {
        for ($x = 1; $x <= 5; $x++) {
            if ($params['aines' . $x] != null) {
                Ainesosa::etsiPerusteellaNimi($params['aines' . $x])->id;
                Drinkinainesosat::lisaaDrinkinAinesosa($drinkki->id, Ainesosa::etsiPerusteellaNimi($params['aines' . $x])->id, $params['maara' . $x]);
            }
        } 
    }

}
