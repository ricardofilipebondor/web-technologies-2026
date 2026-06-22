# Arhitectură eSC — Model C4

SPA + API REST PHP, fără SSR. Cerințe: [web-projects.html](https://edu.info.uaic.ro/web-technologies/web-projects.html)

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
    subgraph CLIENT[Browser]
        HTML[HTML static<br/>index.html, app.html]
        JS[JavaScript<br/>api.js, router.js, pages]
        CSS[CSS responsive<br/>style.css]
    end

    subgraph SERVER[PHP built-in server]
        ROUTER[router.php]
        API[backend/server.php]
        CTRL[Controllers]
        MW[AuthMiddleware]
        PLG[PluginManager]
        SVC[Services]
        EXP[Exports CSV/JSON/XML/PDF]
    end

    subgraph DATA[Persistență]
        MODEL[Models PDO]
        DB[(SQLite)]
    end

    HTML --> JS
    JS -->|fetch| ROUTER --> API --> MW --> CTRL
    CTRL --> SVC --> MODEL --> DB
    CTRL --> MODEL
    CTRL --> EXP
    PLG -.->|meniu pe rol| JS
```

---

## 3. Componente backend (C4 — nivel 3)

```mermaid
flowchart LR
    R[Routes api.php] --> A1[AuthApiController]
    R --> A2[MembersApiController]
    R --> A3[DashboardApiController]
    R --> A4[ModuleApiControllers]
    R --> A5[AdminApiController]
    A2 --> S1[MembersService] --> M[Models]
    A4 --> S2[CompetitionsService]
    A4 --> S3[TeamsService]
    A4 --> S4[TripsService]
    S2 & S3 & S4 --> M
    A4 --> M
    A3 --> M
    A5 --> M
```

---

## 4. Flux încărcare aplicație

```mermaid
flowchart TB
    U[Utilizator] --> LOGIN[index.html]
    LOGIN -->|autentificat| APP[app.html]
    APP --> AUTH[POST /auth/login]
    APP --> MENU[GET /auth/menu]
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
    participant M as Member
    participant DB as SQLite

    U->>P: #/members
    P->>A: GET /members
    A->>S: fetch + cookie sesiune
    S->>C: index()
    C->>C: requireModule members
    C->>M: all()
    M->>DB: SELECT
    DB-->>P: JSON
    P-->>U: tabel HTML
```

---

## 6. Module (15 plugin-uri)

Fiecare modul are un plugin în `backend/plugins/modules/`. Accesul pe rol: `backend/config/app.php`.

Dashboard, Members, Coaches, Teams, Groups, Halls, Activities, Competitions, Participations, Rankings, Prizes, Trips, Expenses, Reimbursements, Admin.

Modulul *reimbursements* agregă date din `trips`, `expenses`, `trip_members` (fără tabel propriu).

---

## 7. Autentificare și roluri

```mermaid
flowchart TB
    REQ[Request API] --> SESS{Sesiune validă?}
    SESS -->|Nu| E401[401]
    SESS -->|Da| MOD{userCanAccess modul?}
    MOD -->|Nu| E403[403]
    MOD -->|Da| OK[Controller]
```

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
| 2 | `server.php`, Router, Auth |
| 3 | `app.html`, hash router |
| 4 | 15 plugin-uri |
| 5 | CSV, JSON, XML, PDF |
| 6 | `AdminApiController`, permisiuni |
| 7 | RAPORT, diagrame, film |

---

## Fișiere cheie

| Strat | Cale |
|-------|------|
| Entry | `router.php`, `backend/server.php` |
| Rute | `backend/routes/api.php` |
| Securitate | `backend/middleware/AuthMiddleware.php` |
| Roluri | `backend/config/app.php` |
| Client | `frontend/js/api.js`, `frontend/js/router.js` |
| Schema | `database/schema/database.sql` |
