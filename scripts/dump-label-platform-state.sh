#!/usr/bin/env bash
set -Eeuo pipefail

# Dump the Label Site source tree into grouped, uploadable text files.
#
# Place in <label-site>/scripts/ and run from anywhere:
#   bash scripts/dump-label-platform-state.sh
# Or pass the project root explicitly:
#   bash /path/to/dump-label-platform-state.sh /path/to/label-site
#
# File selection uses Git's ignore rules: tracked files plus untracked,
# non-ignored files. Sensitive filenames and binary files are excluded even
# when tracked. Review the dumps before uploading: no filename filter can
# guarantee that source files contain no hardcoded secrets.

umask 077

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="${1:-$(cd -- "${SCRIPT_DIR}/.." && pwd)}"
ROOT_DIR="$(cd -- "$ROOT_DIR" && pwd)"
OUTPUT_DIR="${ROOT_DIR}/file_dumps"

if [[ ! -f "${ROOT_DIR}/artisan" || ! -f "${ROOT_DIR}/composer.json" ]]; then
    printf 'Error: %s does not look like a Laravel project root.\n' "$ROOT_DIR" >&2
    printf 'Usage: %s [project-root]\n' "$0" >&2
    exit 1
fi

if ! git -C "$ROOT_DIR" rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    printf 'Error: %s is not a Git worktree. Git is required to honor .gitignore.\n' "$ROOT_DIR" >&2
    exit 1
fi

GIT_ROOT="$(git -C "$ROOT_DIR" rev-parse --show-toplevel)"
if [[ "$(cd -- "$GIT_ROOT" && pwd)" != "$ROOT_DIR" ]]; then
    printf 'Error: %s must be the Git repository root.\n' "$ROOT_DIR" >&2
    exit 1
fi

if [[ -L "$OUTPUT_DIR" ]]; then
    printf 'Error: refusing symlinked output directory: %s\n' "$OUTPUT_DIR" >&2
    exit 1
fi
mkdir -p "$OUTPUT_DIR"

DUMP_NAMES=(
    '01-app-backend.txt'
    '02-config-routes-bootstrap.txt'
    '03-database.txt'
    '04-resources-frontend.txt'
    '05-tests-docs-root.txt'
    '07-other-source.txt'
)

for name in "${DUMP_NAMES[@]}"; do
    : > "${OUTPUT_DIR}/${name}"
    printf 'Label Site File Dump\nGenerated: %s\nProject root: %s\nDump: %s\n' \
        "$(date -u +'%Y-%m-%dT%H:%M:%SZ')" "$ROOT_DIR" "$name" \
        >> "${OUTPUT_DIR}/${name}"
done

printf 'path\tbytes\tlines\tsha256\n' > "${OUTPUT_DIR}/manifest.tsv"
printf 'path\treason\n' > "${OUTPUT_DIR}/skipped-nonsecret-files.tsv"

is_excluded_path() {
    local rel="${1,,}" part
    local filename="${rel##*/}"

    # Always skip runtime/build/output trees, even if accidentally committed.
    case "$rel" in
        .git|.git/*|vendor/*|node_modules/*|storage/*|bootstrap/cache/*|\
        public/build/*|public/hot|file_dumps/*|coverage/*|.phpunit.cache/*|\
        .idea/*|.vscode/*)
            return 0 ;;
    esac

    # Refuse environment files and sensitive directories anywhere in the tree.
    local -a parts
    IFS='/' read -r -a parts <<< "$rel"
    for part in "${parts[@]}"; do
        case "$part" in
            .env|.env.*|*.env|.ssh|.aws|.gnupg|.secrets|secrets|\
            credentials|private-keys|private_keys)
                return 0 ;;
        esac
    done

    # File names commonly used for actual credentials, not source code that
    # merely implements token/credential handling.
    case "$filename" in
        *.pem|*.key|*.p12|*.pfx|*.p8|*.jks|*.keystore|*.kdbx|\
        id_rsa|id_dsa|id_ecdsa|id_ed25519|id_*.pub|\
        .npmrc|.pypirc|.netrc|.htpasswd|.git-credentials|\
        auth.json|credentials.json|secrets.json|secret.json|\
        service-account.json|service_account.json|\
        service-account-*.json|service_account_*.json|\
        *-credentials.json|*_credentials.json|\
        *-secrets.json|*_secrets.json|\
        *.sqlite|*.sqlite3|*.db|*.sql|*.sql.gz)
            return 0 ;;
    esac

    return 1
}

looks_like_secret_material() {
    local file="$1"
    # High-confidence content patterns. This is defense in depth, not a
    # complete secret scanner; manually review output before sharing.
    LC_ALL=C grep -aEiq -- \
        '-----BEGIN ([A-Z0-9 ]+ )?PRIVATE KEY-----|"private_key"[[:space:]]*:[[:space:]]*"-----BEGIN|AKIA[0-9A-Z]{16}|github_pat_[A-Za-z0-9_]{20,}|ghp_[A-Za-z0-9]{30,}|sk_live_[A-Za-z0-9]{16,}' \
        "$file"
}

is_text_file() {
    local file="$1" encoding
    if command -v file >/dev/null 2>&1; then
        encoding="$(file -b --mime-encoding -- "$file")"
        [[ "$encoding" != 'binary' && "$encoding" != 'unknown-8bit' ]]
    else
        [[ ! -s "$file" ]] || LC_ALL=C grep -Iq . -- "$file"
    fi
}

dump_for_path() {
    local rel="$1"
    case "$rel" in
        app/*) printf '01-app-backend.txt' ;;
        config/*|routes/*|bootstrap/*) printf '02-config-routes-bootstrap.txt' ;;
        database/*) printf '03-database.txt' ;;
        resources/*|public/*) printf '04-resources-frontend.txt' ;;
        tests/*|docs/*) printf '05-tests-docs-root.txt' ;;
        */*) printf '07-other-source.txt' ;;
        *) printf '05-tests-docs-root.txt' ;;
    esac
}

included=0
excluded=0
skipped_binary=0
skipped_secret_content=0

# -c: tracked files; -o --exclude-standard: non-ignored untracked files.
# NUL delimiters safely handle spaces and unusual file names.
while IFS= read -r -d '' rel; do
    [[ -n "$rel" ]] || continue

    if is_excluded_path "$rel"; then
        ((excluded += 1))
        continue
    fi

    full="${ROOT_DIR}/${rel}"

    # Never follow symlinks: a tracked symlink could point outside this repo.
    if [[ -L "$full" ]]; then
        printf '%s\tsymlink\n' "$rel" >> "${OUTPUT_DIR}/skipped-nonsecret-files.tsv"
        continue
    fi
    [[ -f "$full" && -r "$full" ]] || continue

    if ! is_text_file "$full"; then
        ((skipped_binary += 1))
        printf '%s\tbinary\n' "$rel" >> "${OUTPUT_DIR}/skipped-nonsecret-files.tsv"
        continue
    fi

    if looks_like_secret_material "$full"; then
        ((skipped_secret_content += 1))
        # Do not record sensitive filenames in the manifest or skipped list.
        continue
    fi

    output="${OUTPUT_DIR}/$(dump_for_path "$rel")"
    {
        printf '\n===== BEGIN FILE: %s =====\n\n' "$rel"
        cat -- "$full"
        printf '\n\n===== END FILE: %s =====\n' "$rel"
    } >> "$output"

    bytes="$(wc -c < "$full" | tr -d ' ')"
    lines="$(wc -l < "$full" | tr -d ' ')"
    hash="$(sha256sum -- "$full" | awk '{print $1}')"
    printf '%s\t%s\t%s\t%s\n' "$rel" "$bytes" "$lines" "$hash" >> "${OUTPUT_DIR}/manifest.tsv"
    ((included += 1))
done < <(git -C "$ROOT_DIR" ls-files --cached --others --exclude-standard -z | LC_ALL=C sort -zu)

{
    printf 'Label Site Project Overview\n'
    printf 'Generated: %s\n' "$(date -u +'%Y-%m-%dT%H:%M:%SZ')"
    printf 'Project root: %s\n\n' "$ROOT_DIR"

    printf '== Runtime versions ==\n'
    php -v 2>/dev/null | head -n 2 || true
    composer --version 2>/dev/null || true
    node --version 2>/dev/null || true
    npm --version 2>/dev/null || true

    printf '\n== Source selection ==\n'
    printf 'Included text files: %d\n' "$included"
    printf 'Excluded by path/name: %d\n' "$excluded"
    printf 'Skipped binary files: %d\n' "$skipped_binary"
    printf 'Skipped suspected embedded secrets: %d\n' "$skipped_secret_content"
    printf 'Input: tracked + non-ignored untracked Git files.\n'
    printf 'Note: tracked files ignored by .gitignore still require explicit exclusions.\n'
    printf 'Note: source files must be reviewed for hardcoded credentials before sharing.\n'

    printf '\n== Top-level source items ==\n'
    git -C "$ROOT_DIR" ls-files --cached --others --exclude-standard \
        | awk -F/ '{print $1}' | LC_ALL=C sort -u

    printf '\n== Composer package summary ==\n'
    if command -v composer >/dev/null 2>&1; then
        (cd "$ROOT_DIR" && composer show --locked --direct --no-interaction 2>/dev/null) || true
    fi

    printf '\n== NPM package summary ==\n'
    if command -v npm >/dev/null 2>&1; then
        (cd "$ROOT_DIR" && npm ls --depth=0 2>/dev/null) || true
    fi
} > "${OUTPUT_DIR}/00-project-overview.txt"

{
    printf 'Label Site Git State\n'
    printf 'Generated: %s\n' "$(date -u +'%Y-%m-%dT%H:%M:%SZ')"
    printf 'Project root: %s\n\n' "$ROOT_DIR"
    printf '== Branch ==\n'
    git -C "$ROOT_DIR" branch --show-current || true
    printf '\n== Status ==\n'
    git -C "$ROOT_DIR" status --short --branch || true
    printf '\n== Recent commits ==\n'
    git -C "$ROOT_DIR" log -n 20 --date=iso --pretty=format:'%h %ad %an %s' || true
    printf '\n\n== Changed files ==\n'
    git -C "$ROOT_DIR" diff --name-status || true
    printf '\n== Staged files ==\n'
    git -C "$ROOT_DIR" diff --cached --name-status || true
    printf '\n== Diff statistics ==\n'
    git -C "$ROOT_DIR" diff --stat || true
    git -C "$ROOT_DIR" diff --cached --stat || true
} > "${OUTPUT_DIR}/06-git-state.txt"

printf 'Created Label Site dumps in: %s\n' "$OUTPUT_DIR"
printf 'Included %d text files; excluded %d sensitive/runtime paths; skipped %d binary files and %d files with suspected embedded secrets.\n' \
    "$included" "$excluded" "$skipped_binary" "$skipped_secret_content"
printf '\nFiles:\n'
find "$OUTPUT_DIR" -maxdepth 1 -type f -printf '  %f\n' | LC_ALL=C sort
printf '\nIMPORTANT: Review the outputs for hardcoded secrets before uploading.\n'
