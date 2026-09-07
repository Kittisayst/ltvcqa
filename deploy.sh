#!/usr/bin/env bash
#
# deploy.sh — deploy ltvcqa ຂຶ້ນ server QA (Windows Server 2012 R2 + Laragon)
#
# ວິທີໃຊ້ (run ຢູ່ server ຜ່ານ SSH):
#   ssh Administrator@192.168.100.32
#   "D:\laragon\bin\git\bin\bash.exe" E:/qa/ltvcqa/deploy.sh
#
# ຫຼື ຄຳສັ່ງດຽວຈາກເຄື່ອງ dev:
#   ssh Administrator@192.168.100.32 "\"D:\laragon\bin\git\bin\bash.exe\" E:/qa/ltvcqa/deploy.sh"
#
# ໝາຍເຫດ: asset (public/build) ຖືກ commit ໄວ້ໃນ git ຢູ່ແລ້ວ — ໂດຍ default
# deploy ຈະ "ບໍ່" run npm. build asset ຢູ່ເຄື່ອງ dev ແລ້ວ commit + push ມາ.
# ຖ້າຢາກ build ຢູ່ server ໃຫ້ຕັ້ງ RUN_NPM=1 (ໃຊ້ Node 22 ຂອງ Laragon).
#
# ຕົວເລືອກ (env var):
#   BRANCH=master        branch ທີ່ຈະ deploy
#   RUN_NPM=1            build asset ຢູ່ server (default: ຂ້າມ)
#   SKIP_MIGRATE=1       ຂ້າມ php artisan migrate
#   NO_MAINTENANCE=1     ບໍ່ເປີດ maintenance mode ຕອນ deploy

set -Eeuo pipefail

# ---------- config ----------
APP_DIR="${APP_DIR:-E:/qa/ltvcqa}"
BRANCH="${BRANCH:-master}"
PHP_BIN="${PHP_BIN:-D:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe}"
COMPOSER_PHAR="${COMPOSER_PHAR:-D:/laragon/bin/composer/composer.phar}"
NODE_DIR="${NODE_DIR:-D:/laragon/bin/nodejs/node-v22}"
# ----------------------------

log()  { printf '\n\033[1;36m==> %s\033[0m\n' "$*"; }
ok()   { printf '\033[1;32m    OK %s\033[0m\n' "$*"; }
die()  { printf '\n\033[1;31m!! %s\033[0m\n' "$*" >&2; exit 1; }

cd "$APP_DIR" || die "ບໍ່ພົບ folder $APP_DIR"

command -v git >/dev/null || die "ບໍ່ພົບ git ໃນ PATH"
[ -f "$PHP_BIN" ] || PHP_BIN="php"
command -v "$PHP_BIN" >/dev/null || [ -f "$PHP_BIN" ] || die "ບໍ່ພົບ PHP"

PREV_COMMIT="$(git rev-parse --short HEAD)"
log "ເລີ່ມ deploy — commit ປັດຈຸບັນ: $PREV_COMMIT (branch: $BRANCH)"

# ---------- maintenance mode ----------
MAINT_ON=0
if [ "${NO_MAINTENANCE:-0}" != "1" ]; then
  log "ເປີດ maintenance mode"
  "$PHP_BIN" artisan down --retry=15 || true
  MAINT_ON=1
fi

bring_back_up() {
  [ "$MAINT_ON" = "1" ] && { "$PHP_BIN" artisan up || true; } || true
}
rollback() {
  printf '\n\033[1;31m!! deploy ລົ້ມເຫລວ — rollback ກັບໄປ %s\033[0m\n' "$PREV_COMMIT" >&2
  git reset --hard "$PREV_COMMIT" || true
  "$PHP_BIN" artisan config:cache || true
  bring_back_up
}
trap 'rollback' ERR

# ---------- pull code ----------
log "ດຶງ code ລ່າສຸດຈາກ origin/$BRANCH"
# stash ສະເພາະ tracked change (ບໍ່ແຕະ untracked file ເຊັ່ນ deploy.sh ເອງ)
if [ -n "$(git status --porcelain --untracked-files=no)" ]; then
  git stash push -m "deploy.sh auto-stash $(date +%Y%m%d-%H%M%S)"
  ok "stash tracked change ໄວ້ແລ້ວ"
fi
git fetch --prune origin
git checkout "$BRANCH"
git reset --hard "origin/$BRANCH"
NEW_COMMIT="$(git rev-parse --short HEAD)"
ok "ອັບເດດເປັນ commit $NEW_COMMIT"

# ---------- php deps ----------
log "ຕິດຕັ້ງ composer dependencies (production)"
"$PHP_BIN" "$COMPOSER_PHAR" install --no-dev --optimize-autoloader --no-interaction --prefer-dist
ok "composer install ແລ້ວ"

# ---------- frontend (opt-in) ----------
if [ "${RUN_NPM:-0}" = "1" ]; then
  log "build frontend ຢູ່ server (Node 22)"
  [ -d "$NODE_DIR" ] && PATH="$NODE_DIR:$PATH"
  command -v npm >/dev/null || die "ບໍ່ພົບ npm (ກວດ NODE_DIR=$NODE_DIR)"
  echo "    node $(node -v) / npm $(npm -v)"
  # ລ້າງ node_modules ກັນ bug native binding ຂອງ npm optional deps
  rm -rf node_modules
  npm install --no-audit --no-fund
  npm run build
  ok "build ແລ້ວ"
else
  log "ຂ້າມ npm — ໃຊ້ asset ຈາກ git (public/build). ຕັ້ງ RUN_NPM=1 ຖ້າຢາກ build ຢູ່ server"
fi

# ---------- migrate ----------
if [ "${SKIP_MIGRATE:-0}" != "1" ]; then
  log "run database migration"
  "$PHP_BIN" artisan migrate --force
  ok "migrate ແລ້ວ"
else
  log "ຂ້າມ migrate (SKIP_MIGRATE=1)"
fi

# ---------- optimize / cache ----------
log "ລ້າງ + ສ້າງ cache ໃໝ່"
"$PHP_BIN" artisan storage:link 2>/dev/null || true
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache
"$PHP_BIN" artisan event:cache 2>/dev/null || true
ok "cache ພ້ອມ"

# ---------- restart workers ----------
log "restart queue workers (ຖ້າມີ)"
"$PHP_BIN" artisan queue:restart || true

# ---------- done ----------
trap - ERR
bring_back_up
log "deploy ສຳເລັດ: $PREV_COMMIT -> $NEW_COMMIT"
git log --oneline -1
