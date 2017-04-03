<?php

/*
 * Kontrolleri, joka hoitaa käyttäjien käsittelyn. Tämä on 
 * vain ylläpitäjille tarkoitettu.
 */

class KayttajaController extends BaseController {

    // metodi, joka hoitaa rekisteröityneiden käyttäjien listaamisen
    public static function index() {
        $kayttajat = Kayttaja::kaikkiEiYllapitajat();
        View::make('muut/kayttajat.html', array('kayttajat' => $kayttajat));
    }

    // metodi, joka hoitaa rekisteröitymisnäkymän renderöinnin
    public static function rekisteroitymisNakyma() {
        View::make('tili/rekisteroidy.html');
    }

    // metodi joka suorittaa toiminnot kun käyttäjä yrittää kirjautumista sisään
    public static function hoidaSisaanKirjautuminen() {
        $params = $_POST;
        $user = Kayttaja::varmistaKirjautumistiedot($params['username'], $params['password']);
        if (!$user) {
            $errors = array();
            $errors[] = "Väärä käyttäjätunnus tai salasana!";
            Redirect::to('/', array('errors' => $errors, 'username' => $params['username']));
        } else {
            $_SESSION['user'] = $user->id;
            Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $user->kayttajatunnus . '!'));
        }
    }

    // metodi joka suorittaa toiminnot kun käyttäjä kirjautuu ulos
    public static function hoidaUlosKirjautuminen() {
        $params = $_POST;
        $_SESSION['user'] = null;
        Redirect::to('/', array('message' => 'Olet kirjautunut ulos!'));
    }

    // metodi joka suorittaa toiminnot kun käyttäjä haluaa poistaa tilinsä
    public static function poistaKayttajatili() {
        $id = $_SESSION['user'];
        Kayttaja::poistaKayttaja($id);
        $_SESSION['user'] = null;
        Redirect::to('/', array('message' => 'Käyttäjätilisi on poistettu!'));
    }

    // metodi joka tekee vaadittavat toimenpiteet uuden käyttäjätilin luomiseksi 
    public static function luoKayttajatili() {
        $params = $_POST;
        $kayttaja = new Kayttaja(array(
            'etunimi' => $params['etunimi'],
            'sukunimi' => $params['sukunimi'],
            'sahkoposti' => $params['sahkoposti'],
            'kayttajatunnus' => $params['kayttajatunnus'],
            'salasana' => $params['salasana1']
        ));
        $errors = $kayttaja->virheet();
        if(strcmp($params['salasana1'], $params['salasana2']) != 0){
            $errors[] = "Salasanasi eivät täsmää";
        }
        if (count($errors) == 0) {
            $kayttaja->lisaaKayttaja();
            // Ohjataan käyttäjä sovelluksen etusivulle
            Redirect::to('/', array('message' => "Käyttäjätililisi on luotu. Voit nyt kirjautua sisään."));
        } else {
            // Käyttäjätilissä oli jotain vikaa
            View::make('tili/rekisteroidy.html', array('kayttaja' => $kayttaja, 'errors' => $errors));
        }
    }

    // metodi, joka renderöi käyttäjätietojen muokkausvalikon käyttäjälle
    public static function muokkausNakyma() {
        if ($_SESSION['user']) {
            $kayttaja = Kayttaja::findById($_SESSION['user']);
            View::make('tili/asetukset.html', array('kayttaja' => $kayttaja));
        }
    }

}
