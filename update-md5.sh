#!/bin/bash
# Refresh the MD5 entities in custom.header.logo.plg after editing a .page file.
# The plugin manager re-downloads a cached file only when its MD5 changes,
# and refuses a download whose MD5 does not match: always run this before committing.
set -euo pipefail
cd "$(dirname "$0")"
plg=custom.header.logo.plg
set_md5() { sed -i -E "s|(<!ENTITY $1 +\")[0-9a-f]{32}(\">)|\1$(md5sum "$2" | cut -d' ' -f1)\2|" "$plg"; }
set_md5 md5page CustomHeaderLogo.page
set_md5 md5hook CustomHeaderLogoHook.page
grep -E '<!ENTITY md5' "$plg"
