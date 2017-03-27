<?php

/*
 * Kontrolleri, joka hoitaa drinkkien käsittelyn.
 */
class DrinkkiController extends BaseController {
    
    // metodi, joka hoitaa drinkkien listaamisen
    public static function index(){
        $drinkit = Drinkki::etsiKaikkiHyvaksytyt();
        View::make('drinkki/drinkki_lista.html', array('drinkit' => $drinkit));
    }
    
    // metodi, joka hoitaa yksittäisen drinkin näkymän näyttämisen
    public static function naytaResepti($id){
        $drinkki = Drinkki::etsiPerusteellaID($id);
        // jos kirjoittaa suoraan urlin  niin voi yrittää hakea reseptiä jota 
        // ei oikeasti ole olemassa. Tässä tilanteessa ohjataan etusivulle.
        if($drinkki == null){
            Redirect::to('/', array('message' => "Kyseistä drinkkiä ei ole olemassa!"));
        }
        View::make('drinkki/resepti.html', array('drinkki' => $drinkki));
    }
    
}

