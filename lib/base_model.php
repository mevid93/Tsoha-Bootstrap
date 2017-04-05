<?php

class BaseModel {

    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null) {
        // Käydään assosiaatiolistan avaimet läpi
        foreach ($attributes as $attribute => $value) {
            // Jos avaimen niminen attribuutti on olemassa...
            if (property_exists($this, $attribute)) {
                // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
                $this->{$attribute} = $value;
            }
        }
    }

    /*
     * Metodi, joka käy läpi kaikki validointimetodit.
     */
    public function virheet() {
        // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
        $errors = array();

        foreach ($this->validators as $validator) {
            // Kutsu validointimetodia tässä ja lisää sen palauttamat virheet errors-taulukkoon
            $validator_errors = $this->{$validator}();
            $errors = array_merge($errors, $validator_errors);
        }

        return $errors;
    }
    
    /*
     * Validointimetodi, joka tarkastaa, että annettu teksti ei ole tyhjä.
     */
    public function validoi_string_epätyhjä($string, $errors, $errorString){
        if ($string == '' || $string == null) {
            $errors[] = $errorString;
        }
        return $errors;
    }
    
    /*
     * Validointimetodi, joka tarkastaa, että annettu teksti on oikean pituinen.
     */
    public function validoi_string_pituus($string, $errors, $ala, $yla, $errorLyhyt, $errorPitka){
        if (strlen($string) < $ala) {
            $errors[] = $errorLyhyt;
        }
        if (strlen($string) > $yla) {
            $errors[] = $errorPitka;
        }
        return $errors;
    }
            
}
