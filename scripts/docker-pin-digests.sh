#!/usr/bin/env bash
# Resolve current Hub digests for PDV base images into docker/images.lock
# and print Dockerfile/Compose snippets. Run from projects/pdv.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
LOCK="${ROOT}/docker/images.lock"

IMAGES=(
  "MYSQL_IMAGE=mysql:8.4"
  "REDIS_IMAGE=redis:7-alpine"
  "PHP_IMAGE=php:8.3-fpm-bookworm"
  "COMPOSER_IMAGE=composer:2"
  "NODE_IMAGE=node:22-alpine"
  "NGINX_IMAGE=nginx:1.27-alpine"
)

digest_for() {
  local ref="$1"
  local dig
  dig="$(docker buildx imagetools inspect "$ref" --format '{{.Manifest.Digest}}' 2>/dev/null || true)"
  if [[ -z "$dig" || "$dig" == "<nil>" ]]; then
    docker pull "$ref" >/dev/null
    dig="$(docker buildx imagetools inspect "$ref" --format '{{.Manifest.Digest}}')"
  fi
  printf '%s' "$dig"
}

{
  echo "# Pinned base images for PDV Docker builds (tag@digest)."
  echo "# Regenerate: bash scripts/docker-pin-digests.sh"
  echo "# Generated: $(date -u +%Y-%m-%d)"
  echo
  for entry in "${IMAGES[@]}"; do
    key="${entry%%=*}"
    ref="${entry#*=}"
    dig="$(digest_for "$ref")"
    echo "${key}=${ref}@${dig}"
    echo "  pinned ${ref}@${dig}" >&2
  done
} >"${LOCK}"

echo "Wrote ${LOCK}" >&2
echo "Update Dockerfiles/compose FROM/image lines to match, then rebuild." >&2
