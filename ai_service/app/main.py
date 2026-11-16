from fastapi import FastAPI, Request
from fastapi.responses import JSONResponse

from app.api.classify import router as classify_router
from app.config import get_settings
from app.services.categories import InvalidModelOutput
from app.services.openai_client import (
    LlmNotConfigured,
    LlmRateLimit,
    LlmRequestFailed,
)
from app.services.prompts import UnknownPromptVersion

app = FastAPI(title="Ecig AI classification", version="1.0.0")
app.include_router(classify_router)


@app.get("/health")
def health() -> dict[str, str]:
    return {"status": "ok"}


def error_response(status: str) -> dict:
    settings = get_settings()

    return {
        "status": status,
        "categories": [],
        "provider": settings.llm_provider.lower(),
        "model": settings.openai_model,
        "temperature": settings.openai_temperature,
        "prompt_version": settings.prompt_version,
    }


@app.exception_handler(LlmRateLimit)
def llm_rate_limit(_: Request, exc: LlmRateLimit):
    print(exc)
    return JSONResponse(
        status_code=200,
        content=error_response("NO_CREDITS"),
    )


@app.exception_handler(LlmNotConfigured)
def llm_not_configured(_: Request, exc: LlmNotConfigured) -> JSONResponse:
    print(exc)
    return JSONResponse(
        status_code=200,
        content=error_response(),
    )


@app.exception_handler(LlmRequestFailed)
def llm_request_failed(_: Request, exc: LlmRequestFailed):
    print(exc)
    return JSONResponse(
        status_code=200,
        content=error_response("ERROR"),
    )


@app.exception_handler(InvalidModelOutput)
def invalid_model_output(_: Request, exc: InvalidModelOutput) -> JSONResponse:
    print(exc)
    return JSONResponse(
        status_code=200,
        content=error_response(),
    )


@app.exception_handler(UnknownPromptVersion)
def unknown_prompt_version(_: Request, exc: UnknownPromptVersion) -> JSONResponse:
    print(exc)
    return JSONResponse(
        status_code=200,
        content=error_response(),
    )
