# Design interfață — eSC Chess Club Manager

## Principii de design

### 1. Layout tip dashboard
Aplicația folosește un **app shell** cu sidebar fix și conținut principal, pattern familiar din aplicațiile administrative. Motivație: utilizatorii (antrenori, administratori) navighează frecvent între module — sidebar-ul oferă acces persistent la toate secțiunile.

### 2. Paletă neutră (alb/gri)
Culorile din `frontend/css/style.css` folosesc tonuri de gri (`#09090b`, `#71717a`, `#f4f4f5`) fără culori stridente. Motivație: interfața este un instrument de lucru profesional, nu un site promoțional — contrastul ridicat asigură lizibilitatea datelor tabulare.

### 3. Tipografie Inter
Fontul Inter este folosit pentru claritate pe ecrane mici și mari. Motivație: font sans-serif modern, optimizat pentru UI, cu suport bun pentru diacritice românești.

### 4. Componente reutilizabile
- **Stat cards** pe dashboard pentru metrici rapide
- **Data tables** pentru liste de entități
- **Filter bar** pentru căutare/filtrare
- **Toolbar** cu acțiuni contextuale (export, adăugare)

Motivație: consistență vizuală — utilizatorul învață un singur pattern și îl aplică în toate modulele.

## Responsive design

### Breakpoint 992px
Sidebar-ul devine off-canvas cu overlay. Motivație: pe tablete, spațiul lateral este redus — meniul hamburger eliberează spațiu pentru tabele.

### Breakpoint 768px
- Grid-urile (`stats-grid`, `grid-2`, `grid-3`) trec la o coloană
- Tabelele devin scroll orizontal (`table-wrap`)
- Formularele ocupă lățimea completă

Motivație: pe telefon, tabelele cu multe coloane nu pot fi comprimate — scroll-ul orizontal păstrează datele intacte.

## Interacțiune

- **Hash router** (`#/members`, `#/dashboard`) — navigare fără reîncărcare pagină
- **Fetch API asincron** — datele se încarcă după render, cu indicator „Se încarcă..."
- **Flash alerts** — feedback vizual pentru acțiuni (salvare, ștergere, import)

## Accesibilitate

- `lang="ro"` pe documente HTML
- `aria-label` pe butonul meniu mobil
- Contrast text/fond conform paletei neutre
- Butoane cu dimensiuni tactile adecvate (`btn-sm` minimum 32px înălțime)

## Alegeri tehnice frontend

| Alegere | Motivație |
|---------|-----------|
| HTML static + JS (fără framework) | Cerință TW; reduce complexitatea |
| SPA cu hash routing | O singură pagină `app.html`, tranziții rapide între module |
| Sesiune PHP + cookie | Autentificare simplă, fără JWT pe client |
| Escape HTML la render | Prevenire XSS la afișarea datelor din API |
