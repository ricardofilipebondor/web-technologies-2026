# eSC — Chess Club Manager

Aplicatie web pentru un club de sah: membri, antrenori, sali, activitati, competitii, premii, deplasari.

Proiect la **Tehnologii Web** (UAIC).

---

## Arhitectura (3 straturi)

```
/frontend          Pagini HTML + CSS + JS (fetch API)
/backend           API REST (server.php, routes, controllers, services)
/database          Acces date (db.php, models, schema SQLite)
```

**Flux:** Browser → `frontend/*.html` → `fetch('/backend/server.php/...')` → controllers → models → SQLite

Nu mai exista SSR — datele sunt incarcate exclusiv prin API.

---

## Instalare

```cmd
cd web-technologies-2026
C:\xampp\php\php.exe install.php
C:\xampp\php\php.exe -S localhost:8000 router.php
```

http://localhost:8000/frontend/index.html

| User  | Parola     | Rol           |
|-------|------------|---------------|
| admin | admin pass | administrator |

---

## API

Entry point: `backend/server.php`

```
POST /backend/server.php/auth/login
GET  /backend/server.php/auth/me
GET  /backend/server.php/dashboard
GET  /backend/server.php/members
GET  /backend/server.php/members/{id}
POST /backend/server.php/members
...
```

Autentificare: sesiune PHP (cookie), `credentials: 'include'` in fetch.

---

## Structura

```
frontend/
  index.html          login / inregistrare
  app.html            aplicatie principala (hash router)
  css/style.css
  js/api.js           client fetch
  js/pages/*.js       pagini dinamice

backend/
  server.php          entry point API
  routes/             definitii rute
  controllers/        handlers JSON
  services/           logica business
  exports/            CSV, JSON, XML, PDF
  plugins/            meniu modular

database/
  db.php              conexiune PDO SQLite
  schema/database.sql schema + date demo
  models/             acces la date (PDO)
```

---

## Cerinte TW

| Cerinta | Loc in proiect |
|---------|----------------|
| PHP fara framework | backend/controllers, database/models |
| API Web + Ajax | backend/server.php, frontend/js/api.js |
| HTML, CSS, responsiv | frontend/ |
| SQLite | database/schema/database.sql |
| CSV, JSON, XML export/import | backend/exports/, frontend import/export UI |
| Admin | backend/controllers/AdminApiController.php, frontend/js/pages/admin.js |
| XSS, SQL injection | escapeHtml(), PDO prepare |
| Plugins/servicii Web | backend/plugins/, backend/services/ |

Documentatie: [docs/RAPORT.html](docs/RAPORT.html) · [docs/DESIGN.md](docs/DESIGN.md) · [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) · [docs/CERINTE.md](docs/CERINTE.md)
