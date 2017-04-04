<?php

/**
 * Tämä luokka on malli muuNimelle, eli drinkin toisille nimille.
 * Se sisältää tietokantaoperaatiot ja muutaman muun 
 * tarvittavan metodin.
 *
 * @author martin vidjeskog
 */
class MuuNimi extends BaseModel {

    // mallin atribuutit
    public $id, $nimi, $drinkki, $validators;

    // konstruktori
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validoiNimi');
    }

    // hae kaikki muutNimet tietokannasta
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM muunimi');
        $query->execute();
        $rows = $query->fetchAll();
        $nimet = array();

        foreach ($rows as $row) {
            $nimet[] = new MuuNimi(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'drinkki' => $row['drinkki']
            ));
        }

        return $nimet;
    }

    // hae muuNimi, jolla tietty id
    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM muunimi WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $nimi = new MuuNimi(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'drinkki' => $row['drinkki']
            ));
            return $nimi;
        }

        return null;
    }

    // hae muuNimi, jolla tietty drinkin id
    public static function findByDrinkId($id) {
        $query = DB::connection()->prepare('SELECT * FROM muunimi WHERE drinkki = :id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();
        $nimet = array();

        foreach ($rows as $row) {
            $nimet[] = new MuuNimi(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'drinkki' => $row['drinkki']
            ));
        }

        if (empty($nimet)) {
            return null;
        }

        return $nimet;
    }

    /*
     * Metodi, joka poistaa muunimi tietokannasta kaikki nimet, joilla on
     * tietty drinkin id.
     */

    public function poistaPerusteellaID($drinkkiID) {
        $query = DB::connection()->prepare('DELETE FROM MuuNimi WHERE drinkki = :drinkki');
        $query->execute(array('drinkki' => $drinkkiID));
    }

    /*
     * Metodi, jokaa lisää muunimen tietokantaan.
     */

    public function tallenna() {
        $query = DB::connection()->prepare('INSERT INTO MuuNimi(nimi, drinkki) VALUES (:nimi, :drinkki)');
        $query->execute(array('nimi' => $this->nimi, 'drinkki' => $this->drinkki));
    }

    /*
     * Metodi, joka validoi nimen.
     */

    public function validoiNimi() {
        $errors = array();
        if (strlen($this->nimi) < 3) {
            $errors[] = 'Nimen pituuden tulee olla vähintään kolme merkkiä';
        }
        if (strlen($this->nimi) > 50) {
            $errors[] = 'Nimen pituuden tulee olla korkeintaan 50 merkkiä';
        }
        return $errors;
    }

    /*
     *  Metodi, joka käy läpi kaikki validointi funktiot.
     */

    public function virheet() {
        $errors = array();
        foreach ($this->validators as $metodi) {
            $validator_errors = $this->{$metodi}();
            $errors = array_merge($errors, $validator_errors);
        }
        return $errors;
    }

}
