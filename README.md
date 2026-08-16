# Ecommerce Cost Tracker

Laravel REST API for the Bukku take-home coding assessment. The application uses PostgreSQL and runs locally with Docker.

## Run locally

Docker Desktop with Docker Compose is required. No local PHP, Composer, or PostgreSQL installation is needed.

```bash
docker compose up --build -d
```

The application will be available at http://localhost:8000. The first startup installs Composer dependencies, creates `.env`, generates the application key, and runs database migrations automatically.

Scramble is configured for interactive API testing at http://localhost:8000/docs/api. The generated OpenAPI specification is available at http://localhost:8000/docs/api.json.

Demo login: `demo@example.com` / `password`. Recent purchase and sale transactions are seeded automatically.

```bash
# Run tests
docker compose exec app php artisan test

# Stop the application
docker compose down
```

## AI tools usage

OpenAI Codex was used to:

- Review and summarize the assessment requirements.
- Scaffold the Laravel project.
- Create the Docker, Nginx, and PostgreSQL development setup.
- Configure the local environment and database startup flow.
