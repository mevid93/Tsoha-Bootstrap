-- Lisää DROP TABLE lauseet tähän tiedostoon


-- Dropataan liitostaulu 
DROP TABLE IF EXISTS DrinkinAinesosat CASCADE;

-- Dropataan muuta nimeä kuvaava tietokantataulu
DROP TABLE IF EXISTS MuuNimi CASCADE;

-- Dropataan käyttäjää kuvaava tietokantataulu
DROP TABLE IF EXISTS Kayttaja CASCADE;

-- Dropataan drinkkiä kuvaava tietokantataulu
DROP TABLE IF EXISTS Drinkki CASCADE;

-- Dropataan drinkkityyppiä kuvaava tietokantataulu
DROP TABLE IF EXISTS Drinkkityyppi CASCADE;

-- Dropataan ainesosaa kuvaava tietokantataulu
DROP TABLE IF EXISTS Ainesosa CASCADE;