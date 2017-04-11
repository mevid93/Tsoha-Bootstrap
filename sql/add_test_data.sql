-- Lisää INSERT INTO lauseet tähän tiedostoon


-- Lisätään ensin kaksi käyttäjää, joista toinen on ylläpitäjä
INSERT INTO Kayttaja (etunimi, sukunimi, sahkoposti, kayttajatunnus, salasana, yllapitaja)
VALUES ('Teemu', 'Teekkari', 'teemu.teekkari@aalto.fi', 'TeekkariElämää', 'Salasana1234', 'true');

INSERT INTO Kayttaja (etunimi, sukunimi, sahkoposti, kayttajatunnus, salasana)
VALUES ('Matti', 'Meikäläinen', 'matti.meikäläinen@gmail.com', 'PohjoisenKarhunAlla', 'KarhuTäyttäOlutta');



-- Lisätään kaksi drinkkityyppiä
INSERT INTO Drinkkityyppi(nimi, kuvaus)
VALUES ('Cocktail', 'Alkoholipitoinen juomasekoitus. Usein koristeellisia ja niiden maut ovat hyvin vaihtelevia.');

INSERT INTO Drinkkityyppi(nimi, kuvaus)
VALUES ('Shotti', 'Pieni annos väkevää alkoholijuomaa. Tarjoillaan yleensä ns. shottilasista.');



-- Lisätään kaksi drinkkiä
INSERT INTO Drinkki(ensisijainenNimi, lasi, kuvaus, lampotila, lisayspaiva, lisaaja, hyvaksytty, drinkkityyppi)
VALUES ('Valkovenäläinen', 'Grogilasi', 'Kuvaus, jota en jaksa kirjoittaa','Viileä', NOW(), 'TeekkariElämää', 'true', 1);

INSERT INTO Drinkki(ensisijainenNimi, lasi, kuvaus, lampotila, lisayspaiva, lisaaja, drinkkityyppi)
VALUES ('Bloody Mary', 'Highball-lasi', 'Kuvaus, jota en jaksa kirjoittaa','Viileä', NOW(), 'TeekkariElämää', 1);



-- Lisätään kaksi muuta nimeä drinkeille
INSERT INTO MuuNimi(nimi, drinkki)
VALUES ('Tomaattisose', 2);

INSERT INTO MuuNimi(nimi, drinkki)
VALUES ('Terästetty maitokahvi', 1);



-- Lisätään muutama ainesosa
INSERT INTO Ainesosa(nimi, kuvaus)
VALUES ('Tomaattimehu', 'Tomaatista valmistettu mehumainen litku.');

INSERT INTO Ainesosa(nimi, kuvaus)
VALUES ('Kerma', 'Kerma on perus elintarvike, jota saa kaupan kylmähyllyiltä.');


-- Lisätään muutama ainesosa liitostauluun
INSERT INTO DrinkinAinesosat(drinkki, ainesosa, maara)
VALUES (1, 2, 1);

INSERT INTO DrinkinAinesosat(drinkki, ainesosa, maara)
VALUES (2, 1, 2);
