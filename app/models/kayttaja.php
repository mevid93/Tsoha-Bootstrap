<?php

/**
 * Tämä luokka on malli käyttäjälle. Luokka toteuttaa tarvittavat 
 * tietokantaoperaatiot. Se sisältää lisäksi muutaman muun metodin kuten esim.
 * validointimetodit.
 */
class Kayttaja extends BaseModel {

    // atribuutit
    public $id, $etunimi, $sukunimi, $sahkoposti, $kayttajatunnus, $salasana, $yllapitaja;

    /*
     * Konstruktori metodi.
     */

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validoiEtunimi', 'validoiSukunimi', 'validoiSahkoposti', 'validoiKayttajatunnus', 'validoiSalasana');
    }

    /*
     * Metodi, joka tallentaa käyttäjän tietokantaan.
     */

    public function lisaaKayttaja() {
        $query = DB::connection()->prepare('INSERT INTO Kayttaja(etunimi, sukunimi, sahkoposti, kayttajatunnus, salasana)
                                            VALUES (:etunimi, :sukunimi, :sahkoposti, :kayttajatunnus, :salasana)');
        $query->execute(array('etunimi' => $this->etunimi, 'sukunimi' => $this->sukunimi, 'sahkoposti' => $this->sahkoposti,
            'kayttajatunnus' => $this->kayttajatunnus, 'salasana' => $this->salasana));
    }

    /*
     * Validointi metodi, joka tarkistaa etunimen.
     */

    public function validoiEtunimi() {
        $errors = array();
        $errors = parent::validoi_string_epätyhjä($this->etunimi, $errors, 'Etunimi ei saa olla tyhjä');
        $errors = parent::validoi_string_pituus($this->etunimi, $errors, 3, 50, 'Etunimen pituuden tulee olla vähintään 3 merkkiä', 'Etunimen pituus saa olla korkeintaan 50 merkkiä');
        return $errors;
    }

    /*
     * Validointi metodi, joka tarkistaa sukunimen.
     */

    public function validoiSukunimi() {
        $errors = array();
        $errors = parent::validoi_string_epätyhjä($this->sukunimi, $errors, 'Sukunimi ei saa olla tyhjä');
        $errors = parent::validoi_string_pituus($this->sukunimi, $errors, 3, 50, 'Sukunimen pituuden tulee olla vähintään 3 merkkiä', 'Sukunimen pituus saa olla korkeintaan 50 merkkiä');
        return $errors;
    }

    /*
     * Validointi metodi, joka tarkistaa sähkopostin.
     */

    public function validoiSahkoposti() {
        $errors = array();
        $errors = parent::validoi_string_epätyhjä($this->sahkoposti, $errors, 'Sähkopostiosoite ei saa olla tyhjä');
        $errors = parent::validoi_string_pituus($this->sahkoposti, $errors, 10, 120, 'Sähköpostiosoitteen pituuden tulee olla vähintään 10 merkkiä', 'Sähköpostiosoitteen pituus saa olla korkeintaan 120 merkkiä');
        return $errors;
    }

    /*
     * Validointi metodi, joka tarkistaa käyttäjätunnuksen. 
     */

    public function validoiKayttajatunnus() {
        $errors = array();
        $errors = parent::validoi_string_epätyhjä($this->kayttajatunnus, $errors, 'Käyttäjätunnus ei saa olla tyhjä');
        $errors = parent::validoi_string_pituus($this->kayttajatunnus, $errors, 3, 20, 'Käyttäjätunnuksen pituuden tulee olla vähintään 3 merkkiä', 'Käyttäjätunnuksen pituus saa olla korkeintaan 20 merkkiä');
        if (Kayttaja::haePerusteellaKayttajatunnus($this->kayttajatunnus)) {
            $errors[] = 'Käyttäjätunnus on jo käytössä.';
        }
        return $errors;
    }

    /*
     * Validointi metodi, joka tarkistaa salasanan. 
     */

    public function validoiSalasana() {
        $errors = array();
        $errors = parent::validoi_string_epätyhjä($this->salasana, $errors, 'Salasana ei saa olla tyhjä');
        $errors = parent::validoi_string_pituus($this->salasana, $errors, 3, 20, 'Salasanan pituuden tulee olla vähintään 6 merkkiä', 'Salasanan pituus saa olla korkeintaan 50 merkkiä');
        return $errors;
    }

    /*
     * Metodi, joka hakee kaikki tavalliset käyttäjät tietokannasta.
     */

    public static function kaikkiTavallisetKayttajat() {
        $query = DB::connection()->prepare('SELECT * FROM kayttaja WHERE yllapitaja = false');
        $query->execute();
        $rows = $query->fetchAll();
        $kayttajat = array();
        foreach ($rows as $row) {
            $kayttajat[] = self::luoKayttaja($row);
        }
        return $kayttajat;
    }

    /*
     * Metodi, joka hakee käyttäjän, jolla on tietty id.
     */

    public static function haePerusteellaID($id) {
        $query = DB::connection()->prepare('SELECT * FROM kayttaja WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        if ($row) {
            $kayttaja = self::luoKayttaja($row);
            return $kayttaja;
        }
        return null;
    }

    /*
     * Metodi, joka hakee käyttäjän, jolla on tietty käyttäjätunnus.
     */

    public static function haePerusteellaKayttajatunnus($name) {
        $query = DB::connection()->prepare('SELECT * FROM kayttaja WHERE kayttajatunnus = :kayttajatunnus LIMIT 1');
        $query->execute(array('kayttajatunnus' => $name));
        $row = $query->fetch();
        if ($row) {
            $kayttaja = self::luoKayttaja($row);
            return $kayttaja;
        }
        return null;
    }

    /*
     * Metodi, joka luo käyttäjä olion.
     */

    public static function luoKayttaja($row) {
        $kayttaja = new Kayttaja(array(
            'id' => $row['id'],
            'etunimi' => $row['etunimi'],
            'sukunimi' => $row['sukunimi'],
            'sahkoposti' => $row['sahkoposti'],
            'kayttajatunnus' => $row['kayttajatunnus'],
            'salasana' => $row['salasana'],
            'yllapitaja' => $row['yllapitaja']
        ));
        return $kayttaja;
    }

    /*
     * Metodi, joka poistaa käyttäjän, jolla on tietty id.
     */

    public static function poistaKayttaja($id) {
        $query = DB::connection()->prepare('DELETE FROM kayttaja WHERE id = :id');
        $query->execute(array('id' => $id));
    }

    /*
     * Metodi, joka tarkistaa käyttäjätunnuksen ja salasanan ja palauttaa sitä
     * vastaavan käyttäjän, mikäli se on olemassa.
     */

    public static function varmistaKirjautumistiedot($username, $password) {
        $query = DB::connection()->prepare('SELECT * FROM kayttaja WHERE kayttajatunnus = :kayttajatunnus AND salasana = :salasana LIMIT 1');
        $query->execute(array('kayttajatunnus' => $username, 'salasana' => $password));
        $row = $query->fetch();
        if ($row) {
            $kayttaja = self::luoKayttaja($row);
            return $kayttaja;
        }
        return null;
    }

}
