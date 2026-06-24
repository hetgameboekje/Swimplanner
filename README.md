# Instructeursportaal — Reddingsbrigade (zwemmend redden)

Front-end demonstratie, **zonder functionaliteit** (geen echte database, geen
echte login). Doel: de architectuur en pagina-structuur laten zien voordat
er functionaliteit gebouwd wordt.

## Architectuur

Drie lagen, met de Dependency Inversion-richting van buiten naar binnen:

```
Presentation (MVC: Controllers + Views)
        ↓ gebruikt
BLL (Services + Interfaces + domeinmodellen)
        ↑ wordt geïmplementeerd door
DAL (vandaag: Fake in-memory repositories — straks: PDO-repositories)
```

- `app/Presentation` — Controllers en Views (Bootstrap 5 + jQuery). Kent alleen de BLL-services.
- `app/BLL` — domeinmodellen (`Models`), interfaces (`Interfaces`) en businesslogica (`Services`). Kent de DAL **niet** rechtstreeks, alleen via interfaces (SOLID — Dependency Inversion).
- `app/DAL/Fake` — huidige implementatie van de interfaces, met statische demo-data. Wordt later 1-op-1 vervangen door PDO-repositories in `app/DAL` zonder dat BLL of Presentation wijzigen.
- `app/Core` — Router, basis Controller, View-renderer, en een minimale `Container` die interfaces aan implementaties koppelt (`config/bindings.php`).

## Draaien in Laragon

1. Zet dit project in `C:\laragon\www\instructeursportaal` (of maak een symlink).
2. Stel de **document root** van de vhost in op de map `public/`.
3. Open `http://instructeursportaal.test/` (of het gegenereerde Laragon-domein).
4. Optioneel: draai `composer install` — dit overschrijft `vendor/autoload.php`
   (nu een minimale handmatige PSR-4 autoloader) met de echte Composer-autoloader.
   Er zijn geen externe package-dependencies nodig.

## Wat dit nog niet doet

- Geen echte authenticatie/sessies (loginformulier is decoratief)
- Geen database/PDO — alle data komt uit `app/DAL/Fake/FakeData.php`
- Geen opslaan/wijzigen — formulieren zijn read-only demo's
- Geen logging (AUTHID/audit_logs) — volgt zodra de echte DAL er is
