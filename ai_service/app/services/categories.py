import json

from app.services.prompts import ALLOWED_CATEGORIES


class InvalidModelOutput(Exception):
    pass


def parse_categories(raw: str) -> list[str]:
    try:
        payload = json.loads(raw)
    except json.JSONDecodeError as error:
        raise InvalidModelOutput("Model response is not valid JSON.") from error

    categories = payload.get("categories") if isinstance(payload, dict) else None

    if not isinstance(categories, list) or not categories:
        raise InvalidModelOutput(
            "Model response must include a non-empty categories array."
        )

    normalized: list[str] = []

    for category in categories:
        if not isinstance(category, str):
            raise InvalidModelOutput("Each category must be a string.")

        name = category.strip().upper()

        if name not in ALLOWED_CATEGORIES:
            raise InvalidModelOutput(f"Unknown category: {name}")

        if name not in normalized:
            normalized.append(name)

    # INVALID solo puede aparecer como categoría única
    if "INVALID" in normalized and len(normalized) > 1:
        raise InvalidModelOutput("INVALID cannot be combined with other categories.")

    return normalized
