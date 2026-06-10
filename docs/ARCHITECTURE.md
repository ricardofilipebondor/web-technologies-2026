# Arhitectura eSC

Diagrame proiect Tehnologii Web.

---

## 1. Context — cine foloseste aplicatia

```mermaid
%%{init: {'theme':'base','themeVariables':{'darkMode':false,'background':'#ffffff','mainBkg':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','clusterBkg':'#ffffff','clusterBorder':'#000000','titleColor':'#000000','fontSize':'24px','fontFamily':'Arial'},'flowchart':{'nodeSpacing':80,'rankSpacing':90,'useMaxWidth':true,'padding':30}}}%%
flowchart TB
    A["<b>Administrator</b>"]
    B["<b>Antrenor</b>"]
    C["<b>Responsabil financiar</b>"]

    APP["<b>eSC Web App</b><br/>PHP + SQLite"]

    A --> APP
    B --> APP
    C --> APP

    classDef alb fill:#ffffff,stroke:#000000,color:#000000,stroke-width:3px
    class A,B,C,APP alb
```

---

## 2. Containere — Browser si Server

```mermaid
%%{init: {'theme':'base','themeVariables':{'darkMode':false,'background':'#ffffff','mainBkg':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','clusterBkg':'#ffffff','clusterBorder':'#000000','fontSize':'22px','fontFamily':'Arial'},'flowchart':{'nodeSpacing':70,'rankSpacing':80,'useMaxWidth':true,'padding':25}}}%%
flowchart TB
    subgraph CLIENT[" BROWSER "]
        direction TB
        HTML["<b>HTML + CSS</b><br/>pagini Web"]
        AJAX["<b>Ajax</b><br/>microservices.js"]
    end

    subgraph SERVER[" SERVER PHP "]
        direction TB
        ROUTER["<b>index.php</b>"]
        CTRL["<b>Controllers</b>"]
        API["<b>microservices.php</b>"]
        SVC["<b>Services</b>"]
        MODEL["<b>Models PDO</b>"]
    end

    DB[("<b>SQLite</b>")]

    HTML --> ROUTER --> CTRL --> MODEL --> DB
    AJAX --> API --> SVC --> MODEL

    classDef alb fill:#ffffff,stroke:#000000,color:#000000,stroke-width:3px
    class HTML,AJAX,ROUTER,CTRL,API,SVC,MODEL,DB alb
```

---

## 3. Flux pagina Web — request clasic

```mermaid
%%{init: {'theme':'base','themeVariables':{'darkMode':false,'background':'#ffffff','mainBkg':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','fontSize':'22px','fontFamily':'Arial'},'flowchart':{'nodeSpacing':60,'rankSpacing':70,'useMaxWidth':true,'padding':20}}}%%
flowchart TB
    U["<b>Utilizator</b>"]
    I["<b>index.php</b>"]
    C["<b>Controller</b>"]
    M["<b>Model</b>"]
    D[("<b>SQLite</b>")]
    V["<b>View HTML</b>"]

    U --> I --> C --> M --> D
    C --> V --> U

    classDef alb fill:#ffffff,stroke:#000000,color:#000000,stroke-width:3px
    class U,I,C,M,D,V alb
```

---

## 4. Flux Ajax

```mermaid
%%{init: {'theme':'base','themeVariables':{'darkMode':false,'background':'#ffffff','mainBkg':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','actorBkg':'#ffffff','actorBorder':'#000000','actorTextColor':'#000000','signalColor':'#000000','labelBoxBkgColor':'#ffffff','labelTextColor':'#000000','noteBkgColor':'#ffffff','noteTextColor':'#000000','fontSize':'20px','fontFamily':'Arial'},'sequence':{'actorMargin':100,'messageMargin':50,'mirrorActors':false,'wrap':true}}}%%
sequenceDiagram
    autonumber
    participant U as Utilizator
    participant JS as microservices.js
    participant API as microservices.php
    participant S as MembersService
    participant DB as SQLite

    U->>JS: Deschide Dashboard
    JS->>API: fetch members list
    API->>S: list()
    S->>DB: SELECT
    DB-->>S: date
    S-->>API: JSON
    API-->>JS: raspuns
    JS-->>U: Afiseaza numar membri
```

---

## 5. Plugin-uri si micro-servicii

```mermaid
%%{init: {'theme':'base','themeVariables':{'darkMode':false,'background':'#ffffff','mainBkg':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','fontSize':'22px','fontFamily':'Arial'},'flowchart':{'nodeSpacing':70,'rankSpacing':80,'useMaxWidth':true,'padding':25}}}%%
flowchart TB
    PM["<b>PluginManager</b>"]

    PM --> P1["<b>MembersPlugin</b>"]
    PM --> P2["<b>CompetitionsPlugin</b>"]
    PM --> P3["<b>TripsPlugin</b>"]

    P1 --> API["<b>microservices.php</b>"]
    P2 --> API
    P3 --> API

    API --> S1["<b>MembersService</b>"]
    API --> S2["<b>CompetitionsService</b>"]
    API --> S3["<b>TripsService</b>"]

    classDef alb fill:#ffffff,stroke:#000000,color:#000000,stroke-width:3px
    class PM,P1,P2,P3,API,S1,S2,S3 alb
```

---

## 6. Baza de date — utilizatori si roluri

```mermaid
%%{init: {'theme':'base','themeVariables':{'darkMode':false,'background':'#ffffff','mainBkg':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','fontSize':'20px','fontFamily':'Arial'}}}%%
erDiagram
    roles ||--o{ users : are

    roles {
        int id PK
        text role_name
    }
    users {
        int id PK
        text username
        text password_hash
        int role_id FK
    }
```

---

## 7. Baza de date — membri si antrenori

```mermaid
%%{init: {'theme':'base','themeVariables':{'darkMode':false,'background':'#ffffff','mainBkg':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','fontSize':'20px','fontFamily':'Arial'}}}%%
erDiagram
    coaches ||--o{ members : antreneaza
    coaches ||--o{ groups : coordoneaza
    groups }o--o{ members : contine

    coaches {
        int id PK
        text nume
        text specializare
    }
    members {
        int id PK
        text nume
        text categorie
        int coach_id FK
    }
    groups {
        int id PK
        text denumire
        int coach_id FK
    }
```

---

## 8. Baza de date — competitii si premii

```mermaid
%%{init: {'theme':'base','themeVariables':{'darkMode':false,'background':'#ffffff','mainBkg':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','fontSize':'20px','fontFamily':'Arial'}}}%%
erDiagram
    competitions ||--o{ participations : are
    members ||--o{ participations : participa
    members ||--o{ prizes : castiga
    competitions ||--o{ prizes : ofera

    competitions {
        int id PK
        text nume
        text tip
    }
    participations {
        int id PK
        int member_id FK
        int competition_id FK
    }
    prizes {
        int id PK
        text titlu
        int member_id FK
    }
```

---

## 9. Baza de date — deplasari si cheltuieli

```mermaid
%%{init: {'theme':'base','themeVariables':{'darkMode':false,'background':'#ffffff','mainBkg':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','fontSize':'20px','fontFamily':'Arial'}}}%%
erDiagram
    teams ||--o{ trips : organizeaza
    trips ||--o{ expenses : are
    trips ||--o{ reimbursements : decont

    teams {
        int id PK
        text denumire
    }
    trips {
        int id PK
        text destinatie
        int team_id FK
    }
    expenses {
        int id PK
        text tip
        real suma
    }
```

---

## 10. Autentificare si acces pe roluri

```mermaid
%%{init: {'theme':'base','themeVariables':{'darkMode':false,'background':'#ffffff','mainBkg':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','fontSize':'22px','fontFamily':'Arial'},'flowchart':{'nodeSpacing':60,'rankSpacing':70,'useMaxWidth':true,'padding':20}}}%%
flowchart TB
    START(["<b>Request</b>"])
    LOGIN{"<b>Logat?</b>"}
    AUTH["<b>Pagina Login</b>"]
    ROLE{"<b>Rol OK?</b>"}
    DENY["<b>Mesaj eroare</b>"]
    PAGE["<b>Controller + View</b>"]
    CHECK{"<b>Parola corecta?</b>"}
    SESSION["<b>Sesiune PHP</b>"]
    DASH["<b>Dashboard</b>"]

    START --> LOGIN
    LOGIN -->|Nu| AUTH
    LOGIN -->|Da| ROLE
    ROLE -->|Nu| DENY
    ROLE -->|Da| PAGE
    AUTH --> CHECK
    CHECK -->|Da| SESSION
    CHECK -->|Nu| AUTH
    SESSION --> DASH

    classDef alb fill:#ffffff,stroke:#000000,color:#000000,stroke-width:3px
    class START,LOGIN,AUTH,ROLE,DENY,PAGE,CHECK,SESSION,DASH alb
```

---

## 11. Etape proiect (timeline)

```mermaid
%%{init: {'theme':'base','themeVariables':{'darkMode':false,'background':'#ffffff','mainBkg':'#ffffff','primaryColor':'#ffffff','primaryTextColor':'#000000','primaryBorderColor':'#000000','lineColor':'#000000','fontSize':'22px','fontFamily':'Arial'},'flowchart':{'nodeSpacing':50,'rankSpacing':60,'useMaxWidth':true,'padding':20}}}%%
flowchart TB
    E1["<b>1. SQLite</b><br/>install.php"]
    E2["<b>2. Auth</b><br/>Router"]
    E3["<b>3. Module</b><br/>CRUD"]
    E4["<b>4. Micro-servicii</b><br/>Ajax"]
    E5["<b>5. Import</b><br/>Export"]
    E6["<b>6. Film demo</b>"]

    E1 --> E2 --> E3 --> E4 --> E5 --> E6

    classDef alb fill:#ffffff,stroke:#000000,color:#000000,stroke-width:3px
    class E1,E2,E3,E4,E5,E6 alb
```
