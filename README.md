### README — SwiftNotify (Tataouine SMS Alert Platform)

A Laravel + Vue (Inertia) platform to send targeted SMS alerts to the Tataouine population via Twilio, with smart group suggestions, cost tracking, dark mode UI, and role-based access.

### Features
- Smart Group Management: auto-suggest groups, combinations, relevance scoring
- Message Intelligence: smart templates, auto-complete, context awareness
- SMS: bulk/group/zone/all sending, progress, history, status
- SMS Cost Tracking: per-recipient cost, totals, analytics
- Contacts & Groups: CSV import, map areas (Leaflet), tags/categories
- RBAC: Admin and Agent roles
- OAuth: Google Sign-In (desktop), Email verification
- UI/UX: Responsive, modern dark mode, dashboards

### Tech Stack
- Backend: Laravel 10+/PHP 8.2+
- Frontend: Vue 3 + Inertia.js, Tailwind CSS
- SMS: Twilio API
- Maps: Leaflet + Leaflet.draw
- DB: MySQL

### Prerequisites
- PHP 8.2+, Composer
- Node.js 18+, npm
- MySQL 8+
- OpenSSL (for HTTPS if using tunnels)

### Quick Start
1) Clone and install
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

2) Create database and configure `.env`
- DB_DATABASE, DB_USERNAME, DB_PASSWORD
- APP_URL=http://127.0.0.1:3000

3) Migrate and seed
```bash
php artisan migrate
php artisan db:seed --class=ContactGroupSeeder
php artisan db:seed --class=MessageTemplateSeeder
```

4) Configure Twilio in `.env`
```env
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_PHONE_NUMBER=+1xxxxxxxxxx
```

5) Configure mail (Gmail App Password recommended)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=YOUR_16_CHAR_APP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@gmail.com
MAIL_FROM_NAME="SwiftNotify"
```

### Google OAuth (Desktop-only by default)
Google blocks private IPs (192.168.x.x) for Web OAuth. Use localhost for desktop; for phones, use an HTTPS tunnel (see below).

- Google Cloud Console → Credentials → OAuth client:
  - Authorized JavaScript origin: http://127.0.0.1:3000
  - Authorized redirect URI: http://127.0.0.1:3000/auth/google/callback
- .env:
```env
APP_URL=http://127.0.0.1:3000
GOOGLE_CLIENT_ID=xxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxxxxxxxxxxx
GOOGLE_REDIRECT_URI=http://127.0.0.1:3000/auth/google/callback
SANCTUM_STATEFUL_DOMAINS=127.0.0.1:3000
SESSION_DOMAIN=127.0.0.1
SESSION_SECURE_COOKIE=false
```

### Dev Servers
- Laravel (serve on 127.0.0.1:3000 to match Google OAuth)
```bash
php artisan serve --host=127.0.0.1 --port=3000
```
- Vite (configured to 127.0.0.1:5174)
```bash
npm run dev
```
- Open: http://127.0.0.1:3000

### Phone (LAN) Access
- Open the app from your phone: http://YOUR_LAN_IP:3000
- Start Laravel: php artisan serve --host=0.0.0.0 --port=3000
- Adjust Vite HMR to your LAN IP if needed (vite.config.js):
  - server.hmr.host='YOUR_LAN_IP', server.port=5173, transformAssetUrls.base to that host:port
- Note: Google OAuth won’t work on LAN IP. Use email/password login or a public HTTPS tunnel.

### Public HTTPS Tunnel (phone + Google OAuth)
Use ngrok or Cloudflare Tunnel:
- Get https://YOUR-SUBDOMAIN.ngrok-free.app
- In Google Console:
  - Origin: https://YOUR-SUBDOMAIN.ngrok-free.app
  - Redirect: https://YOUR-SUBDOMAIN.ngrok-free.app/auth/google/callback
- Update `.env` APP_URL/GOOGLE_REDIRECT_URI accordingly
- Set SESSION_SECURE_COOKIE=true

### Important Routes
- Messages
  - GET /messages (compose)
  - POST /messages (send)
  - GET /messages/history (history)
  - POST /messages/smart-suggestions (smart suggestions)
- Costs
  - GET /messages/cost-analytics
- Admin
  - Users management (via dashboard)

### Seeding Demo Data
```bash
php artisan db:seed --class=ContactGroupSeeder
php artisan db:seed --class=MessageTemplateSeeder
```

### Common Tasks
- Clear caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
```
- Fix Vite port busy
```bash
taskkill /IM node.exe /F
npm run dev
```
- Test mail quickly
```bash
php artisan tinker
>>> Mail::raw('Test', fn($m) => $m->to('you@example.com')->subject('Test'));
```
- Test Twilio config
```bash
GET /messages/test-twilio
```

### Troubleshooting
- Google OAuth “redirect_uri_mismatch”:
  - Ensure the browser origin and Google Console redirect match exactly.
  - For desktop: use 127.0.0.1:3000 only. For phone: use HTTPS tunnel.
- CORS/HMR issues:
  - Keep a single consistent origin (127.0.0.1:3000 for desktop).
  - Vite configured to 127.0.0.1:5174; don’t mix with LAN IP.
- Email “535-5.7.8 Username and Password not accepted”:
  - Use a Gmail App Password (with 2FA enabled), not your normal password.
  - Consider MAIL_MAILER=log or Mailtrap in development.
- “Cannot read properties of null (method)” in pagination:
  - Fixed; if you still see it, hard refresh to clear stale assets.
- SMS “No contacts found”:
  - Ensure the selected group actually contains contacts.
  - Re-select group after refresh; confirm POST body has group_ids.

### Notes
- Email verification links are generated from APP_URL; verify that APP_URL matches the origin where you click the link. Use the same PC/browser session.
- Notifications read state is persisted locally and survives navigation.

### Build for Production (basic)
- Set production `.env` with your domain + SSL
- Build assets:
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### License
For academic/internship use. Adapt as needed for production.
