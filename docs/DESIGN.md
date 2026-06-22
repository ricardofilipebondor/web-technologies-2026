# Design interfață — eSC Chess Club Manager

Fișiere: `frontend/app.html`, `frontend/index.html`, `frontend/css/style.css`, `frontend/js/pages/*.js`, `frontend/js/router.js`.

## Layout

App shell cu sidebar fix și zonă de conținut. Meniul lateral vine din `GET /auth/menu` și afișează doar modulele permise rolului curent (`backend/config/app.php`).

## Stil

- Paletă gri (`#09090b`, `#71717a`, `#f4f4f5`) pentru lizibilitatea datelor tabulare.
- Font Inter pentru claritate și suport diacritice românești.
- Componente reutilizabile: stat cards (dashboard), tabele, filter bar, toolbar cu acțiuni (export, adăugare).

## Responsive

| Breakpoint | Comportament |
|------------|--------------|
| 992px | Sidebar off-canvas cu overlay |
| 768px | Grid-uri pe o coloană; tabele cu scroll orizontal (`table-wrap`); formulare pe lățime completă |

## Interacțiune

- Navigare hash (`#/members`, `#/dashboard`) fără reîncărcare pagină.
- Date încărcate asincron cu `fetch`; indicator „Se încarcă..." la așteptare.
- Mesaje flash la salvare, ștergere, import.

## Accesibilitate

- `lang="ro"` pe paginile HTML.
- `aria-label` pe butonul meniului mobil.
- Contrast text/fond ridicat; butoane min. 32px înălțime.

## Mapare module UI

| Rută hash | Fișier JS | Conținut |
|-----------|-----------|----------|
| `#/dashboard` | `pages/dashboard.js` | Statistici, activitate recentă |
| `#/members` | `pages/members.js` | Membri, import/export |
| `#/coaches` | `pages/coaches.js` | Antrenori |
| `#/competitions` | `pages/coaches.js` | Competiții |
| `#/teams` | `pages/teams.js` | Echipe, rezultate |
| `#/groups` | `pages/teams.js` | Grupe |
| `#/halls` | `pages/halls.js` | Săli, intervale orare |
| `#/activities` | `pages/halls.js` | Activități |
| `#/participations` | `pages/participations.js` | Participări |
| `#/rankings` | `pages/participations.js` | Clasamente |
| `#/prizes` | `pages/participations.js` | Premii |
| `#/trips` | `pages/trips.js` | Deplasări |
| `#/expenses` | `pages/trips.js` | Cheltuieli |
| `#/reimbursements` | `pages/trips.js` | Deconturi |
| `#/admin` | `pages/admin.js` | Utilizatori, roluri |
