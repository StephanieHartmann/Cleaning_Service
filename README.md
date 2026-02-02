# Projekt Service Auftrag (Migration React + PHP)

Dieses Projekt ist eine Webanwendung zur Verwaltung von Serviceaufträgen. Es wurde von einer reinen PHP-Legacy-Version auf eine moderne Architektur migriert, die **Frontend** und **Backend** trennt.

**Wichtiger Hinweis zur Infrastruktur:**
*   **Datenbank**: Verwaltet via **Supabase** (PostgreSQL in der Cloud). Eine lokale Datenbank-Installation ist nicht erforderlich.
*   **XAMPP**: Wird in diesem Projekt **ausschiesslich** als PHP-Interpreter verwendet, um den Backend-Code auszuführen. Der Apache-Server oder MySQL von XAMPP werden nicht benötigt.

## 🚀 Übersicht

Die Anwendung ermöglicht es einem Sanitätsunternehmen, den gesamten Lebenszyklus eines Auftrags zu verwalten:
1.  **Auftrag erfassen**: Neue Aufträge für Kunden registrieren.
2.  **Disponieren**: Aufträge an Mitarbeiter verteilen und Termine festlegen (mit Konfliktprüfung).
3.  **Ausführen**: Mitarbeiter erfassen Arbeitsstunden und Berichte.
4.  **Verrechnen**: Der Auftrag wird abgeschlossen und verrechnet.
5.  **Drucken**: Generierung eines A4-Dokuments zur Unterschrift.

## 🏗️ Architektur

Das Projekt verwendet eine **Headless-Architektur** (Frontend getrennt vom Backend):

*   **Frontend (Ordner `frontend/`)**:
    *   Entwickelt mit **Next.js 14** (App Router) und **Tailwind CSS**.
    *   Verantwortlich für die visuelle Darstellung (Seiten, Formulare, Buttons).
    *   Verwaltet die Datenvalidierung im Browser für eine schnelle UX.
    *   Kommuniziert mit dem Server über API-Aufrufe (JSON).

*   **Backend (Ordner `backend/`)**:
    *   Entwickelt mit reinem **PHP** (API).
    *   Verantwortlich für die **Geschäftslogik** (Regeln, Sicherheit, Terminkonflikte).
    *   Verbindet sich direkt mit der **Supabase Cloud-Datenbank**.
    *   Erreichbar unter `http://localhost:8000`.

## 📂 Ordnerstruktur

```
root/
├── backend/            # Server-Code
│   ├── public/         # Einstiegspunkt (index.php)
│   │   └── api/        # API-Endpunkte (orders.php, etc.)
│   └── src/            # Kernlogik (db.php - Supabase config)
├── frontend/           # Client-Code (Next.js)
│   ├── src/app/        # Seiten (Pages)
│   └── public/         # Statische Assets
└── README.md           # Diese Datei
```

## 🛠️ Anleitung zum Starten

Da Frontend und Backend getrennt sind, müssen **zwei Terminals** geöffnet werden.

### 1. Backend starten (PHP)
Öffnen Sie ein Terminal im Hauptordner und führen Sie aus:
```bash
cd backend
php -S localhost:8000 -t public
```
*Hinweis: Der Befehl `php` muss verfügbar sein (z.B. via XAMPP Path).*

### 2. Frontend starten (Next.js)
Öffnen Sie ein **zweites** Terminal im Hauptordner:
```bash
cd frontend
npm run dev
```
Das Projekt läuft unter **`http://localhost:3000`**.

## ✅ Hauptfunktionen

*   **Intelligente Validierung**: Das Formular prüft Schweizer Telefonnummern-Formate (+41) und warnt bei fehlenden Feldern oder ungültiger Grösse (m²).
*   **Optimierter Druck**: Beim Klick auf "Print" passt sich die Seite für A4-Papier an.
*   **Interaktive Berichte**: Beim Abschluss eines Auftrags wird nach Arbeitsstunden und Notizen gefragt.
*   **Clean Design**: Angepasste Farben (`#FAF8F5`, `#4D403A`) und responsive Benutzeroberfläche.

---
**GitHub**: [StephanieHartmann]https://github.com/StephanieHartmann/Cleaning_Service
