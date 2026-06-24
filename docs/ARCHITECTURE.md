# Arhitectură eSC — Model C4

SPA + API REST PHP stateless (JWT), fără SSR. Cerințe: [web-projects.html](https://edu.info.uaic.ro/web-technologies/web-projects.html)

---

## 1. Context (C4 — nivel 1)

```mermaid
flowchart TB
    A[Administrator]
    B[Antrenor]
    C[Responsabil financiar]
    APP[eSC Web App<br/>PHP + SQLite + HTML/CSS/JS]
    A --> APP
    B --> APP
    C --> APP
```

---

## 2. Containere (C4 — nivel 2)

```mermaid
flowchart TB
    %% Stiluri pentru a imita culorile standard C4 Model
    classDef container fill:#438dd5,color:#fff,stroke:#31669a,stroke-width:2px
    classDef db fill:#2b5797,color:#fff,stroke:#204172,stroke-width:2px
    classDef boundary stroke:#666,stroke-dasharray: 5 5,fill:none

    %% Actorii L1 pot fi inclusi sumar pentru context
    USER((Utilizator\nClub de Șah))

    subgraph ENV_BROWSER [Mediul Client - Browser Web]
        SPA["Single-Page Application\n[HTML, CSS, Vanilla JS]\n\nOferă interfața grafică interactivă, rutează paginile local și validează datele înainte de trimitere."]:::container
    end

    subgraph ENV_SERVER [Mediul Server]
        BACKEND["Monolithic Web App & API\n[PHP 8]\n\nRol dublu: Servește fișierele statice inițiale și funcționează ca API REST (JSON) pentru logica de business."]:::container
        DATABASE[("Bază de Date\n[SQLite]\n\nStochează utilizatorii, rolurile, sălile, activitățile și rapoartele financiare.")]:::db
    end

    %% Relatiile
    USER -- "Interacționează cu" --> SPA
    SPA -- "1. Cere fișiere statice (index.html)\n2. Apelează endpoint-uri API [JSON/HTTP]" --> BACKEND
    BACKEND -- "Citește și scrie date [PDO]" --> DATABASE

    %% Aplicarea stilurilor de limite
    class ENV_BROWSER boundary
    class ENV_SERVER boundary
```

---

## 3. Componente backend (C4 — nivel 3)

```mermaid
flowchart LR
    R[Routes api.php] --> S1[SessionsApiController]
    R --> S2[UsersApiController]
    R --> S3[MenuApiController]
    R --> A2[MembersApiController]
    R --> A3[DashboardApiController]
    R --> A4[ModuleApiControllers]
    R --> A5[ResourceApiControllers]
    A2 --> HLP[helpers.php]
    A4 --> HLP
    A5 --> HLP
    HLP --> M[Models]
    A4 --> M
    A3 --> M
    S2 --> M
```

---

## 4. Flux încărcare aplicație

```mermaid
flowchart TB
    U[Utilizator] --> LOGIN[index.html]
    LOGIN -->|POST /sessions| TOKEN[JWT în localStorage]
    TOKEN --> APP[app.html]
    APP --> ME[GET /users/me]
    APP --> MENU[GET /menu]
    MENU --> PAGE[pages/*.js]
    PAGE --> API[backend/server.php]
```

---

## 5. Flux Ajax (exemplu: membri)

```mermaid
sequenceDiagram
    participant U as Utilizator
    participant P as members.js
    participant A as api.js
    participant S as server.php
    participant C as MembersApiController
    participant H as helpers.php
    participant M as Member
    participant DB as SQLite

    U->>P: #/members
    P->>A: GET /members
    A->>S: fetch + Authorization Bearer
    S->>S: AuthMiddleware::authenticate()
    S->>C: index()
    C->>C: requireModule members
    C->>M: all()
    M->>DB: SELECT
    DB-->>P: JSON items + _links
    P-->>U: tabel HTML

    Note over U,H: Profil membru: GET /members/{id}<br/>C apelează getMemberProfile() din helpers.php
```

---

## 6. Module (15 module UI)

Lista modulelor este definită în `$MENU_ITEMS` din `backend/config/app.php`. Accesul pe rol: `$ROLE_PERMISSIONS` + `userCanAccess()`. Meniul API: `MenuApiController` filtrează elementele după rol.

Dashboard, Members, Coaches, Teams, Groups, Halls, Activities, Competitions, Participations, Rankings, Prizes, Trips, Expenses, Reimbursements, Admin.

Modulul *reimbursements* agregă date din `trips`, `expenses`, `trip_members` (fără tabel propriu). Raportul este construit cu `getTripReport()` din `helpers.php`.

---

## 7. Autentificare și roluri

```mermaid
flowchart TB
    REQ[Request API] --> HDR{Header Authorization Bearer?}
    HDR -->|Nu / invalid| E401[401]
    HDR -->|JWT valid| MOD{userCanAccess modul?}
    MOD -->|Nu| E403[403]
    MOD -->|Da| OK[Controller]
```

Autentificare **stateless**: `JwtService` (HS256) emite token la `POST /sessions`; clientul îl trimite la fiecare cerere. Fără `session_start()` sau cookie de sesiune.

| Resursă | Metodă | Rol |
|---------|--------|-----|
| `/sessions` | POST | Login public → `access_token` |
| `/sessions` | DELETE | Logout (client șterge tokenul) |
| `/users` | POST | Înregistrare publică sau creare admin |
| `/users/me` | GET | Utilizator curent |
| `/users` | GET | Listă utilizatori (admin) |
| `/users/{id}` | PUT / DELETE | Rol / ștergere (admin) |
| `/roles` | GET | Roluri disponibile (admin) |
| `/menu` | GET | Elemente meniu filtrate pe rol |

---

## 7b. Convenții API REST

- **Resurse** ca URI-uri la plural (`/members`, `/teams/{id}/members`)
- **Verbe HTTP**: GET (citire), POST (creare → 201 + `Location`), PUT (actualizare → 200), DELETE (ștergere → 204)
- **Reprezentări** JSON directe, fără envelope `{ success, data }`
- **Erori** RFC 7807: `{ type, title, status, detail }`
- **HATEOAS**: câmp `_links` pe resurse și colecții (`RestHelper`, `Hateoas`)
- **Export**: query `?format=csv|json|xml` pe colecții; PDF pe deconturi (`GET /reimbursements/{id}/export?format=pdf`)

Funcția `exportList()` din `helpers.php` centralizează exportul CSV/JSON/XML pentru rapoarte participări și clasamente.

---

## 8. ER — utilizatori

```mermaid
erDiagram
    roles ||--o{ users : are
    roles {
        int id PK
        text role_name UK
    }
    users {
        int id PK
        int role_id FK
        text username UK
        text email UK
        text password_hash
    }
```

---

## 9. ER — antrenori, membri, grupe

```mermaid
erDiagram
    coaches ||--o{ members : antreneaza
    coaches ||--o{ groups : coordoneaza
    groups ||--o{ group_members : contine
    members ||--o{ group_members : apartine
```

---

## 10. ER — săli și activități

```mermaid
erDiagram
    halls ||--o{ hall_slots : are
    halls ||--o{ activities : gazduieste
    coaches ||--o{ activities : conduce
```

Conflicte la programare: `Activity::hasHallConflict()`, `Activity::hasCoachConflict()`.

---

## 11. ER — echipe și competiții

```mermaid
erDiagram
    teams ||--o{ team_members : include
    members ||--o{ team_members : face_parte
    teams ||--o{ team_results : obtine
    competitions ||--o{ team_results : evalueaza
    competitions ||--o{ participations : are
    members ||--o{ participations : participa
    members ||--o{ prizes : castiga
    competitions ||--o{ prizes : acorda
```

---

## 12. ER — deplasări

```mermaid
erDiagram
    teams ||--o{ trips : organizeaza
    trips ||--o{ trip_members : include
    members ||--o{ trip_members : participa
    trips ||--o{ expenses : genereaza
```

---

## 13. Etape dezvoltare

```mermaid
flowchart LR
    E1[Bază date] --> E2[API REST]
    E2 --> E3[Frontend SPA]
    E3 --> E4[Module CRUD]
    E4 --> E5[Import/Export]
    E5 --> E6[Admin + roluri]
    E6 --> E7[Documentație]
```

| Etapă | Conținut |
|-------|----------|
| 1 | `database.sql`, `install.php` |
| 2 | `server.php`, Router, JWT, Auth |
| 3 | `app.html`, hash router |
| 4 | 15 module CRUD (controllers + pages) |
| 5 | CSV, JSON, XML, PDF |
| 6 | `UsersApiController`, permisiuni în `app.php` |
| 7 | RAPORT, diagrame, film |

---

## Fișiere cheie

| Strat | Cale |
|-------|------|
| Entry | `router.php`, `backend/server.php` |
| Rute | `backend/routes/api.php` |
| Securitate | `backend/middleware/AuthMiddleware.php`, `backend/services/JwtService.php` |
| Config + meniu + roluri | `backend/config/app.php` |
| Funcții reutilizabile | `backend/helpers.php` |
| REST helpers | `backend/utils/Response.php`, `Hateoas.php`, `RestHelper.php` |
| Export | `backend/exports/DataExporter.php`, `PdfExporter.php` |
| Client | `frontend/js/api.js`, `frontend/js/router.js`, `frontend/js/utils.js` |
| Schema | `database/schema/database.sql` |
