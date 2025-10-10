# PHP-ProejktTypeFast mit Vue.js 

## Projektbeschreibung
Diese webbasierte Tippgeschwindigkeitstest-Anwendung kombiniert ein PHP-Backend mit einem Vue.js-Frontend. Benutzer können sich registrieren, anmelden und ihre Tippgeschwindigkeit testen. Ergebnisse werden gespeichert, und durch das Spielen verdienen Nutzer eine virtuelle Währung, mit der sie Features freischalten und upgraden können.

---

## Features

- **Benutzerregistrierung & Login**
  - Sichere Passwort-Hashing mit PHP (`password_hash` / `password_verify`)
  - Session-basierte Authentifizierung
- **Tippgeschwindigkeitstest**
  - Echtzeit-Berechnung von WPM (Wörter pro Minute) und Fehleranzahl
  - Fortschrittsbalken, Fehleranzeige (freischaltbare Features)
  - Anpassbare UI mit Farb- und Schriftoptionen (Customization)
- **Speicherung**
  - Ergebnisse (WPM, Genauigkeit, Fehler) werden pro Benutzer in MySQL gespeichert
  - Währungssystem: Für jeden abgeschlossenen Test erhalten Benutzer Coins
- **Feature-Freischaltung**
  - Features wie Fehleranzeige, Progress Bar, Customization, 1v1-Modus können mit Coins gekauft und freigeschaltet werden
  - 1v1-Modus: Tipp gegen einen eigenen oder anderen gespeicherten Score
- **Bestenliste**
  - Rankings und Top-Ergebnisse aller Benutzer
- **Minimalistisches und ablenkungsfreies UI**
- **Technologie-Stack**
  - Backend: PHP 7+ mit PDO für sicheren DB-Zugriff
  - Frontend: Vue.js für reaktive, modulare Benutzeroberfläche
  - Datenbank: MySQL oder MariaDB
  - Kommunikation zwischen Frontend und Backend via AJAX / REST-API

---

## Architektur

### Backend (PHP)
- Benutzerverwaltung, Session-Handling
- API-Endpunkte für:
  - User-Login / Registrierung
  - Tipp-Test Ergebnis speichern & laden
  - Coins und Feature-Status verwalten
  - Bestenliste bereitstellen
- Datenbankanbindung mit PDO

### Frontend (Vue.js)
- Interaktives Tippfeld mit Echtzeit-Berechnung von WPM, Fehlern, Fortschritt
- Feature-Management UI (Shop für Freischaltungen)
- Customization UI (Farben, Fonts)
- 1v1-Modus UI zur Auswahl gespeicherter Scores
- AJAX-Kommunikation mit PHP-Backend

---

## Installation

### Voraussetzungen
- PHP 7.0 oder höher
- MySQL oder kompatibler Datenbankserver
- Webserver (Apache, Nginx o.ä.)
- Node.js und npm (für Vue.js Entwicklung)

### Setup Backend
1. Repository klonen:

   ```bash
   git clone https://github.com/BasementKirill/PHP-ProejktTypeFast.git
   cd PHP-ProjektTypeFast
