<?php

/*
 * Kontrolleri, joka hoitaa käyttäjien käsittelyn. Tämä on 
 * vain ylläpitäjille tarkoitettu.
 */

class KayttajaController extends BaseController {
    /*
     * Metodi, joka hoitaa sellaisten rekisteröityneiden käyttäjien listaamisen, 
     * jotka eivät ole ylläpitäjiä.
     */

    public static function kayttajatNakyma() {
        self::check_admin_logged_in();
        $kayttajat = Kayttaja::kaikkiTavallisetKayttajat();
        View::make('muut/kayttajat.html', array('kayttajat' => $kayttajat));
    }

    /*
     * Metodi, joka hoitaa rekisteröitymisnäkymän renderöinnin.
     */

    public static function rekisteroitymisNakyma() {
        parent::check_already_logged_in();
        View::make('tili/rekisteroidy.html');
    }

    /*
     * Metodi, joka suorittaa sisäänkirjautumisyrityksen edellyttämät
     * toiminnot.
     */

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

    /*
     * Metodi, joka suorittaa uloskirjautumisen.
     */

    public static function hoidaUlosKirjautuminen() {
        $params = $_POST;
        $_SESSION['user'] = null;
        Redirect::to('/', array('message' => 'Olet kirjautunut ulos!'));
    }

    /*
     * Metodi, joka suorittaa toiminnot kun käyttäjä haluaa poistaa tilinsä.
     */

    public static function poistaKayttajatili() {
        $id = $_SESSION['user'];
        Kayttaja::poistaKayttaja($id);
        $_SESSION['user'] = null;
        Redirect::to('/', array('message' => 'Käyttäjätilisi on poistettu!'));
    }

    /*
     * Metodi, joka suorittaa toiminnot kun ylläpitäjä haluaa poistaa
     * jonkun sovelluksen käyttäjän.
     */

    public static function yllapitoPoistaKayttajatili() {
        self::check_admin_logged_in();
        $params = $_POST;
        Kayttaja::poistaKayttaja($params['id']);
        Redirect::to('/kayttajat', array('message' => 'Käyttäjätili poistettu onnistuneesti!'));
    }

    /*
     * Metodi, joka luo uuden käyttäjätilin jos tiedot olivat validit. Muuten
     * käyttäjä ohjataan takaisin rekisteröitymissivulle. 
     */

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
        if (strcmp($params['salasana1'], $params['salasana2']) != 0) {
            $errors[] = "Salasanasi eivät täsmää";
        }
        if (count($errors) == 0) {
            $kayttaja->lisaaKayttaja();
            Redirect::to('/', array('message' => "Käyttäjätililisi on luotu. Voit nyt kirjautua sisään."));
        } else {
            View::make('tili/rekisteroidy.html', array('kayttaja' => $kayttaja, 'errors' => $errors));
        }
    }

    /*
     * Metodi, joka renderöi käyttäjätietojen muokkausvalikon.
     */

    public static function muokkausNakyma() {
        if ($_SESSION['user']) {
            $kayttaja = Kayttaja::haePerusteellaID($_SESSION['user']);
            View::make('tili/asetukset.html', array('kayttaja' => $kayttaja));
        }
    }
    
    /*
     * Metodi, joka tallentaa käyttäjän tiliin tekemät muutokset.
     */

    public static function muokkaaKayttajanAsetukset() {
        self::check_logged_in();
        $params = $_POST;
        $user = Kayttaja::haePerusteellaID($_SESSION['user']);
        $user->etunimi = $params['etunimi'];
        $user->sukunimi = $params['sukunimi'];
        $user->sahkoposti = $params['sahkoposti'];
        $errors = $user->validoiEtunimi();
        $errors = array_merge($errors, $user->validoiSukunimi());
        $errors = array_merge($errors, $user->validoiSahkoposti());
        if (count($errors) == 0) { 
            $user->paivitaMuutokset();
            Redirect::to('/', array('message' => "Muutokset tallennettu."));
        }else{
            View::make('tili/asetukset.html', array('kayttaja' => $user, 'errors' => $errors));
        }
    }

}
