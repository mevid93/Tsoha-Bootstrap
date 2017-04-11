<?php

/**
 * Tämä luokka on malli ainesosan ja drinkin yhdistävälle liitostaululle.
 * Se sisältää tarvittavat tietokantaoperaatiot.
 *
 */
class Drinkinainesosat extends BaseModel {  // HUOM! Ei voi käyttää camelcase tyyliä, sillä ei jostain syystä muuten toimi.
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

}
