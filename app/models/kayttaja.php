<?php

/**
 * Tämä luokka on malli käyttäjälle.
 * Se sisältää tietokantaoperaatiot ja muutaman muun 
 * tarvittavan metodin.
 *
 * @author martin vidjeskog
 */
class Kayttaja extends BaseModel {

    // mallin atribuutit
    public $id, $etunimi, $sukunimi, $sahkoposti, $kayttajatunnus, $salasana, $yllapitaja, $validators;

    // konstruktori
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validoiEtunimi', 'validoiSukunimi', 'validoiSahkoposti', 'validoiKayttajatunnus', 'validoiSalasana');
    }

    // hae kaikki käyttäjät tietokannasta
    public static function all() {
        // haetaan kaikki tiedot käyttäjä-taulusta
        $query = DB::connection()->prepare('SELECT * FROM kayttaja');
        $query->execute();
        $rows = $query->fetchAll();
        $kayttajat = array();

        foreach ($rows as $row) {
            $kayttajat[] = new Kayttaja(array(
                'id' => $row['id'],
                'etunimi' => $row['etunimi'],
                'sukunimi' => $row['sukunimi'],
                'sahkoposti' => $row['sahkoposti'],
                'kayttajatunnus' => $row['kayttajatunnus'],
                'salasana' => $row['salasana'],
                'yllapitaja' => $row['yllapitaja']
            ));
        }

        return $kayttajat;
    }

    // hae käyttäjä, jolla tietty id
    public static function findById($id) {
        $query = DB::connection()->prepare('SELECT * FROM kayttaja WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
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

        return null;
    }

    // hae käyttäjä, jolla tietty käyttäjätunnus
    public static function findByUserName($name) {
        $query = DB::connection()->prepare('SELECT * FROM kayttaja WHERE kayttajatunnus = :kayttajatunnus LIMIT 1');
        $query->execute(array('kayttajatunnus' => $name));
        $row = $query->fetch();

        if ($row) {
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

        return null;
    }

    // poista käyttäjä, jolla tietty käyttäjätunnus
    public static function poistaKayttaja($id) {
        $query = DB::connection()->prepare('DELETE FROM kayttaja WHERE id = :id');
        $query->execute(array('id' => $id));
    }
    
    // tallenna käyttäjä tietokantaan, jolla tietty käyttäjätunnus
    public function lisaaKayttaja() {
        $query = DB::connection()->prepare('INSERT INTO Kayttaja(etunimi, sukunimi, sahkoposti, kayttajatunnus, salasana)
                                            VALUES (:etunimi, :sukunimi, :sahkoposti, :kayttajatunnus, :salasana)');
        $query->execute(array('etunimi' => $this->etunimi, 'sukunimi' => $this->sukunimi, 'sahkoposti' => $this->sahkoposti,
            'kayttajatunnus' => $this->kayttajatunnus, 'salasana' => $this->salasana));
    }

    // metodi, joka tarkistaa käyttäjätunnuksen ja salasanan ja palauttaa sitä
    // vastaavan käyttäjän, mikäli se on olemassa.
    public static function varmistaKirjautumistiedot($username, $password) {
        $query = DB::connection()->prepare('SELECT * FROM kayttaja WHERE kayttajatunnus = :kayttajatunnus AND salasana = :salasana LIMIT 1');
        $query->execute(array('kayttajatunnus' => $username, 'salasana' => $password));
        $row = $query->fetch();

        if ($row) {
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
        } else {
            return null;
        }
    }

    // validoi etunimi
    public function validoiEtunimi() {
        $errors = array();
        if ($this->etunimi == '' || $this->etunimi == null) {
            $errors[] = 'Etunimi ei saa olla tyhjä';
        }
        if (strlen($this->etunimi) < 3) {
            $errors[] = 'Etunimen pituuden tulee olla vähintään kolme merkkiä';
        }
        return $errors;
    }
    
    // validoi sukunimi
    public function validoiSukunimi() {
        $errors = array();
        if ($this->sukunimi == '' || $this->sukunimi == null) {
            $errors[] = 'Sukunimi ei saa olla tyhjä';
        }
        if (strlen($this->sukunimi) < 3) {
            $errors[] = 'Sukunimen pituuden tulee olla vähintään kolme merkkiä';
        }
        return $errors;
    }

    // validoi sahkoposti
    public function validoiSahkoposti() {
        $errors = array();
        if ($this->sahkoposti == '' || $this->sahkoposti == null) {
            $errors[] = 'Sähköposti ei saa olla tyhjä';
        }
        if (strlen($this->sahkoposti) < 10) {
            $errors[] = 'Sähköpostin pituus pitää olla vähintään 10 merkkiä.';
        }
        return $errors;
    }

    // validoi kayttajatunnus
    public function validoiKayttajatunnus() {
        $errors = array();
        if ($this->kayttajatunnus == '' || $this->kayttajatunnus == null) {
            $errors[] = 'Käyttäjätunnus ei saa olla tyhjä.';
        }
        if (strlen($this->kayttajatunnus) < 3) {
            $errors[] = 'Kuvauksen pituuden tulee olla vähintään 3 merkkiä';
        }
        if (Kayttaja::findByUserName($this->kayttajatunnus)) {
            $errors[] = 'Käyttäjätunnus on jo käytössä.';
        }
        return $errors;
    }
    
    // validoi salasana
    public function validoiSalasana() {
        $errors = array();
        if ($this->salasana == '' || $this->salasana == null) {
            $errors[] = 'Salasana ei saa olla tyhjä.';
        }
        if (strlen($this->salasana) < 6) {
            $errors[] = 'Salasanan pituuden tulee olla vähintään 6 merkkiä';
        }
        return $errors;
    }

    // metodi, joka käy läpi kaikki validointi funtiot
    public function virheet() {
        $errors = array();

        foreach ($this->validators as $metodi) {
            $validator_errors = $this->{$metodi}();
            $errors = array_merge($errors, $validator_errors);
        }

        return $errors;
    }

}
