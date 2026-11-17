# Ecig Logística — Clasificación de consultas con IA

Mini app full-stack (Laravel + MySQL + FastAPI) para clasificación automática de consultas de clientes y su posterior almacenamiento en base de datos.

## Requisitos

- Docker Desktop / Docker Compose
- Puertos libres en tu máquina:
  - `3306` → MySQL
  - `8000` → Laravel (Blade)
  - `8001` → FastAPI (clasificación LLM)

## Arranque rápido

```bash
git clone https://github.com/javi-2000/ai-consulta-classifier.git
cd javi_hueso_ai_test
```

1. Rellena la API key en `ai_service/.env`.
```env
OPENAI_API_KEY=sk-...
```

Sin esa key,`POST /classify` devuelve 503.

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

# Recrear DB desde cero (borra datos y vuelve a seedear)
docker compose exec backend php artisan migrate:fresh --seed

# Parar
docker compose down
```

## Stack actual

- Laravel (Blade + controllers) en el contenedor `ecig_backend`
- MySQL 8.4 en el contenedor `ecig_mysql`
- FastAPI en el contenedor `ecig_ai` (`POST /classify`)
