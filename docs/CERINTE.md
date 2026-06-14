# Cerinte TW — eSC

Referinta: [web-projects.html](https://edu.info.uaic.ro/web-technologies/web-projects.html)

## Checklist cerinte tehnice obligatorii

| Cerinta | Status | Loc in proiect |
|---------|--------|----------------|
| PHP server, fara framework | ✅ | `backend/server.php`, `backend/controllers/`, `database/models/` |
| Servicii Web JSON + Ajax/fetch | ✅ | `backend/routes/api.php`, `frontend/js/api.js` |
| HTML + CSS valid, responsive | ✅ | `frontend/`, `frontend/css/style.css` |
| SQLite | ✅ | `database/schema/database.sql`, `database/db.php` |
| SQL injection | ✅ | PDO `prepare()` in `database/models/` |
| XSS | ✅ | `escapeHtml()` in `frontend/js/utils.js` |
| Import/export CSV + JSON + XML | ✅ | `backend/exports/`, UI in `frontend/js/pages/members.js`, `participations.js` |
| Modul administrare | ✅ | Rol `administrator`, `backend/controllers/AdminApiController.php`, `frontend/js/pages/admin.js` |
| Arhitectura plugins/servicii | ✅ | `backend/plugins/`, `backend/services/` |

## Module tema club sah

| Modul | API | Frontend |
|-------|-----|----------|
| Dashboard | `GET /dashboard` | `js/pages/dashboard.js` |
| Membri (+ import/export) | `/members` | `js/pages/members.js` |
| Antrenori | `/coaches` | `js/pages/coaches.js` |
| Echipe | `/teams` | `js/pages/teams.js` |
| Grupe | `/groups` | `js/pages/teams.js` (groups) |
| Sali + intervale | `/halls` | `js/pages/halls.js` |
| Activitati (+ conflicte) | `/activities` | `js/pages/halls.js` (activities) |
| Competitii | `/competitions` | `js/pages/coaches.js` (competitions) |
| Participari + rapoarte | `/participations` | `js/pages/participations.js` |
| Clasamente | `/rankings` | `js/pages/participations.js` (rankings) |
| Premii | `/prizes` | `js/pages/participations.js` (prizes) |
| Deplasari | `/trips` | `js/pages/trips.js` |
| Cheltuieli | `/expenses` | `js/pages/trips.js` (expenses) |
| Deconturi | `/reimbursements` | `js/pages/trips.js` (reimbursements) |
| Administrare | `/admin` | `js/pages/admin.js` |

## Documentatie

| Livrabil | Fisier |
|----------|--------|
| Arhitectura C4 + diagrame | `docs/ARCHITECTURE.md` |
| Design UI + motivatii | `docs/DESIGN.md` |
| Raport Scholarly HTML | `docs/RAPORT.html` |
