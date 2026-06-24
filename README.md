# Instructeursportaal — Reddingsbrigade (zwemmend redden)

**Alle kernmodules zijn volledig functioneel** tegen de echte MySQL-database
(Login, Groepen, Leden, Lessen, Lesplanningen, Materiaal), met audit-logging
(AUTHID) op elke wijziging. Er is geen Fake/demo-DAL meer over.

## Inloggen

Demo-accounts (wachtwoord voor alle drie: `demo123`):
- timo@reddingsbrigade.nl (beheerder)
- anna@reddingsbrigade.nl (instructeur)
- sven@reddingsbrigade.nl (instructeur)

Alle pagina's behalve `/login` vereisen een sessie — zonder in te loggen
stuurt `public/index.php` je automatisch naar `/login`. Beheerders zien
alles; instructeurs zien alleen groepen die ze zelf hebben aangemaakt of
waar ze aan gekoppeld zijn (en de bijbehorende dashboard-waarschuwingen).

## Architectuur

Drie lagen, met de Dependency Inversion-richting van buiten naar binnen:

```
Presentation (MVC: Controllers + Views)
        ↓ gebruikt
BLL (Services + Interfaces + domeinmodellen)
        ↑ wordt geïmplementeerd door
DAL (app/DAL/Pdo — echte database, geen Fake-DAL meer)
```

- `app/Presentation` — Controllers en Views (Bootstrap 5 + jQuery). Kent alleen de BLL-services.
- `app/BLL` — domeinmodellen (`Models`), interfaces (`Interfaces`) en businesslogica (`Services`, incl. validatie). Kent de DAL **niet** rechtstreeks, alleen via interfaces (SOLID — Dependency Inversion).
- `app/DAL/Pdo` — de database-implementatie van alle interfaces. Voert ook de audit-logging uit ná elke create/update/delete. Repositories met relaties (Les, Lesplanning) bouwen hun resultaat op via compositie van andere repositories (bv. `PdoLesRepository` vraagt Groep/Gebruiker op via hun eigen interface) i.p.v. dezelfde joins overal te dupliceren.
- `app/Core` — Router (incl. `{id}`-parameters), basis Controller, View-renderer, `Container` die interfaces aan implementaties koppelt (`config/bindings.php`), `AuditLogger`, `Flash`-meldingen, en `HuidigeGebruiker` (sessie-gebaseerde AUTHID + rol-check).

**Let op — bekende valkuil met de minimale autoloader:** elke klasse/enum
moet in zijn eigen bestand staan (`Klasse.php` → klasse `Klasse`). De
meegeleverde `vendor/autoload.php` is een simpele PSR-4-mapper zonder
classmap; als je twee symbolen (bv. een enum + een klasse) in één bestand
zet, faalt het zodra het tweede symbool als eerste wordt aangeroepen. Met
`composer install` (echte Composer-autoloader) is dit geen probleem meer.

## Draaien in Laragon

1. Zet dit project in `C:\laragon\www\swimplanner` (of via Laragon's www-pad).
2. Stel de **document root** van de vhost in op de map `public/` (Laragon
   detecteert dit niet altijd automatisch bij een kale PHP-structuur — check
   `C:\laragon\etc\apache2\sites-enabled\auto.<naam>.test.conf`).
3. Zorg voor een `public/.htaccess` die alles naar `index.php` rewrit
   (al aanwezig) — zonder deze regel geeft Apache 404 op alles behalve `/`.
4. Open `http://swimplanner.test/`.
5. Optioneel: draai `composer install` — dit overschrijft `vendor/autoload.php`
   met de echte Composer-autoloader. Geen externe package-dependencies nodig.

## Database opbouwen vanuit XML

De database (`swimplanner`, collation `utf8mb4_0900_ai_ci`) wordt **niet**
met handgeschreven SQL aangemaakt, maar met XML-bestanden per tabel in
`database/schema/tables/`. Een PHP-script (`app/DAL/Schema/SchemaParser.php`
+ `SchemaBuilder.php`) leest die XML uit en bouwt de `CREATE TABLE`-statements.

```
php database/build.php            # maakt database + tabellen aan als ze nog niet bestaan
php database/build.php --fresh     # DROPt eerst alle tabellen (alleen lokale dev, geen productiedata!)
php database/seed.php             # afdeling + 3 demo-gebruikers (login-accounts)
```

Connectiegegevens staan in `config/database.php` (host `127.0.0.1`, user
`root`, geen wachtwoord — past bij een standaard Laragon-MySQL).

**Een kolom of tabel toevoegen, zonder SQL te schrijven:**
- Kolom toevoegen aan een bestaande tabel → open het bijbehorende
  `database/schema/tables/xx_tabelnaam.xml`, voeg een `<column>`-regel toe.
  Run daarna `php database/build.php --fresh` (tabel bestaat al, dus zonder
  `--fresh` gebeurt er niets — er is nog geen ALTER-ondersteuning).
- Nieuwe tabel toevoegen → nieuw XML-bestand in dezelfde map, met een
  nummer-prefix dat ná alle tabellen komt waarnaar verwezen wordt (foreign
  keys worden in bestandsvolgorde aangemaakt).

Kolom-attributen die de parser/builder ondersteunt: `type`, `length`,
`values` (voor `ENUM`, komma-gescheiden), `nullable`, `default`,
`autoIncrement`, `primaryKey`, `unique`, `onUpdateCurrentTimestamp`,
`references` (`tabel.kolom`), `onDelete`, `onUpdate`. Voor samengestelde
unieke combinaties: `<unique columns="kolom_a,kolom_b" />` binnen `<table>`.

## Wat al écht werkt

- **Login/logout** — sessie-gebaseerd, wachtwoord-hash-verificatie, audit-log voor login/logout
- **Groepen** — aanmaken, bewerken, deactiveren/activeren, verwijderen, instructeur(s) koppelen, start-/einddatum (voor gebruik over meerdere seizoenen). Instructeurs zien alleen hun eigen groepen; beheerders zien alles.
- **Leden** — per groep (klik op een groep) leden toevoegen (voornaam, achternaam, jaartal, contactgegevens) en uitschrijven, met behoud van historie (`groep_leden.datum_uitgeschreven`)
- **Lessen** — aanmaken, bewerken, verwijderen; **meerdere groepen én meerdere instructeurs** per les, optionele begin-/eindtijd en locatie, elke datum toegestaan. **Bulk inplannen** (`/lessen/bulk`) tot 52 lessen met een vast interval in één keer.
- **Lesplanningen** — volledige CRUD, incl. lesonderdelen (dynamisch aantal, standaard Inleiding/Kern 1/Kern 2/Afsluiting) met per onderdeel een materiaal-selectie. Klik op **"Ontbreekt"** bij een les → opent een lesplanning met groep/datum/tijden/locatie al ingevuld en gekoppeld aan die les (`lesplanningen.les_id`); na opslaan verandert de badge naar "Aanwezig" en verdwijnt de dashboard-waarschuwing.
- **Materiaal** — volledige CRUD (aanmaken/bewerken/deactiveren/verwijderen), centraal beheerd
- **Printen van lesplanningen** (`/lesplanningen/{id}/print`) — opmaak volgt de structuur van het officiële lesvoorbereidingsformulier (LVF): kopblok met naam/datum/locatie/tijd/groepsgrootte, beginsituatie/doelstelling, en een onderdelen-tabel met dezelfde kolomkoppen (Tijd, Doel, Activiteit, Organisatie en Materialen, Didactische aanwijzingen). Opent in een nieuw tabblad, printknop roept gewoon `window.print()` aan.
- Dashboard toont alleen de eerste 2 waarschuwingen + een "+N andere meldingen"-uitklapper
- Elke create/update/delete/login/logout wordt gelogd in `audit_logs` met AUTHID, actie, entiteit, record-id en een samenvatting

## Wat dit nog niet doet

- Geen ALTER-ondersteuning in de schema-builder — kolom toevoegen aan een
  bestaande tabel vereist `--fresh` (dropt en herbouwt alles; geen probleem
  zolang er geen productiedata in staat)
- Geen CSRF-bescherming en geen wachtwoord-reset (intranet-demo-niveau)
- Geen rollen-gebaseerde rechten op acties (beheerder/instructeur hebben nu
  evenveel rechten binnen wat ze zien — `HuidigeGebruiker::isBeheerder()`
  bestaat al en wordt gebruikt voor zichtbaarheid, niet voor actie-rechten)
- Aanwezigheidsregistratie en Jaarplanning staan nog op de planning (nog niet gebouwd)
