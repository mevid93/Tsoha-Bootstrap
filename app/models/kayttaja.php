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
    public $id, $etunimi, $sukunimi, $sahkposti, $kayttajatunnus, $salasana, $yllapitaja;

    // konstruktori
    public function __construct($attributes) {
        parent::__construct($attributes);
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

}
