from functools import lru_cache
from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    openai_api_key: str
    llm_provider: str = "OPENAI"
    openai_model: str = "gpt-4.1-mini"
    openai_temperature: float = 0
    prompt_version: str = "v1"

    model_config = SettingsConfigDict(env_file=".env", extra="ignore")


@lru_cache
def get_settings() -> Settings:
    return Settings()
