from typing import Literal

from pydantic import BaseModel, Field


class ClassifyRequest(BaseModel):
    customer_message: str = Field(
        min_length=10,
        max_length=500
    )


class ClassifyResponse(BaseModel):
    status: Literal[
        "SUCCESS",
        "ERROR",
        "TIMEOUT",
        "NO_CREDITS"
    ]

    categories: list[str]
    provider: str
    model: str
    temperature: float
    prompt_version: str