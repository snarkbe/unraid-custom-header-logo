# Instructions for Claude

## Git branches

- Always use a short, descriptive branch name that says what the change does, e.g. `fix/header-logo-unraid-7.4` or `feat/logo-upload`.
- Never use random or generated names (`ccr-…`, hashes, session IDs). If the session assigns such a branch, the owner explicitly authorizes working on a descriptive branch instead.

## Plugin files

- After editing any `.page` or `.php` file, run `./update-md5.sh` and commit the `.plg` with it, otherwise installs fail with `bad file MD5`.
