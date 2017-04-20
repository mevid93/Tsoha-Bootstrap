<?php

/**
 * Kontrolleri, joka hoitaa etusivun käsittelyn.
 */
class EtusivuController extends BaseController {

    /**
     * Metodi, joka renderöi näkyville sovelluksen etusivun.
     */
    public static function etusivuNakyma() {
        View::make('muut/etusivu.html');
    }

}
