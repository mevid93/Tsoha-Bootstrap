<?php

/**
 * Tämä luokka toimii mallina MuuNimelle, eli drinkin toisille mahdollisille
 * nimille. Se sisältää tietokantaoperaatiot muun muassa tietokannasta hakuun,
 * lisäykseen ja poistoon.
 *
 */
class MuuNimi extends BaseModel {

    // atribuutit
    public $id, $nimi, $drinkki;

    /*
     * Luokan konstruktori
     */

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validoiNimi');
    }

    /*
     * Metodi, joka hakee kaikki muut nimet tietokannasta (ei kaiketi tarvita)
     */

    public static function kaikki() {
        $query = DB::connection()->prepare('SELECT * FROM muunimi');
        $query->execute();
        $rows = $query->fetchAll();
        $nimet = array();
        foreach ($rows as $row) {
            $nimet[] = self::luoMuuNimi($row);
        }
        return $nimet;
    }

    /*
     * Metodi, joka etsii muun nimen, jolla on jokin tietty id.
     */

    public static function etsiPerusteellaID($id) {
        $query = DB::connection()->prepare('SELECT * FROM muunimi WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        if ($row) {
            $nimi = self::luoMuuNimi($row);
            return $nimi;
        }
        return null;
    }

    /*
     * Metodi, joka etsii muut nimet drinkille drinkin id:n perusteella. 
     */

    public static function etsiPerusteellaDrinkkiID($id) {
        $query = DB::connection()->prepare('SELECT * FROM muunimi WHERE drinkki = :id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();
        $nimet = array();
        foreach ($rows as $row) {
            $nimet[] = self::luoMuuNimi($row);
        }
        if (empty($nimet)) {
            return null;
        }
        return $nimet;
    }

    /*
     * Apumetodi, joka luo yksittäsien muu nimi olion.
     */

    private static function luoMuuNimi($row) {
        $nimi = new MuuNimi(array(
        'id' => $row['id'],
        'nimi' => $row['nimi'],
        'drinkki' => $row['drinkki']));
        return $nimi;
    }

    /*
     * Metodi, joka poistaa muunimi tietokannasta kaikki nimet, joilla on
     * tietty drinkin id.
     */

    public static function poistaPerusteellaDrinkkiID($drinkkiID) {
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
        parent::validoi_string_epätyhjä($this->nimi, $errors, 'Muu drinkin nimi ei saa olla tyhjä');
        parent::validoi_string_pituus($this->nimi, $errors, 3, 50, 'Nimen pituuden tulee olla vähintään 3 merkkiä', 'Nimen pituuden tulee olla korkeintaan 50 merkkiä');
        return $errors;
    }

}
