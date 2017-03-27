<?php

/**
 * Tämä luokka on malli ainesosalle.
 * Se sisältää tietokantaoperaatiot ja muutaman muun 
 * tarvittavan metodin.
 *
 * @author martin vidjeskog
 */
class Ainesosa extends BaseModel {

    // mallin atribuutit
    public $id, $nimi, $kuvaus;

    // konstruktori
    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    // hae kaikki ainesosat tietokannasta
    public static function all() {
        // haetaan kaikki tiedot ainesosa-taulusta
        $query = DB::connection()->prepare('SELECT * FROM ainesosa');
        $query->execute();
        $rows = $query->fetchAll();
        $ainekset = array();

        foreach ($rows as $row) {
            $ainekset[] = new Ainesosa(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus']
            ));
        }

        return $ainekset;
    }
    
    // hae kaikki ainesosat tietokannasta nimen mukaan aakkosjärejstyksessä
    public static function allAlphabetically() {
        // haetaan kaikki tiedot ainesosa-taulusta
        $query = DB::connection()->prepare('SELECT * FROM ainesosa ORDER BY nimi');
        $query->execute();
        $rows = $query->fetchAll();
        $ainekset = array();

        foreach ($rows as $row) {
            $ainekset[] = new Ainesosa(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus']
            ));
        }

        return $ainekset;
    }

    // hae ainesosa, jolla tietty id
    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM ainesosa WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $aines = new Ainesosa(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus']
            ));
            return $aines;
        }

        return null;
    }

}
