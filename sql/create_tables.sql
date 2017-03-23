-- Lisää CREATE TABLE lauseet tähän tiedostoon

-- Luodaan tietokantataulu, joka kuvaa käyttäjää
CREATE TABLE Kayttaja(
    id SERIAL PRIMARY KEY,
    etunimi varchar(50) NOT NULL,
    sukunimi varchar(50) NOT NULL,
    sahkoposti varchar(120) NOT NULL,
    kayttajatunnus varchar (20) NOT NULL,
    salasana varchar(50) NOT NULL,
    yllapitaja boolean DEFAULT FALSE
);




-- Luodaan tietokantataulu, joka kuvaa drinkkityyppiä
CREATE TABLE Drinkkityyppi(
    id SERIAL PRIMARY KEY,
    nimi varchar(50) NOT NULL,
    kuvaus varchar(400)
);




-- Luodaan tietokantataulu, joka kuvaa drinkkiä
CREATE TABLE Drinkki(
    id SERIAL PRIMARY KEY,
    ensisijainenNimi varchar(50) NOT NULL,
    lasi varchar(20) NOT NULL,
    lampotila varchar(20) NOT NULL,
    lisayspaiva DATE NOT NULL,
    lisaaja varchar(50) NOT NULL,
    drinkkityyppi INTEGER REFERENCES Drinkkityyppi(id)
);



-- Luodaan tietokantataulu, joka kuvaa muuta nimeä, joka drinkillä voi olla
CREATE TABLE MuuNimi(
    id SERIAL PRIMARY KEY,
    nimi varchar(50) NOT NULL,
    drinkki INTEGER REFERENCES Drinkki(id)
);




-- Luodaan tietokantataulu, joka kuvaa Ainesosaa
CREATE TABLE Ainesosa(
    id SERIAL PRIMARY KEY,
    nimi varchar(50) NOT NULL,
    kuvaus varchar(400)
);



-- Luodaan liitos tietokantataulu drinkkien ja ainesosien väliin.
CREATE TABLE DrinkinAinesosat(
    drinkki INTEGER REFERENCES Drinkki(id),
    ainesosa INTEGER REFERENCES Ainesosa(id)
);
