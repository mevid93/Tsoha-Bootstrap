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
        $ainekset = Ainesosa::all();
        View::make('ehdotus/ehdota.html', array('tyypit' => $tyypit, 'ainekset' => $ainekset));
    }

    /*
     * Metodi, joka lisää käyttäjän luoman ehdotuksen tietokantaan, mikäli
     * ehdotuksessa ei ollut virheitä. Jos ehdotus sisältää virheitä, ohjataan
     * käyttäjä takaisin ehdotus näkymään ja virheilmoitukset listataan.
     */

    public static function lisaa() {
        $params = $_POST;
        //luodaan drinkki
        $drinkki = self::luoDrinkki($params);
        //luodaan muutnimet
        $muunimi1 = self::luoMuuNimi($params['muunimi1']);
        $muunimi2 = self::luoMuuNimi($params['muunimi2']);
        // tarkistetaan virheet drinkissä
        $errors = $drinkki->virheet();
        // tarkistetaan virheet muunimissä
        $errors = array_merge($errors, self::virheMuuNimessa($muunimi1));
        $errors = array_merge($errors, self::virheMuuNimessa($muunimi2));
        // ohjataan käyttäjä eteenpäin virheiden määrän perusteella
        if (count($errors) == 0) {
            $drinkki->tallenna();
            self::tallennaMuuNimi($muunimi1, $drinkki);
            self::tallennaMuuNimi($muunimi2, $drinkki);
            Redirect::to('/', array('message' => "Ehdotuksesi on lähetetty ylläpitäjän hyväksyttäväksi!"));
        } else {
            $tyypit = Drinkkityyppi::kaikki();
            $ainekset = Ainesosa::all();
            View::make('ehdotus/ehdota.html', array('muunimi1' => $muunimi1, 'muunimi2' => $muunimi2, 'drinkki' => $drinkki, 'tyypit' => $tyypit, 'ainekset' => $ainekset, 'errors' => $errors));
        }
    }

    /*
     * Apumetodi, joka hoitaa drinkki olion luomisen.
     */

    private static function luoDrinkki($params) {
        $drinkki = new Drinkki(array(
            'ensisijainennimi' => $params['nimi'],
            'drinkkityyppi' => Drinkkityyppi::findByName($params['tyyppi'])->id,
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
     * Apumetodi, joka hoitaa muunimi olion luomisen.
     */

    private static function luoMuuNimi($nimi) {
        if ($nimi == null) {
            return null;
        }
        $muunimi = new MuuNimi(array(
            'nimi' => $nimi
        ));
        return $muunimi;
    }

    /*
     * Apumetodi, joka tarkistaa virheet muunimi olioista.
     */

    private static function virheMuuNimessa($muunimi) {
        if ($muunimi == null) {
            return array();
        }
        return $muunimi->virheet();
    }
    
    /*
     * Apumetodi, joka tallentaa muun nimen.
     */

    private static function tallennaMuuNimi($muunimi, $drinkki) {
        if ($muunimi != null) {
            $muunimi->drinkki = $drinkki->id;
            if($muunimi->drinkki != null){
                $muunimi->tallenna();
            }
        }
    }

}
