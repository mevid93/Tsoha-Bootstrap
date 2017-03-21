<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  View::make('suunnitelmat/frontPage.html');
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
        //echo 'Hello World!';
        View::make('helloworld.html');
    }
    
    public static function recipe_list(){
      // Renderöidään reseptilistasivu
        View::make('suunnitelmat/recipe_list.html');
    }
    
    public static function recipe_show(){
      // Renderöidään reseptin esittely sivu
        View::make('suunnitelmat/recipe_show.html');
    }
    
    public static function sign_in(){
      // Renderöidään sisäänkirjautumis sivu
        View::make('suunnitelmat/sign_in.html');
    }
    
    public static function sign_up(){
      // Renderöidään rekisteröitymis sivu
        View::make('suunnitelmat/sign_up.html');
    }
    
    public static function change_settings(){
      // Renderöidään käyttäjän asetusten muutos sivu
        View::make('suunnitelmat/account_settings.html');
    }
    
    public static function recipe_suggest(){
      // Renderöidään reseptin ehdotus sivu
        View::make('suunnitelmat/recipe_suggest.html');
    }
    
    public static function suggest_list(){
      // Renderöidään reseptin ehdotus sivu
        View::make('suunnitelmat/suggest_list.html');
    }
    
    public static function user_list(){
      // Renderöidään reseptin ehdotus sivu
        View::make('suunnitelmat/user_list.html');
    }
    
    public static function recipi_edit(){
      // Renderöidään reseptin ehdotus sivu
        View::make('suunnitelmat/recipe_edit.html');
    }
    
  }
