# Instructeursportaal — Reddingsbrigade (zwemmend redden)

**Login, Groepen en Lessen zijn volledig functioneel** tegen de echte
MySQL-database, met audit-logging (AUTHID) op elke wijziging. Lesplanningen
en Materiaal zijn nog front-end demo's op statische demo-data.

## Inloggen

Demo-accounts (wachtwoord voor alle drie: `demo123`):
- timo@reddingsbrigade.nl (beheerder)
- anna@reddingsbrigade.nl (instructeur)
- sven@reddingsbrigade.nl (instructeur)

Alle pagina's behalve `/login` vereisen een sessie — zonder in te loggen
stuurt `public/index.php` je automatisch naar `/login`.

## Architectuur

Drie lagen, met de Dependency Inversion-richting van buiten naar binnen:

```
Presentation (MVC: Controllers + Views)
        ↓ gebruikt
BLL (Services + Interfaces + domeinmodellen)
        ↑ wordt geïmplementeerd door
DAL (Pdo* voor Login/Groepen/Lessen — Fake* voor Lesplanningen/Materiaal)
```

- `app/Presentation` — Controllers en Views (Bootstrap 5 + jQuery). Kent alleen de BLL-services.
- `app/BLL` — domeinmodellen (`Models`), interfaces (`Interfaces`) en businesslogica (`Services`, incl. validatie zoals de zondag-check op lessen). Kent de DAL **niet** rechtstreeks, alleen via interfaces (SOLID — Dependency Inversion).
- `app/DAL/Pdo` — echte database-implementatie (Gebruiker, Afdeling, Groep, Les). Voert ook de audit-logging uit ná elke create/update/delete.
- `app/DAL/Fake` — in-memory implementatie voor de modules die nog niet op de database draaien (Lesplanning, Materiaal). Wordt module voor module vervangen door `Pdo*`-klassen, zonder dat BLL of Presentation wijzigen.
- `app/Core` — Router (incl. `{id}`-parameters), basis Controller, View-renderer, `Container` die interfaces aan implementaties koppelt (`config/bindings.php`), `AuditLogger`, `Flash`-meldingen, en `HuidigeGebruiker` (sessie-gebaseerde AUTHID).

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
3. Open `http://swimplanner.test/`.
4. Optioneel: draai `composer install` — dit overschrijft `vendor/autoload.php`
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

- **Login/logout** — sessie-gebaseerd, wachtwoord-hash-verificatie, audit-log
  voor login/logout (`App\BLL\Services\AuthService`, `App\Core\HuidigeGebruiker`)
- **Groepen** — aanmaken, bewerken, deactiveren/activeren, verwijderen,
  instructeur(s) koppelen; FK-conflict bij verwijderen geeft een nette
  foutmelding in plaats van een crash
- **Lessen** — aanmaken, bewerken, verwijderen; een les kan aan **meerdere
  groepen én meerdere instructeurs** gekoppeld worden (many-to-many via
  `les_groepen`/`les_instructeurs`), met optionele begin-/eindtijd voor
  lesblokken met meerdere lessen per dag. Elke datum is toegestaan (geen
  zondag-eis — incidentele lessen voor aspiranten/kader mogen op elke dag).
  Dashboard-waarschuwing ("les zonder lesplanning") draait op echte data.
- **Groepen** hebben een **start- en (optionele) einddatum**, zodat dezelfde
  groepsnaam over meerdere seizoenen/jaren heen apart bijgehouden kan worden
- Elke create/update/delete/login/logout wordt gelogd in `audit_logs` met
  AUTHID, actie, entiteit, record-id en een samenvatting

## Wat dit nog niet doet

- Lesplanningen en Materiaal draaien nog op de Fake-DAL (statische
  demo-data, geen opslaan/wijzigen)
- Geen ALTER-ondersteuning in de schema-builder — kolom toevoegen aan een
  bestaande tabel vereist `--fresh` (dropt en herbouwt alles; geen probleem
  zolang er geen productiedata in staat)
- Geen CSRF-bescherming en geen wachtwoord-reset (intranet-demo-niveau)
- Geen rollen-gebaseerde rechten in de UI (beheerder/instructeur hebben nu
  evenveel rechten — `HuidigeGebruiker::isBeheerder()` bestaat al, wordt
  nog nergens gebruikt om acties te beperken)
