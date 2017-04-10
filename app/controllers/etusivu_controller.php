<?php

/*
 * Kontrolleri, joka hoitaa etusivun käsittelyn.
 */

class EtusivuController extends BaseController {
    /*
     * Metodi, joka renderöi näkyville sovelluksen etusivun.
     */

    public static function index() {
        View::make('muut/etusivu.html');
    }

}
