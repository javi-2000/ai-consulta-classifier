from app.config import Settings
from app.schemas.classification import ClassifyResponse
from app.services.categories import parse_categories
from app.services.openai_client import OpenAIClient
from app.services.prompts import load_prompt


class Classifier:

    def __init__(
        self,
        settings: Settings,
        client: OpenAIClient
    ) -> None:

        self.settings = settings
        self.client = client


    def classify(
        self,
        customer_message: str
    ) -> ClassifyResponse:

        prompt = load_prompt(
            self.settings.prompt_version
        )

        raw = self.client.complete(
            prompt,
            customer_message
        )

        categories = parse_categories(raw)

        return ClassifyResponse(
            status="SUCCESS",
            categories=categories,
            provider=self.settings.llm_provider.lower(),
            model=self.settings.openai_model,
            temperature=self.settings.openai_temperature,
            prompt_version=self.settings.prompt_version,
        )