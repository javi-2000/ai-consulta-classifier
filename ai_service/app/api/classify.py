from fastapi import APIRouter, Depends

from app.config import Settings, get_settings
from app.schemas.classification import ClassifyRequest, ClassifyResponse
from app.services.classifier import Classifier
from app.services.openai_client import OpenAIClient

router = APIRouter()


def get_classifier(settings: Settings = Depends(get_settings)) -> Classifier:
    return Classifier(settings, OpenAIClient(settings))


@router.post("/classify", response_model=ClassifyResponse)
def classify(
    body: ClassifyRequest,
    classifier: Classifier = Depends(get_classifier),
) -> ClassifyResponse:
    return classifier.classify(body.customer_message)
