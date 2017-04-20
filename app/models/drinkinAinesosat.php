<?php

/**
 * Tämä luokka on malli ainesosan ja drinkin yhdistävälle liitostaululle.
 * Se sisältää tarvittavat tietokantaoperaatiot.
 *
 */
class Drinkinainesosat extends BaseModel {  // HUOM! Ei voi käyttää camelcase tyyliä, sillä ei jostain syystä muuten toimi.

    public $ainesosanNimi, $ainesosaID, $drinkkiID, $maara;

    /**
     *  Konstruktori metodi
     * 
     * @param array $attributes lista Drinkinainesosat parametreista
     */
    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    /**
     * Metodi, joka hakee kaikki tietokannasta kaikki ainesosat,
     * jotka kuuluvat johonkin tiettyyn drinkkiin (id).
     * 
     * @param integer $id drinkin tunnus
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

    /**
     * Metodi, joka hakee kaikki tietokannasta kaikki ainesosat,
     * jotka kuuluvat johonkin tiettyyn drinkkiin (id), ja luo näistä
     * taulukon DrinkinAinesosat olioita.
     * 
     * @param integer $id drinkin tunnus
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

    /**
     * Metodi, joka lisää liitostauluun uuden rivin (ainesosa, drinkki,määrä)
     * 
     * @param integer $drinkkiId drinkin tunnus
     * @param integer $ainesID ainesosan tunnus
     * @param integer $maara ainesosan määrä drinkissä
     */
    public static function lisaaDrinkinAinesosa($drinkkiId, $ainesID, $maara) {
        $query = DB::connection()->prepare('INSERT INTO drinkinainesosat (drinkki, ainesosa, maara) VALUES(:drinkki, :ainesosa, :maara)');
        $query->execute(array('drinkki' => $drinkkiId, 'ainesosa' => $ainesID, 'maara' => $maara));
    }

    /**
     * Metodi, joka poistaa liitostaulusta kaikki rivit, joilla on drinkin id.
     * 
     * @param integer $id drinkin tunnus
     */
    public static function poistaPerusteellaDrinkkiID($id) {
        $query = DB::connection()->prepare('DELETE FROM drinkinainesosat WHERE drinkki = :id');
        $query->execute(array('id' => $id));
    }

    /**
     * Metodi, joka palautaa listan ainesosien määristä.
     * 
     * @param integer $drinkkiID drinkin tunnus
     */
    public static function ainesosienMaarat($drinkkiID) {
        $query = DB::connection()->prepare('SELECT drinkinainesosat.* FROM drinkinainesosat WHERE drinkki = :drinkki');
        $query->execute(array('drinkki' => $drinkkiID));
        $rows = $query->fetchAll();
        foreach ($rows as $row) {
            $maarat[] = $row['maara'];
        }
        while (count($maarat) < 5) {
            $maarat[] = null;
        }
        return $maarat;
    }

}
