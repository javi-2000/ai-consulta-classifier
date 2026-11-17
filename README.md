# Ecig Logística — Clasificación de consultas con IA

Mini app full-stack (Laravel + MySQL + FastAPI) para clasificación automática de consultas de clientes y su posterior almacenamiento en base de datos.

## Requisitos

- Docker instalado:
  - Windows / macOS: Docker Desktop
  - Linux: Docker Engine + Docker Compose plugin

- Puertos libres en tu máquina:
  - `3306` → MySQL
  - `8000` → Laravel (Blade)
  - `8001` → FastAPI (clasificación LLM)

## Arranque rápido

1. Clona el repositorio
```bash
git clone https://github.com/javi-2000/ai-consulta-classifier.git
```

2. Accede a la raíz del proyecto
```bash
cd ai-consulta-classifier
```

1. Rellena la API key en `ai_service/.env`.
```env
OPENAI_API_KEY=sk-...
```

Sin esa key, el servicio de clasificación no podrá procesar consultas correctamente.

2. Levanta el proyecto:

```bash
docker compose up --build
```

3. Abre la app:

[http://localhost:8000](http://localhost:8000)

Servicio IA en [http://localhost:8001/health](http://localhost:8001/health). Clasificar una consulta:

```bash
curl -X POST http://localhost:8001/classify \
  -H "Content-Type: application/json" \
  -d "{\"customer_message\":\"No encuentro el seguimiento de mi pedido y además quería saber si hay descuentos.\"}"
```

## Comandos útiles

```bash
# Ver logs
docker compose logs -f backend
docker compose logs -f llm

# Recrear DB desde cero (borra datos y vuelve a seedear)
docker compose exec backend php artisan migrate:fresh --seed

# Parar
docker compose down
```

## Stack actual

- Laravel (Blade + controllers) en el contenedor `ecig_backend`
- MySQL 8.4 en el contenedor `ecig_mysql`
- FastAPI en el contenedor `ecig_ai` (`POST /classify`)
