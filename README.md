# Remote configuration backend

Host this folder on GitHub (e.g. `https://raw.githubusercontent.com/USER/REPO/BRANCH/backend/`).

Update `REMOTE_CONFIG_BASE_URL` in `app/build.gradle.kts` (`buildConfigField`) to match your raw URL (must end with `/`).

## Files

| File | Purpose |
|------|---------|
| `config.json` | Master config: schema version, relative paths to other JSON files, feature flags, maintenance mode. |
| `updates.json` | App version checks: `latest_version`, `force_update`, `download_url`, messages. |
| `ads.json` | Global ads: banners, native slots, interstitial rules. |
| `news_ads.json` | Reader-focused placements (inline between pages, reader banners). |

Edit JSON manually or automate via CI. Keep `schema_version` when making breaking changes.
