# eSC — Chess Club Manager

Aplicație web pentru gestionarea unui club de șah: membri, antrenori, săli, activități, competiții, premii, deplasări.

Proiect la **Tehnologii Web** (UAIC). Cerințe: [web-projects.html](https://edu.info.uaic.ro/web-technologies/web-projects.html)

Licență: [MIT](LICENSE)

---

## Instalare

```cmd
cd web-technologies-2026
C:\xampp\php\php.exe install.php
C:\xampp\php\php.exe -S localhost:8000 router.php
```

Deschide: http://localhost:8000/frontend/index.html

| User  | Parola      | Rol           |
|-------|-------------|---------------|
| admin | admin pass  | administrator |

---

## Structură

```
frontend/     HTML, CSS, JS (SPA hash router, fetch API)
backend/      API REST JSON stateless JWT (server.php, controllers, helpers)
database/     SQLite, modele PDO, schema SQL
docs/         RAPORT.html, ARCHITECTURE.md, DESIGN.md
```

**Flux:** Browser → `frontend/*.html` → `fetch /backend/server.php/...` + JWT Bearer → controllers → SQLite

**Autentificare:** `POST /sessions` (login) → token JWT în `localStorage`; `GET /users/me`, `GET /menu` după login.

**Backend:** controllers CRUD, `helpers.php` (profil membru, rapoarte, export), `config/app.php` (roluri + meniu), `exports/` (CSV, JSON, XML, PDF).

---

## Documentație

| Document | Conținut |
|----------|----------|
| [docs/RAPORT.html](docs/RAPORT.html) | Cerințe funcționale și tehnice |
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | Arhitectură C4, ER, flux Ajax, etape dezvoltare |
| [docs/DESIGN.md](docs/DESIGN.md) | Design UI responsive |
| [Video demonstrație (Google Drive)](https://drive.google.com/file/d/1uCbAkeCKgMQj4y6krD8_pOES3L6RFXVv/view?usp=sharing) | Film demonstrativ al aplicației |
