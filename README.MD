# 🧾 Checkin App – Arbeitsplatzbuchung & Mitarbeiter-Check-in

Ein leichtgewichtiges, modernes Tool zum Check-in im Büro + Buchung von Arbeitsplätzen – mit **Plain PHP 8.3**, **Vue 3**, **JWT Auth**, Docker & sauberer Architektur (SOLID, KISS, DRY).

---

## 🚀 Features

✅ Check-in / Check-out für Mitarbeiter  
✅ Buchung von Arbeitsplätzen mit Zeitverwaltung  
✅ Rollenbasierte Authentifizierung (Admin/User)  
✅ Admin: Alle Buchungen einsehen  
✅ JWT Login  
✅ Vue 3 Frontend (Composition API)  
✅ REST API in modernem Plain PHP 8.3 (keine Framework-Abhängigkeit)

---

## 🧱 Tech Stack

| Bereich        | Technologie                     |
|---------------|----------------------------------|
| Frontend      | Vue 3 + Vite                     |
| Backend       | Plain PHP 8.3 (SOLID Architektur)|
| Auth          | JWT (via `firebase/php-jwt`)     |
| Datenbank     | MySQL 8                          |
| Container     | Docker + Docker Compose          |

---

## 🛠️ Setup (lokal)

### 1. 🔁 Projekt klonen

```bash
git clone https://github.com/mariomogge/checkin-app.git
cd checkin-app
```

### 2, ⚙️ Docker starten

```bash
docker compose up --build
```
- PHP/Apache läuft unter: http://localhost:8080

- MySQL auf Port 3307

- Vue-Devserver auf: http://localhost:5173

### 3. 📦 Frontend installieren & starten
```bash
cd frontend
npm install
npm run dev
```

### 4. 🧪 Testdaten / Init-SQL
Die Datenbank wird automatisch beim Start mit der Datei docker/init.sql befüllt.

- Admin-Login:
    - Benutzer: admin
    - Passwort: secret123

- Testnutzer:
    - mario (Passwort: secret123)

### API-Endpunkte
Methode|Route|Beschreibung
|---|---|---|
POST|/login|	Login + Token
POST|/check-in|	Check-in für User
POST|/check-out|	Check-out für User
POST|/book|	Arbeitsplatz buchen
GET|/desks|	Freie Plätze
GET|/user/:id|	Nutzerinfo (mit CheckIn)
GET|/bookings/active/:id|	Aktive Buchung
GET|/admin/bookings|	(Admin) alle Buchungen

### Tests
```bash
composer test
```
