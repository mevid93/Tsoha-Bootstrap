<?php

/**
 * Tämä luokka on malli drinkityypille. Se sisältää muun muassa 
 * tarvittavat tietokantaoperaatiot ja operaatiot, joilla voidaan
 * validioida käyttäjän syöte. 
 *
 */
class Drinkkityyppi extends BaseModel {

    // atribuutit
    public $id, $nimi, $kuvaus, $validators;

    /*
     * Luokan konstruktori.
     */

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validoiNimi', 'validoiKuvaus');
    }

    /*
     * Metodi, joka hakee kaikki drinkityypit tietokannasta.
     */

    public static function kaikki() {
        $query = DB::connection()->prepare('SELECT * FROM Drinkkityyppi');
        $query->execute();
        $rows = $query->fetchAll();
        $tyypit = array();

        foreach ($rows as $row) {
            $tyypit[] = new Drinkkityyppi(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus']
            ));
        }

        return $tyypit;
    }

    /*
     * Metodi, joka hakee drinkkityypin sen id:n perusteella.
     */

    public static function etsiPerusteellaID($id) {
        $query = DB::connection()->prepare('SELECT * FROM Drinkkityyppi WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $tyyppi = new Drinkkityyppi(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus']
            ));
            return $tyyppi;
        }

        return null;
    }

    /*
     * Metodi, joka hakee drinkkityypin, jolla on tietty nimi.
     */

    public static function etsiPerusteellaNimi($nimi) {
        $query = DB::connection()->prepare('SELECT * FROM Drinkkityyppi WHERE nimi = :nimi LIMIT 1');
        $query->execute(array('nimi' => $nimi));
        $row = $query->fetch();

        if ($row) {
            $tyyppi = new Drinkkityyppi(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus']
            ));
            return $tyyppi;
        }

        return null;
    }

    /*
     * Metodi, joka lisää drinkkityypin tietokantaan.
     */

    public function tallenna() {
        $query = DB::connection()->prepare('INSERT INTO Drinkkityyppi(nimi, kuvaus)
                                            VALUES (:nimi, :kuvaus)');
        $query->execute(array('nimi' => $this->nimi, 'kuvaus' => $this->kuvaus));
    }

    /*
     * Metodi, joka muokkaa tietokannassa olevaa drinkkityyppiä.
     */

    public function muokkaa() {
        $query = DB::connection()->prepare('UPDATE Drinkkityyppi SET nimi = :nimi, kuvaus = :kuvaus WHERE id = :id');
        $query->execute(array('id' => $this->id, 'nimi' => $this->nimi, 'kuvaus' => $this->kuvaus));
    }

    /*
     * Metodi, joka poistaa drinkkityypin tietokannasta. Samalla joudutaan poistamaan
     * myös kaikki drinkit, joilla on kyseinen drinkkityyppi ja muut nimet, joilla on
     * drinkin id.
     */

    public function poista($id) {
        $query = DB::connection()->prepare('DELETE FROM Drinkki WHERE drinkkityyppi = :id');
        $query->execute(array('id' => $id));
        $query = DB::connection()->prepare('DELETE FROM Drinkkityyppi WHERE id = :id');
        $query->execute(array('id' => $id));
    }

    /*
     *  Metodi, joka validoi drinkkityypin nimen.
     */

    public function validoiNimi() {
        $errors = array();
        if ($this->nimi == '' || $this->nimi == null) {
            $errors[] = 'Nimi ei saa olla tyhjä';
        }
        if (strlen($this->nimi) < 3) {
            $errors[] = 'Nimen pituuden tulee olla vähintään kolme merkkiä';
        }
        if (strlen($this->nimi) > 50) {
            $errors[] = 'Nimen pituus saa olla korkeintaan 50 merkkiä';
        }
        return $errors;
    }

    /*
     *  Metodi, joka validoi drinkkityypin kuvauksen.
     */

    public function validoiKuvaus() {
        $errors = array();
        if ($this->kuvaus == '' || $this->kuvaus == null) {
            $errors[] = 'Drinkkityypin kuvaus ei saa olla tyhjä';
        }
        if (strlen($this->kuvaus) < 20) {
            $errors[] = 'Kuvauksen pituuden tulee olla vähintään 20 merkkiä';
        }
        if (strlen($this->kuvaus) > 400) {
            $errors[] = 'Kuvauksen pituuden tulee olla korkeintaan 400 merkkiä';
        }
        return $errors;
    }

    /*
     *  Metodi, joka käy läpi kaikki validointi funtiot.
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
