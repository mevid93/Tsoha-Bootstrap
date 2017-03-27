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
    public $id, $nimi, $drinkki;

    // konstruktori
    public function __construct($attributes) {
        parent::__construct($attributes);
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
    public static function find($id){
        $query = DB::connection()->prepare('SELECT * FROM muunimi WHERE id = :id LIMIT 1');
        $query->execute(array('id'=> $id));
        $row = $query->fetch();
        
        if($row){
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
    public static function findByDrinkId($id){
        $query = DB::connection()->prepare('SELECT * FROM muunimi WHERE drinkki = :id');
        $query->execute(array('id'=> $id));
        $rows = $query->fetchAll();
        $nimet = array();
        
        foreach ($rows as $row) {
            $nimet[] = new MuuNimi(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'drinkki' => $row['drinkki']
            ));
        }
        
        if(empty($nimet)){
            return null;
        }
        
        return $nimet;
    }

}
