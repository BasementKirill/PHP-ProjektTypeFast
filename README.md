# PHP Tippgeschwindigkeitstest mit Benutzer-Login und Währungssystem

Eine webbasierte Tippgeschwindigkeitstest-Anwendung, entwickelt mit PHP, MySQL und JavaScript. Benutzer können sich registrieren, anmelden, ihre Tippgeschwindigkeit testen, Ergebnisse speichern und in einer Bestenliste gegeneinander antreten. Zusätzlich gibt es ein Währungssystem, mit dem Nutzer Features freischalten und upgraden können, z.B. den 1-gegen-1-Modus gegen einen eigenen vorherigen Versuch.

---

## Features

- Benutzerregistrierung und Login mit sicherer Passwort-Hashing
- Echtzeit-Berechnung der Tippgeschwindigkeit (WPM) und Genauigkeit im Frontend
- Speicherung der Ergebnisse (WPM, Genauigkeit) pro Benutzer in MySQL
- Bestenliste mit Benutzer-Rankings und Top-Ergebnissen
- Währungssystem: Für jedes abgeschlossene Tipp-Test erhält man eine virtuelle Währung
- Upgrade-Features, z.B. 1v1-Modus gegen eigenen vorherigen Versuch, der mehr Währung bringt
- Minimalistisches, ablenkungsfreies Benutzerinterface

---

## Technologie-Stack

- **Backend:** PHP 7+ mit PDO für Datenbankzugriff
- **Frontend:** HTML5, CSS3, JavaScript (für Testlogik und AJAX-Kommunikation)
- **Datenbank:** MySQL oder MariaDB
- **Session-Management:** PHP-Sessions für Benutzerauthentifizierung

---

## Erste Schritte

### Voraussetzungen

- PHP 7.0 oder höher
- MySQL oder kompatibler Datenbankserver
- Webserver (Apache, Nginx, o.ä.)

### Installation

1. Repository klonen:

   ```bash
   git clone https://github.com/dein-benutzername/php-typing-speed-test.git
   cd php-typing-speed-test
