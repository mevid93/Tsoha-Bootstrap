<?php

/*
 * Kontrolleri, joka hoitaa etusivun käsittelyn.
 */

  class EtusivuController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  View::make('muut/etusivu.html');
    }

  }