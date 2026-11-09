#!/usr/bin/env bash
# Build this repo with Cloud Build and deploy the Docker image to Cloud Run.
# Step-by-step: docs/deploying-on-cloud-run.md

set -euo pipefail

PROJECT="${PROJECT:-}"
if [[ -z "$PROJECT" ]]; then
  PROJECT="$(gcloud config get-value project 2>/dev/null || true)"
fi
if [[ -z "$PROJECT" || "$PROJECT" == "(unset)" ]]; then
  echo "Set PROJECT or run: gcloud config set project PROJECT_ID" >&2
  exit 1
fi

REGION="${REGION:-europe-west2}"
SERVICE="${SERVICE:-govuk-frontend-example-php}"
SECRET="${SECRET:-govuk-frontend-example-php-app-key}"
MEMORY="${MEMORY:-1Gi}"
CPU="${CPU:-1}"
CONCURRENCY="${CONCURRENCY:-8}"
MIN_INSTANCES="${MIN_INSTANCES:-0}"
MAX_INSTANCES="${MAX_INSTANCES:-3}"

if [[ "$CONCURRENCY" != "8" ]]; then
  echo "CONCURRENCY is ${CONCURRENCY}, but docker/php-fpm.conf sets pm.max_children = 8. Keep them equal." >&2
  exit 1
fi

echo "Enabling Cloud Run, Cloud Build, Artifact Registry, and Secret Manager on ${PROJECT}"
gcloud services enable \
  run.googleapis.com \
  cloudbuild.googleapis.com \
  artifactregistry.googleapis.com \
  secretmanager.googleapis.com \
  --project "$PROJECT"

if ! gcloud secrets describe "$SECRET" --project "$PROJECT" >/dev/null 2>&1; then
  echo "Creating Secret Manager secret ${SECRET}"
  key="base64:$(openssl rand -base64 32)"
  printf '%s' "$key" | gcloud secrets create "$SECRET" \
    --project "$PROJECT" \
    --replication-policy="automatic" \
    --data-file=-
else
  echo "Using existing secret ${SECRET}"
fi

project_number="$(gcloud projects describe "$PROJECT" --format='value(projectNumber)')"
runtime_sa="${RUNTIME_SA:-${project_number}-compute@developer.gserviceaccount.com}"

echo "Granting roles/secretmanager.secretAccessor to ${runtime_sa}"
gcloud secrets add-iam-policy-binding "$SECRET" \
  --project "$PROJECT" \
  --member="serviceAccount:${runtime_sa}" \
  --role="roles/secretmanager.secretAccessor" \
  --quiet >/dev/null

echo "Deploying ${SERVICE} in ${REGION}"
gcloud run deploy "$SERVICE" \
  --source . \
  --project "$PROJECT" \
  --region "$REGION" \
  --service-account "$runtime_sa" \
  --allow-unauthenticated \
  --ingress=all \
  --port 8080 \
  --memory "$MEMORY" \
  --cpu "$CPU" \
  --concurrency "$CONCURRENCY" \
  --timeout 60 \
  --min-instances "$MIN_INSTANCES" \
  --max-instances "$MAX_INSTANCES" \
  --cpu-boost \
  --set-env-vars "APP_ENV=production,APP_DEBUG=false,DEMOS_ENABLED=true,LOG_CHANNEL=stderr,LOG_LEVEL=info,SESSION_DRIVER=cookie,SESSION_SECURE_COOKIE=true" \
  --set-secrets "APP_KEY=${SECRET}:latest" \
  --startup-probe="initialDelaySeconds=0,timeoutSeconds=2,periodSeconds=5,failureThreshold=24,httpGet.path=/health,httpGet.port=8080" \
  --liveness-probe="timeoutSeconds=2,periodSeconds=30,failureThreshold=3,httpGet.path=/health,httpGet.port=8080" \
  --readiness-probe="timeoutSeconds=2,periodSeconds=5,failureThreshold=3,httpGet.path=/health,httpGet.port=8080"

url="$(gcloud run services describe "$SERVICE" --project "$PROJECT" --region "$REGION" --format='value(status.url)')"
echo "Deployed ${url}"
echo "Check ${url}/health"
