<?php

/*
 * Kontrolleri, joka hoitaa käyttäjien käsittelyn. Tämä on 
 * vain ylläpitäjille tarkoitettu.
 */

class KayttajaController extends BaseController {

    // metodi, joka hoitaa rekisteröityneiden käyttäjien listaamisen
    public static function index() {
        $kayttajat = Kayttaja::kaikkiEiYllapitajat();
        View::make('muut/kayttajat.html', array('kayttajat' => $kayttajat));
    }

}
