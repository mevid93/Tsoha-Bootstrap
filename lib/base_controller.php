<?php

  class BaseController{

    public static function get_user_logged_in(){
      // Toteuta kirjautuneen käyttäjän haku tähän
        
        // jos käyttäjä on kirjautunut sisään
        if(isset($_SESSION['user'])){
            $user_id = $_SESSION['user'];
            $user = Kayttaja::findById($user_id);
            return $user;
        }
        
      // jos käyttäjä ei ole kirjautunut sisään
      return null;
    }
    
    public static function get_admin_logged_in(){
      // Toteuta kirjautuneen ylläpitäjän haku tähän
        
        // jos ylläpitäjä on kirjautunut sisään
        if(isset($_SESSION['user'])){
            $user_id = $_SESSION['user'];
            $user = Kayttaja::findById($user_id);
            if($user->yllapitaja){
                return $user;
            }
            return null;
        }
        
      // jos käyttäjä ei ole kirjautunut sisään
      return null;
    }

    public static function check_logged_in(){
      // Toteuta kirjautumisen tarkistus tähän.
      // Jos käyttäjä ei ole kirjautunut sisään, ohjaa hänet toiselle sivulle (esim. kirjautumissivulle).
    }

  }
