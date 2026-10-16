#!/usr/bin/env bash
# Verify required plugin files exist before WordPress.org SVN deploy.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

REQUIRED=(
	"viable-url-media-uploader.php"
	"readme.txt"
	"includes/class-viable-url-media-uploader.php"
	"includes/class-vumu-svg-sanitizer.php"
	"includes/class-vumu-svg-support.php"
	"includes/class-vumu-url-handler.php"
	"includes/class-vumu-upload-handler.php"
	"includes/upload-page.php"
)

missing=0
for file in "${REQUIRED[@]}"; do
	if [[ ! -f "$file" ]]; then
		echo "MISSING: $file"
		missing=1
	fi
done

if [[ "$missing" -ne 0 ]]; then
	echo "Release verification failed."
	exit 1
fi

echo "Release verification passed (${#REQUIRED[@]} required files present)."
exit 0
