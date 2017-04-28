<?php

/**
 * Tämä luokka on malli ainesosalle. Se sisältää muun muassa tarvittavat
 * tietokantaoperaatiot, syötteiden validoinnit ja muutaman muun oleellisen
 * metodin. 
 */
class Ainesosa extends BaseModel {

    // atribuutit
    public $id, $nimi, $kuvaus;

    /**
     *  Konstruktori metodi.
     * 
     * @param array $attributes lista ainesosan parametreja
     */
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validoiNimi', 'validoiKuvaus');
    }

    /**
     * Metodi, joka tallentaa ainesosan tietokantaan.
     */
    public function tallenna() {
        $query = DB::connection()->prepare('INSERT INTO Ainesosa(nimi, kuvaus) VALUES (:nimi, :kuvaus)');
        $query->execute(array('nimi' => $this->nimi, 'kuvaus' => $this->kuvaus));
    }

    /**
     * Metodi, joka muokkaa tietokannassa olevaa ainesosaa.
     */
    public function muokkaa() {
        $query = DB::connection()->prepare('UPDATE Ainesosa SET nimi = :nimi, kuvaus = :kuvaus WHERE id = :id');
        $query->execute(array('id' => $this->id, 'nimi' => $this->nimi, 'kuvaus' => $this->kuvaus));
    }

    /**
     * Metodi, joka poistaa ainesosan tietokannasta. Samalla joudutaan poistamaan
     * myös kaikki drinkit, joilla on kyseinen ainesosa ja kaikki ainesosaan
     * liittyvät rivit liitostaulusta.
     * 
     * @param integer $id ainesosan tunnus
     */
    public function poista($id) {
        $query = DB::connection()->prepare('SELECT DISTINCT Drinkki.* FROM Drinkki, DrinkinAinesosat, Ainesosa WHERE Drinkki.id = DrinkinAinesosat.drinkki AND DrinkinAinesosat.ainesosa = :id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();
        foreach ($rows as $row){
            $drinkki = Drinkki::luoDrinkki($row);
            $drinkki->poista();
        }
        $query = DB::connection()->prepare('DELETE FROM Ainesosa WHERE id = :id');
        $query->execute(array('id' => $id));
    }

    /**
     * Validointi metodi, joka tarkistaa että nimi on validi.
     */
    public function validoiNimi() {
        $errors = array();
        $errors = parent::validoi_string_epätyhjä($this->nimi, $errors, 'Nimi ei saa olla tyhjä');
        $errors = parent::validoi_string_pituus($this->nimi, $errors, 3, 50, 'Nimen pituuden tulee olla vähintään 3 merkkiä', 'Nimen pituus saa olla korkeintaan 50 merkkiä');
        $aines = self::etsiPerusteellaNimi($this->nimi);
        if ($aines != null && $aines->id != $this->id) {
            $errors[] = 'Kyseinen ainesosan nimi on jo käytössä';
        }
        return $errors;
    }

    /**
     * Validointi metodi, joka tarkistaa että kuvaus on validi.
     */
    public function validoiKuvaus() {
        $errors = array();
        $errors = parent::validoi_string_epätyhjä($this->kuvaus, $errors, 'Ainesosan kuvaus ei saa olla tyhjä');
        $errors = parent::validoi_string_pituus($this->kuvaus, $errors, 20, 400, 'Kuvauksen pituuden tulee olla vähintään 20 merkkiä', 'Kuvauksen pituuden tulee olla korkeintaan 400 merkkiä');
        return $errors;
    }

    /**
     * Metodi, joka hakee kaikki ainesosat tietokannasta.
     */
    public static function kaikki() {
        $query = DB::connection()->prepare('SELECT * FROM ainesosa');
        $query->execute();
        $rows = $query->fetchAll();
        $ainekset = array();
        foreach ($rows as $row) {
            $ainekset[] = self::luoAinesosa($row);
        }
        return $ainekset;
    }

    /**
     * Metodi, joka hakee kaikkiainesosat tietokannasta nimen mukaan aakkosjärejstyksessä
     */
    public static function kaikkiAakkosjarjestyksessa() {
        $query = DB::connection()->prepare('SELECT * FROM ainesosa ORDER BY nimi');
        $query->execute();
        $rows = $query->fetchAll();
        $ainekset = array();
        foreach ($rows as $row) {
            $ainekset[] = self::luoAinesosa($row);
        }
        return $ainekset;
    }

    /**
     * Metodi, joka hakee tietokannasta ainesosan, jolla on tietty id. 
     * 
     * @param integer $id ainesosan tunnus
     */
    public static function etsiPerusteellaID($id) {
        $query = DB::connection()->prepare('SELECT * FROM ainesosa WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        if ($row) {
            $aines = self::luoAinesosa($row);
            return $aines;
        }
        return null;
    }

    /**
     * Metodi, joka hakee tietokannasta ainesosan, jolla on tietty nimi. 
     * 
     * @param string $nimi ainesosan nimi
     */
    public static function etsiPerusteellaNimi($nimi) {
        $query = DB::connection()->prepare('SELECT * FROM ainesosa WHERE nimi = :nimi LIMIT 1');
        $query->execute(array('nimi' => $nimi));
        $row = $query->fetch();
        if ($row) {
            $aines = self::luoAinesosa($row);
            return $aines;
        }
        return null;
    }

    /**
     * Apumetodi, jolla voidaan luoda yksi ainesosa olio.
     * 
     * @param array $row assosiaatiolista ainesosan parametreista
     */
    private static function luoAinesosa($row) {
        $aines = new Ainesosa(array(
            'id' => $row['id'],
            'nimi' => $row['nimi'],
            'kuvaus' => $row['kuvaus']
        ));
        return $aines;
    }

}
