# Nextplay API

RESTful API and event-driven backend powering the Nextplay platform.

The application manages sports and leisure activity discovery, venue ingestion pipelines, tournament organization, and real-time player interactions.

---

## Architecture & Code Discipline

The project follows a decoupled, single-responsibility architecture designed to keep the codebase maintainable and testable as features grow:

- **Lean Controllers**: Controllers act purely as traffic routers. They accept HTTP requests, delegate validation, trigger Actions, and return JSON responses or API resources.
- **Actions (`app/Actions`)**: All domain and business logic is encapsulated in dedicated Action classes.
- **DTOs (`app/DTOs`)**: Complex request payloads and state transitions are typed and validated using Data Transfer Objects instead of arbitrary arrays.
- **Self-Documenting Code**: Code is written to be explicit by design through expressive naming and strict typing, avoiding unnecessary inline comments.

```
Request ──> FormRequest ──> Controller ──> DTO ──> Action ──> Model / Service ──> Resource
```

---

## Technical Highlights

- **Framework & Runtime**: Laravel 12 on PHP 8.5, configured for Laravel Octane / FrankenPHP for high throughput.
- **Automated Venue Pipeline**: Ingestion crawler collecting OpenStreetMap data and enriching venue profiles using AI classification (`AIClassifierService` with GPT-4.1 Nano).
- **Durable Workflows**: Long-running ingestion jobs and multi-step background processing orchestrated with durable workflow state machines.
- **Real-Time & Notifications**: WebSockets powered by Laravel Reverb and push notifications delivered through Expo (`laravel-notification-channels/expo`).
- **Auth & Media**: Multi-platform authentication supporting Apple and Google OAuth alongside Laravel Sanctum tokens. Asset storage backed by Cloudflare R2 and AWS S3.
- **API Documentation**: OpenAPI / Swagger 3.0 specification.

---

## Project Structure

```
app/
├── Actions/          # Isolated business logic units
├── DTOs/             # Strongly typed data transfer objects
├── Http/
│   ├── Controllers/  # Route handlers and response dispatchers
│   ├── Requests/     # Validation rules
│   └── Resources/    # JSON transformation layers
├── Models/           # Eloquent models
├── Services/         # External integrations (AI, Apple tokens, crawler)
└── Workflows/        # Durable workflow tasks and activities
```

---

## Getting Started

### Requirements

- PHP 8.5+ (extensions: `pdo`, `mbstring`, `curl`, `bcmath`)
- Composer
- SQLite or MySQL / PostgreSQL

### Installation

1. **Clone the repository:**
   ```bash
   git clone git@github.com:Nextplay-dev/API.git
   cd API
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Environment configuration:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup & migrations:**
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```

5. **Run the local development stack:**
   ```bash
   composer run dev
   ```

   Or run individual services:
   ```bash
   php artisan serve
   php artisan queue:listen
   ```

---

## Testing & Quality

Tests are written using Pest PHP:

```bash
# Run test suite
./vendor/bin/pest

# Check code formatting (Laravel Pint)
./vendor/bin/pint --test
```

---

## License

Proprietary - All rights reserved
