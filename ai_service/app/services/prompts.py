from pathlib import Path

PROMPTS_DIR = Path(__file__).resolve().parent.parent / "prompts"

ALLOWED_CATEGORIES = {
    "TECH_SUPPORT",
    "COMMERCIAL",
    "ACKNOWLEDGMENT",
    "OTHER",
    "INVALID",
}


class UnknownPromptVersion(Exception):
    def __init__(self, version: str) -> None:
        super().__init__(f"Unknown prompt version: {version}")
        self.version = version


def load_prompt(version: str) -> str:
    path = PROMPTS_DIR / f"{version}.txt"
    if not path.is_file():
        raise UnknownPromptVersion(version)
    return path.read_text(encoding="utf-8")
