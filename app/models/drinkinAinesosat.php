<?php

/**
 * Tämä luokka on malli ainesosan ja drinkin yhdistävälle liitostaululle.
 * Se sisältää tarvittavat tietokantaoperaatiot.
 *
 */
class Drinkinainesosat extends BaseModel {  // HUOM! Ei voi käyttää camelcase tyyliä, sillä ei jostain syystä muuten toimi.
    
    // mallin atribuutit
    public $ainesosanNimi, $ainesosaID, $drinkkiID, $maara;

    /*
     *  Konstruktori metodi
     */

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    /*
     * Metodi, joka hakee kaikki tietokannasta kaikki ainesosat,
     *  jotka kuuluvat johonkin tiettyyn drinkkiin (id).
     */

    public static function haeAinesosat($id) {
        $query = DB::connection()->prepare('SELECT ainesosa.* FROM drinkki, ainesosa, drinkinainesosat WHERE drinkki.id = :id  AND drinkinainesosat.drinkki = drinkki.id AND drinkinainesosat.ainesosa = ainesosa.id');
        $query->execute(array('id' => $id));
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
    
    /*
     * Metodi, joka hakee kaikki tietokannasta kaikki ainesosat,
     * jotka kuuluvat johonkin tiettyyn drinkkiin (id), ja luo näistä
     * taulukon DrinkinAinesosat olioita.
     */

    public static function haeAinesosatOliot($id) {
        $query = DB::connection()->prepare('SELECT DISTINCT ainesosa.nimi AS nimi, ainesosa.id AS id, drinkinainesosat.maara AS maara FROM ainesosa, drinkinainesosat WHERE drinkinainesosat.drinkki = :id AND drinkinainesosat.ainesosa = ainesosa.id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();
        $drinkinAinesosat = array();
        foreach ($rows as $row) {
            $drinkinAinesosat[] = new Drinkinainesosat(array(
               'ainesosanNimi' => $row['nimi'], 
               'ainesosaID' => $row['id'], 
               'drinkkiID' => $id,
               'maara' => $row['maara']
            ));
        }
        return $drinkinAinesosat;
    }

    /*
     * Metodi, joka hakee kaikki drinkit tietokannasta, johon tarvitaan
     * tiettyä ainesosaa. 
     */

    public static function haeDrinkitPerusteellaAinesosa($id) {
        $query = DB::connection()->prepare('SELECT drinkki.* FROM drinkki, ainesosa, drinkinainesosat WHERE drinkki.id = :id  AND drinkinainesosat.drinkki = drinkki.id AND drinkinainesosat.ainesosa = ainesosa.id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();
        foreach ($rows as $row) {
            $drinkit[] = new Drinkki(array(
                'id' => $row['id'],
                'ensisijainennimi' => $row['ensisijainennimi'],
                'muutnimet' => MuuNimi::etsiPerusteellaDrinkkiID($row['id']),
                'lasi' => $row['lasi'],
                'kuvaus' => $row['kuvaus'],
                'lampotila' => $row['lampotila'],
                'lisayspaiva' => $row['lisayspaiva'],
                'lisaaja' => $row['lisaaja'],
                'drinkkityyppi' => Drinkkityyppi::etsiPerusteellaID($row['drinkkityyppi'])->nimi
            ));
        }
        return $drinkit;
    }

    /*
     * Metodi, joka lisää liitostauluun uuden rivin (ainesosa, drinkki,määrä)
     */

    public static function lisaaDrinkinAinesosa($drinkkiId, $ainesID, $maara) {
        $query = DB::connection()->prepare('INSERT INTO drinkinainesosat (drinkki, ainesosa, maara) VALUES(:drinkki, :ainesosa, :maara)');
        $query->execute(array('drinkki' => $drinkkiId, 'ainesosa' => $ainesID, 'maara' => $maara));
    }

    /*
     * Metodi, joka poistaa liitostaulusta kaikki rivit, joilla on drinkin id.
     */

    public static function poistaPerusteellaDrinkkiID($id) {
        $query = DB::connection()->prepare('DELETE FROM drinkinainesosat WHERE drinkki = :id');
        $query->execute(array('id' => $id));
    }
    
    /*
     * Metodi, joka palautaa listan ainesosien määristä.
     */
    
    public static function ainesosienMaarat($drinkkiID){
        $query = DB::connection()->prepare('SELECT drinkinainesosat.* FROM drinkinainesosat WHERE drinkki = :drinkki');
        $query->execute(array('drinkki' => $drinkkiID));
        $rows = $query->fetchAll();
        foreach ($rows as $row) {
            $maarat[] = $row['maara'];
        }
        while(count($maarat) < 5){
            $maarat[] = null;
        }
        return $maarat;
    }

}
