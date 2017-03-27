<?php

/**
 * Tämä luokka on malli drinkityypille.
 * Se sisältää tietokantaoperaatiot ja muutaman muun 
 * tarvittavan metodin.
 *
 * @author martin vidjeskog
 */
class Drinkkityyppi extends BaseModel {

    // mallin atribuutit
    public $id, $nimi, $kuvaus;

    // konstruktori
    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    // hae kaikki drinkityypit tietokannasta
    public static function all() {
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
    
    // hae drinkkityyppi, jolla tietty id
    public static function find($id){
        $query = DB::connection()->prepare('SELECT * FROM Drinkkityyppi WHERE id = :id LIMIT 1');
        $query->execute(array('id'=> $id));
        $row = $query->fetch();
        
        if($row){
            $tyyppi = new Drinkkityyppi(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus']
            ));
            return $tyyppi;
        }
        
        return null;
    }
    
    // hae drinkkityyppi, jolla tietty nimi
    public static function findByName($nimi){
        $query = DB::connection()->prepare('SELECT * FROM Drinkkityyppi WHERE nimi = :nimi LIMIT 1');
        $query->execute(array('nimi'=> $nimi));
        $row = $query->fetch();
        
        if($row){
            $tyyppi = new Drinkkityyppi(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus']
            ));
            return $tyyppi;
        }
        
        return null;
    }

}
