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
        $params = $_POST;
        //luodaan array olioista
        $oliot = self::luoOlioArray($params);
        // tarkistetaan virheet
        $errors = self::tarkistaOlioidenVirheet($oliot);
        // ohjataan käyttäjä eteenpäin virheiden määrän perusteella
        if (count($errors) == 0) {
            // tallennetaan oliot
            self::tallennaOliot($oliot);
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
            'lisaaja' => Kayttaja::findById($_SESSION['user'])->kayttajatunnus
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
        if(strlen($params['muunimi1']) != 0){
            $oliot[] = new MuuNimi(array('nimi' => $params['muunimi1']));
        }
        if(strlen($params['muunimi2']) != 0){
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
        // ensin joudutaan laittamaan kaikkien muunimi tyyppisten olioiden
        // id kuntoon.
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
        $ainekset = Ainesosa::all();
        // määritellään palautteavat muuttujat olioista.
        $drinkki = $oliot[0];
        $muunimi1 = $oliot[1];
        $muunimi2 = $oliot[2];
        View::make('ehdotus/ehdota.html', array('muunimi1' => $muunimi1, 'muunimi2' => $muunimi2, 'drinkki' => $drinkki, 'tyypit' => $tyypit, 'ainekset' => $ainekset, 'errors' => $errors, 'maara1' => $params['maara1'], 'maara2' => $params['maara2'], 'maara3' => $params['maara3'], 'maara4' => $params['maara4'], 'maara5' => $params['maara5']));
    }

}
