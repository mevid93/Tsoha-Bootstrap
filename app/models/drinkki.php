<?php

/**
 * Tämä luokka on malli drinkille.
 * Se sisältää tietokantaoperaatiot ja muutaman muun 
 * tarvittavan metodin.
 *
 * @author martin vidjeskog
 */
class Drinkki extends BaseModel {

    // mallin atribuutit
    public $id, $ensisijainennimi, $muutnimet, $lasi, $kuvaus, $lampotila, $lisayspaiva, $lisaaja, $hyvaksytty, $drinkkityyppi;

    // konstruktori
    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    // hae kaikki hyväksytyt drinkit tietokannasta
    public static function etsiKaikkiHyvaksytytAakkosjarjestyksessa() {
        // haetaan kaikki tiedot Drinkkit-taulusta
        $query = DB::connection()->prepare('SELECT * FROM Drinkki WHERE hyvaksytty = true ORDER BY ensisijainennimi');
        $query->execute();
        $rows = $query->fetchAll();
        $drinkit = array();

        foreach ($rows as $row) {
            $drinkit[] = new Drinkki(array(
            'id' => $row['id'],
            'ensisijainennimi' => $row['ensisijainennimi'],
            'muutnimet' => MuuNimi::findByDrinkId($row['id']),    
            'lasi' => $row['lasi'],
            'kuvaus' => $row['kuvaus'],
            'lampotila' => $row['lampotila'],
            'lisayspaiva' => $row['lisayspaiva'],
            'lisaaja' => $row['lisaaja'],
            'hyvaksytty' => $row['hyvaksytty'],
            'drinkkityyppi' => Drinkkityyppi::find($row['drinkkityyppi'])->nimi
            ));
        }

        return $drinkit;
    }
    
    // hae kaikki hyväksytyt drinkit tietokannasta
    public static function etsiKaikkiHyvaksytytDrinkkityypinPerusteella() {
        // haetaan kaikki tiedot Drinkkit-taulusta
        $query = DB::connection()->prepare('SELECT * FROM Drinkki WHERE hyvaksytty = true ORDER BY drinkkityyppi');
        $query->execute();
        $rows = $query->fetchAll();
        $drinkit = array();

        foreach ($rows as $row) {
            $drinkit[] = new Drinkki(array(
            'id' => $row['id'],
            'ensisijainennimi' => $row['ensisijainennimi'],
            'muutnimet' => MuuNimi::findByDrinkId($row['id']),    
            'lasi' => $row['lasi'],
            'kuvaus' => $row['kuvaus'],
            'lampotila' => $row['lampotila'],
            'lisayspaiva' => $row['lisayspaiva'],
            'lisaaja' => $row['lisaaja'],
            'hyvaksytty' => $row['hyvaksytty'],
            'drinkkityyppi' => Drinkkityyppi::find($row['drinkkityyppi'])->nimi
            ));
        }

        return $drinkit;
    }
    
    // hae kaikki ehdotetut drinkit tietokannasta
    public static function kaikkiHyvaksytyt() {
        // haetaan kaikki tiedot Drinkkit-taulusta
        $query = DB::connection()->prepare('SELECT * FROM Drinkki WHERE hyvaksytty = false');
        $query->execute();
        $rows = $query->fetchAll();
        $drinkit = array();

        foreach ($rows as $row) {
            $drinkit[] = new Drinkki(array(
            'id' => $row['id'],
            'ensisijainennimi' => $row['ensisijainennimi'],
            'muutnimet' => MuuNimi::findByDrinkId($row['id']),    
            'lasi' => $row['lasi'],
            'kuvaus' => $row['kuvaus'],
            'lampotila' => $row['lampotila'],
            'lisayspaiva' => $row['lisayspaiva'],
            'lisaaja' => $row['lisaaja'],
            'hyvaksytty' => $row['hyvaksytty'],
            'drinkkityyppi' => Drinkkityyppi::find($row['drinkkityyppi'])->nimi
            ));
        }

        return $drinkit;
    }

    // hae drinkki, jolla tietty id
    public static function etsiPerusteellaID($id) {
        $query = DB::connection()->prepare('SELECT * FROM Drinkki WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $drinkki = new Drinkki(array(
                'id' => $row['id'],
                'ensisijainennimi' => $row['ensisijainennimi'],
                'muutnimet' => MuuNimi::findByDrinkId($row['id']), 
                'lasi' => $row['lasi'],
                'kuvaus' => $row['kuvaus'],
                'lampotila' => $row['lampotila'],
                'lisayspaiva' => $row['lisayspaiva'],
                'lisaaja' => $row['lisaaja'],
                'hyvaksytty' => $row['hyvaksytty'],
                'drinkkityyppi' => Drinkkityyppi::find($row['drinkkityyppi'])->nimi
            ));
            return $drinkki;
        }

        return null;
    }
    
    // hae drinkit joiden nimeen sisältyy hakutermi
    public static function etsiNimenPerusteella($nimi) {
        $query = DB::connection()->prepare("SELECT DISTINCT Drinkki.* FROM Drinkki, MuuNimi WHERE ensisijainennimi LIKE '%'|| :nimi ||'%' OR (Drinkki.id = MuuNimi.drinkki AND MuuNimi.nimi LIKE '%'|| :nimi ||'%')");
        $query->execute(array('nimi' => $nimi));
        $rows = $query->fetchAll();
        $drinkit = array();
        
        foreach ($rows as $row) {
            $drinkit[] = new Drinkki(array(
            'id' => $row['id'],
            'ensisijainennimi' => $row['ensisijainennimi'],
            'muutnimet' => MuuNimi::findByDrinkId($row['id']),    
            'lasi' => $row['lasi'],
            'kuvaus' => $row['kuvaus'],
            'lampotila' => $row['lampotila'],
            'lisayspaiva' => $row['lisayspaiva'],
            'lisaaja' => $row['lisaaja'],
            'hyvaksytty' => $row['hyvaksytty'],
            'drinkkityyppi' => Drinkkityyppi::find($row['drinkkityyppi'])->nimi
            ));
        }

        return $drinkit;
    }
    
    // hae drinkit joihin on käytetty jotain tiettyä ainesosaa
    public static function etsiAinesosanPerusteella($aines) {
        $query = DB::connection()->prepare("SELECT Drinkki.* FROM Drinkki, Ainesosa, DrinkinAinesosat WHERE DrinkinAinesosat.drinkki = Drinkki.id AND DrinkinAinesosat.ainesosa = Ainesosa.id AND Ainesosa.nimi LIKE '%'|| :aines ||'%'");
        $query->execute(array('aines' => $aines));
        $rows = $query->fetchAll();
        $drinkit = array();
        
        foreach ($rows as $row) {
            $drinkit[] = new Drinkki(array(
            'id' => $row['id'],
            'ensisijainennimi' => $row['ensisijainennimi'],
            'muutnimet' => MuuNimi::findByDrinkId($row['id']),    
            'lasi' => $row['lasi'],
            'kuvaus' => $row['kuvaus'],
            'lampotila' => $row['lampotila'],
            'lisayspaiva' => $row['lisayspaiva'],
            'lisaaja' => $row['lisaaja'],
            'hyvaksytty' => $row['hyvaksytty'],
            'drinkkityyppi' => Drinkkityyppi::find($row['drinkkityyppi'])->nimi
            ));
        }

        return $drinkit;
    }
    
    // lisää drinkki tietokantaan
    public function tallenna(){
        $query = DB::connection()->prepare('INSERT INTO Drinkki(ensisijainenNimi, lasi, kuvaus, lampotila, lisayspaiva, lisaaja, drinkkityyppi)
                                            VALUES (:ensisijainenNimi, :lasi, :kuvaus, :lampotila, NOW(), :lisaaja, :drinkkityyppi)');
        $query->execute(array('ensisijainenNimi' => $this->ensisijainennimi, 'lasi' => $this->lasi, 'kuvaus' => $this->kuvaus, 'lampotila' => $this->lampotila, 
            'lisaaja' => $this->lisaaja, 'drinkkityyppi' => $this->drinkkityyppi));
    
    }
    
    // poista drinkki tietokannasta
    public function poista(){
        $query = DB::connection()->prepare('DELETE FROM Drinkki WHERE id = :id');
        $query->execute(array('id' => $this->id));
    
    }
    
    // poista drinkki tietokannasta
    public function merkitseHyvaksytyksi(){
        $query = DB::connection()->prepare('UPDATE Drinkki SET hyvaksytty = true WHERE id = :id');
        $query->execute(array('id' => $this->id));
    
    }

}
