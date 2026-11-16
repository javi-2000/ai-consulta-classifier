from openai import APIError, OpenAI, RateLimitError

from app.config import Settings


class LlmNotConfigured(Exception):
    pass


class LlmRequestFailed(Exception):
    pass


class LlmRateLimit(Exception):
    pass


class OpenAIClient:
    def __init__(self, settings: Settings) -> None:
        self.settings = settings
        self.client = (
            OpenAI(api_key=settings.openai_api_key) if settings.openai_api_key else None
        )

    def complete(self, system_prompt: str, customer_message: str) -> str:

        print("=== OPENAI CLIENT START ===", flush=True)

        print(f"MODEL: {self.settings.openai_model}", flush=True)

        print(f"TEMP: {self.settings.openai_temperature}", flush=True)

        print(f"API KEY EXISTS: {bool(self.settings.openai_api_key)}", flush=True)

        if self.client is None:
            raise LlmNotConfigured("OPENAI_API_KEY is not configured.")

        try:
            response = self.client.chat.completions.create(
                model=self.settings.openai_model,
                temperature=self.settings.openai_temperature,
                response_format={"type": "json_object"},
                messages=[
                    {"role": "system", "content": system_prompt},
                    {"role": "user", "content": customer_message},
                ],
            )

        except RateLimitError as error:
            print("OPENAI QUOTA ERROR", flush=True)
            print(error, flush=True)

            raise LlmRateLimit("OpenAI quota exceeded.") from error

        except APIError as error:
            print("OPENAI API ERROR:", flush=True)
            print(error, flush=True)

            raise LlmRequestFailed("OpenAI request failed.") from error

        except Exception as error:
            print("UNEXPECTED ERROR:", flush=True)
            print(error, flush=True)

            raise LlmRequestFailed("Unexpected OpenAI error.") from error

        content = response.choices[0].message.content

        if not content:
            raise LlmRequestFailed("OpenAI returned an empty response.")

        return content
