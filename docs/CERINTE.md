# Cerinte TW — eSC

Referinta: [web-projects.html](https://edu.info.uaic.ro/web-technologies/web-projects.html)

## Checklist

| Cerinta | Fisier / loc |
|---------|--------------|
| PHP server, fara framework | `index.php`, `controllers/`, `models/` |
| Servicii Web JSON | `api/microservices.php` |
| Ajax | `assets/js/microservices.js` |
| HTML + CSS | `views/`, `assets/css/style.css` |
| SQLite | `database.sql`, `config/database.php` |
| Template-uri | `views/`, `render()` |
| Responsiv | `style.css`, `app.js` |
| SQL injection | PDO prepare in `models/` |
| XSS | `e()` in `helpers/functions.php` |
| CSV + JSON | `exports/` |
| Admin | rol `administrator` in `config/app.php` |

## Module tema club sah

| Modul | Controller |
|-------|------------|
| Membri | MemberController |
| Antrenori | CoachController |
| Grupe | GroupController |
| Sali | HallController |
| Activitati | ActivityController |
| Competitii | CompetitionController |
| Participari | ParticipationController |
| Clasamente | RankingController |
| Premii | PrizeController |
| Echipe | TeamController |
| Deplasari | TripController |
| Cheltuieli | ExpenseController |
| Deconturi | ReimbursementController |
