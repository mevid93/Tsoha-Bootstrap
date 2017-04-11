<?php

/**
 * Tämä luokka on malli drinkille. Se sisältää tietokantaoperaatioita
 * syötteen validointia, luontioperaatioita ja muita drinkkeihin liittyviä
 * tarpeellisia metodeita.
 */
class Drinkki extends BaseModel {

    // mallin atribuutit
    public $id, $ensisijainennimi, $muutnimet, $lasi, $kuvaus, $lampotila, $lisayspaiva, $lisaaja, $hyvaksytty, $drinkkityyppi;

    /*
     * Konstruktori
     */

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validoiNimi', 'validoiLasi', 'validoiKuvaus');
    }

    /*
     * Lisää drinkki tietokantaan.
     */

    public function tallenna() {
        $query = DB::connection()->prepare('INSERT INTO Drinkki(ensisijainenNimi, lasi, kuvaus, lampotila, lisayspaiva, lisaaja, drinkkityyppi)
                                            VALUES (:ensisijainenNimi, :lasi, :kuvaus, :lampotila, NOW(), :lisaaja, :drinkkityyppi)  RETURNING id');
        $query->execute(array('ensisijainenNimi' => $this->ensisijainennimi, 'lasi' => $this->lasi, 'kuvaus' => $this->kuvaus, 'lampotila' => $this->lampotila,
            'lisaaja' => $this->lisaaja, 'drinkkityyppi' => $this->drinkkityyppi));
        $row = $query->fetch();
        $this->id = $row['id'];
    }

    /*
     * Päivitä muutoksia drinkkiin tietokannassa.
     */

    public function paivita() {
        $query = DB::connection()->prepare('UPDATE Drinkki SET ensisijainennimi = :ensisijainennimi, lasi = :lasi, kuvaus = :kuvaus, lampotila = :lampotila, drinkkityyppi = :drinkkityyppi WHERE id = :id');
        $query->execute(array('ensisijainennimi' => $this->ensisijainennimi, 'lasi' => $this->lasi, 'kuvaus' => $this->kuvaus, 'lampotila' => $this->lampotila, 'drinkkityyppi' => $this->drinkkityyppi, 'id' => $this->id));
    }

    /*
     * Poista drinkki tietokannasta ja muut mahdolliset nimet muunimi-tietokannasta
     * Poistetaan myös DrinkinIanesosat taulusta sellaiset johon drinkki liittyy.
     */

    public function poista() {
        MuuNimi::poistaPerusteellaDrinkkiID($this->id); 
        Drinkinainesosat::poistaPerusteellaDrinkkiID($this->id);
        $query = DB::connection()->prepare('DELETE FROM Drinkki WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }

    /*
     * Merkitse jokin tietokannassa oleva resepti hyväksytyksi. 
     */

    public function merkitseHyvaksytyksi() {
        $query = DB::connection()->prepare('UPDATE Drinkki SET hyvaksytty = true WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }

    /*
     * Validoi drinkin nimi.
     */

    public function validoiNimi() {
        $errors = array();
        $errors = parent::validoi_string_epätyhjä($this->ensisijainennimi, $errors, 'Nimi ei saa olla tyhjä');
        $errors = parent::validoi_string_pituus($this->ensisijainennimi, $errors, 3, 50, 'Nimen pituuden tulee olla vähintään 3 merkkiä', 'Nimen pituus saa olla korkeintaan 50 merkkiä');
        return $errors;
    }

    /*
     * Validoi drinkkilasi.
     */

    public function validoiLasi() {
        $errors = array();
        $errors = parent::validoi_string_epätyhjä($this->lasi, $errors, 'Suositeltu lasin nimi ei saa olla tyhjä');
        $errors = parent::validoi_string_pituus($this->lasi, $errors, 3, 20, 'Lasin nimen pituuden tulee olla vähintään 3 merkkiä', 'Lasin nimen pituus saa olla korkeintaan 20 merkkiä');
        return $errors;
    }

    /*
     * Validoi kuvaus.
     */

    public function validoiKuvaus() {
        $errors = array();
        $errors = parent::validoi_string_epätyhjä($this->kuvaus, $errors, 'Drinkin kuvaus ei saa olla tyhjä');
        $errors = parent::validoi_string_pituus($this->kuvaus, $errors, 20, 400, 'Drinkin kuvauksen tulee olla vähintään 20 merkkiä', 'Drinkin kuvauksen pituus saa olla korkeintaan 400 merkkiä');
        return $errors;
    }

    /*
     * Hae kaikki hyväksytyt drinkit tietokannasta aakkosjärjestyksessä.
     */

    public static function etsiKaikkiHyvaksytytAakkosjarjestyksessa() {
        // haetaan kaikki tiedot Drinkkit-taulusta
        $query = DB::connection()->prepare('SELECT * FROM Drinkki WHERE hyvaksytty = true ORDER BY ensisijainennimi');
        $query->execute();
        $rows = $query->fetchAll();
        $drinkit = array();
        foreach ($rows as $row) {
            $drinkit[] = self::luoDrinkki($row);
        }
        return $drinkit;
    }

    /*
     * Hae kaikki hyväksytyt drinkit tietokannasta drinkkityypin perusteella.
     */

    public static function etsiKaikkiHyvaksytytDrinkkityypinPerusteella() {
        // haetaan kaikki tiedot Drinkkit-taulusta
        $query = DB::connection()->prepare('SELECT * FROM Drinkki WHERE hyvaksytty = true ORDER BY drinkkityyppi');
        $query->execute();
        $rows = $query->fetchAll();
        $drinkit = array();
        foreach ($rows as $row) {
            $drinkit[] = self::luoDrinkki($row);
        }
        return $drinkit;
    }

    /*
     * Hae kaikki ehdotetut drinkit tietokannasta.
     */

    public static function kaikkiHyvaksymattomat() {
        // haetaan kaikki tiedot Drinkkit-taulusta
        $query = DB::connection()->prepare('SELECT * FROM Drinkki WHERE hyvaksytty = false');
        $query->execute();
        $rows = $query->fetchAll();
        $drinkit = array();
        foreach ($rows as $row) {
            $drinkit[] = self::luoDrinkki($row);
        }
        return $drinkit;
    }

    /*
     * Hae drinkki, jolla tietty id.
     */

    public static function etsiPerusteellaID($id) {
        $query = DB::connection()->prepare('SELECT * FROM Drinkki WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        if ($row) {
            $drinkki = self::luoDrinkki($row);
            return $drinkki;
        }
        return null;
    }

    /*
     * Hae drinkit joiden nimeen sisältyy hakutermi.
     */

    public static function etsiNimenPerusteella($nimi) {
        $query = DB::connection()->prepare("SELECT DISTINCT Drinkki.* FROM Drinkki, MuuNimi WHERE ensisijainennimi LIKE '%'|| :nimi ||'%' OR (Drinkki.id = MuuNimi.drinkki AND MuuNimi.nimi LIKE '%'|| :nimi ||'%')");
        $query->execute(array('nimi' => $nimi));
        $rows = $query->fetchAll();
        $drinkit = array();
        foreach ($rows as $row) {
            $drinkit[] = self::luoDrinkki($row);
        }
        return $drinkit;
    }

    /*
     * Hae drinkit joihin on käytetty jotain tiettyä ainesosaa.
     */

    public static function etsiAinesosanPerusteella($aines) {
        $query = DB::connection()->prepare("SELECT Drinkki.* FROM Drinkki, Ainesosa, DrinkinAinesosat WHERE DrinkinAinesosat.drinkki = Drinkki.id AND DrinkinAinesosat.ainesosa = Ainesosa.id AND Ainesosa.nimi LIKE '%'|| :aines ||'%'");
        $query->execute(array('aines' => $aines));
        $rows = $query->fetchAll();
        $drinkit = array();
        foreach ($rows as $row) {
            $drinkit[] = self::luoDrinkki($row);
        }
        return $drinkit;
    }

    /*
     * Poista drinkki tietokannasta ja muut mahdolliset nimet.
     */

    public static function poistaKaikkiJoillaDrinkkityyppi($drinkkityyppi) {
        $query = DB::connection()->prepare('SELECT * FROM Drinkki WHERE drinkkityyppi = :drinkkityyppi');
        $query->execute(array('drinkkityyppi' => $drinkkityyppi));
        $rows = $query->fetchAll();
        foreach ($rows as $row) {
            $drinkki = self::luoDrinkki($row);
            $drinkki->poista();
        }
    }

    /*
     * Metodi, joka luo patametrien perusteella yhden drinkki olion ja palauttaa sen.
     */

    public static function luoDrinkki($row) {
        $drinkki = new Drinkki(array(
            'id' => $row['id'],
            'ensisijainennimi' => $row['ensisijainennimi'],
            'muutnimet' => MuuNimi::etsiPerusteellaDrinkkiID($row['id']),
            'lasi' => $row['lasi'],
            'kuvaus' => $row['kuvaus'],
            'lampotila' => $row['lampotila'],
            'lisayspaiva' => $row['lisayspaiva'],
            'lisaaja' => $row['lisaaja'],
            'hyvaksytty' => $row['hyvaksytty'],
            'drinkkityyppi' => Drinkkityyppi::etsiPerusteellaID($row['drinkkityyppi'])->nimi
        ));
        return $drinkki;
    }

}
