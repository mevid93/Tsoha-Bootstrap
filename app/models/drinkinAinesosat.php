<?php

/**
 * Tämä luokka on malli ainesosan ja drinkin yhdistävälle liitostaululle.
 * Se sisältää tietokantaoperaatiot tilanteille, jossa halutaan löytää
 * kaikki ainekset jotka kuuluvat johonkin drinkkiin ja kaikki drinkit
 * johon jotain ainesta voidaan käyttää.
 *
 * @author martin vidjeskog
 */
class DrinkinAinesosat extends BaseModel {

    // konstruktori
    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    // hae kaikki ainesosat jotka kuuluvat drinkkiin (id) tietokannasta
    public static function findIngredients($id) {
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
    
    // hae kaikki drinkit johon tarvitaan jotain ainesosaa
    public static function findDrinks($id) {
        $query = DB::connection()->prepare('SELECT drinkki.* FROM drinkki, ainesosa, drinkinainesosat WHERE drinkki.id = :id  AND drinkinainesosat.drinkki = drinkki.id AND drinkinainesosat.ainesosa = ainesosa.id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();

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
            'drinkkityyppi' => Drinkkityyppi::find($row['drinkkityyppi'])->nimi
            ));
        }

        return $drinkit;
        
    }

}
