# Tietokantasovelluksen esittelysivu

Yleisiä linkkejä:

* [Linkki sovellukseen](http://mevidjes.users.cs.helsinki.fi/tsoha/)
* [Linkki dokumentaatioon](https://github.com/mevid93/Tsoha-Bootstrap/blob/master/doc/dokumentaatio.pdf)

## Työn aihe

Tässä materiaalissa toteutan pienen sovelluksen, joka ratkaisee ikuisen ongelman erilaisten
drinkkien valmistukseen liittyvien ohjeiden muistamisesta. Maailmassa on paljon erilaisia drinkkejä,
eikä niiden kaikkien muistaminen ole mitenkään mahdollista. Tässä drinkkisovellukseni astuu
kuvaan. Tämä sovellus mullistaa drinkkien valmistamisen ja se on merkittävin edistysaskel
ihmiskunnan historiassa sitten… No joo, ei ehkä liioitella. Todellisuudessa sovelluksesta voisi
kuitenkin olla hyötyä esimerkiksi aloittelevalle baarimikolle, tai miksei myös aiheesta
kiinnostuneelle tavalliselle yksityishenkilölle.
<br />
<br />
Sovellus on siis eräänlainen www-sivulla toimiva drinkkihakulomake. Sen tarkoituksena on auttaa
muistamaan ja pitämään kirjaa erilaisista drinkkiresepteistä. Drinkkireseptit kuvaavat cocktaileja ja
muita juomasekoituksia. Sovelluksessa käyttäjät voivat hakea reseptejä juoman nimeen liittyvällä
hakusanalla tai vaihtoehtoiesti jollakin tietyllä ainesosalla. Resepteistä voi pyytää myös listan
aakkosjärjestyksen, ainesosan tai juomalajin mukaan.
<br />
<br />
Tavallinen käyttäjä voi hakea drinkkireseptejä kannasta ja myös ehdottaa uusien lisäämistä
drinkkikantaan. Järjestelmän ylläpitäjä on enemmän oikeuksia. Hän voi lisätä järjestelmään
reseptejä joko kokonaan itse, tai hyväksyä käyttäjien ehdotuksia. Lisääminen tapahtuu syöttämällä
tietoja lomakkeeseen. Myös tavalliselle käyttäjälle voidaan ylläpitäjän toimesta antaa enemmän
käyttöoikeuksia.
<br />
<br />
Työ toteutetaan laitoksen users-palvelimella, PHP:llä, käyttäen Postgre SQL-tietokantapalvelinta.
Koska sovellus käyttää PHP:tä, niin sovelluksen alustajärjestelmältä edellytetään myös tukea tälle
ohjelmointikielelle. Selaimen puolestaan olisi syytä myös tukea javascriptiä, sillä sitä hyödynnetään
sovelluksessa jonkin verran (Bootstrap). Sovellus toimii vain yhdellä tietokannalla. 

<br />

## Linkit staattisiin HTML sivuihin

* [Linkki sovelluksen etusivulle](http://mevidjes.users.cs.helsinki.fi/tsoha/)
* [Linkki rekisteröitymissivulle](http://mevidjes.users.cs.helsinki.fi/tsoha/rekisteroidy)
* [Linkki kirjautumissivulle](http://mevidjes.users.cs.helsinki.fi/tsoha/kirjaudu)
* [Linkki tilin muokkaussivulle](http://mevidjes.users.cs.helsinki.fi/tsoha/asetukset)
* [Linkki käyttäjätilien listaussivulle](http://mevidjes.users.cs.helsinki.fi/tsoha/kayttajat)
* [Linkki reseptien listaussivulle](http://mevidjes.users.cs.helsinki.fi/tsoha/drinkki)
* [Linkki ehdotettujen reseptien listaussivulle](http://mevidjes.users.cs.helsinki.fi/tsoha/ehdotukset)
* [Linkki reseptin esittelysivulle](http://mevidjes.users.cs.helsinki.fi/tsoha/drinkki/1)
* [Linkki reseptin muokkaussivulle](http://mevidjes.users.cs.helsinki.fi/tsoha/drinkki/1/muokkaa)
* [Linkki reseptin ehdotussivulle](http://mevidjes.users.cs.helsinki.fi/tsoha/ehdota)

<br />

## Linkki tietokantayhteys sivulle

* [Linkki tietokantayhteys sivulle](http://mevidjes.users.cs.helsinki.fi/tsoha/tietokantayhteys)

<br />

## Valmiit tunnukset joilla sovellukseen voi kirjautua

Tavallinen kirjautunut käyttäjä:
- käyttäjätunnus: PohjoisenKarhunAlla
- salasana: KarhuTäyttäOlutta

Ylläpitäjä:
- käyttäjätunnus: TeekkariElämää
- salasana: Salasana1234

<br />
<br />
<br />

<a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/"><img alt="Creative Commons -lisenssi" style="border-width:0" src="https://i.creativecommons.org/l/by-nc/4.0/88x31.png" /></a><br />Tämä teos on lisensoitu <a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/">Creative Commons Nimeä-EiKaupallinen 4.0 Kansainvälinen -lisenssillä</a>.
