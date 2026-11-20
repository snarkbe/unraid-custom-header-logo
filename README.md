# Custom Header Logo

An [Unraid](https://unraid.net) plugin that replaces the stock Unraid logo in the top banner with your own image and lets you configure the URL it links to.

No Unraid OS files are patched — the plugin uses the webGUI `Buttons` page hook, so it survives OS updates.

## Features

- Replace the Unraid banner logo with any PNG or SVG image (URL or flash-hosted)
- Configure the URL the logo links to (defaults to `/Dashboard`)
- Adjustable logo height (default 28 px, matching the stock logo)
- Optional open-in-new-tab behaviour
- Keeps the native Unraid version and update indicator next to the logo
- Enable/disable without uninstalling

## Install

1. In Unraid, go to **Plugins → Install Plugin**
2. Paste the following URL and click **Install**:

```
https://raw.githubusercontent.com/snarkbe/unraid-custom-header-logo/refs/heads/main/custom.header.logo.plg
```

## Configuration

After installing, navigate to **Settings → User Preferences → Custom Header Logo**.

| Setting | Description |
|---|---|
| Enable custom logo | Turn the replacement on or off |
| Logo image URL | Full URL or flash path to your image (PNG/SVG recommended) |
| Logo links to | URL opened when clicking the logo (empty = Dashboard) |
| Logo height | Display height in pixels (default 28) |
| Open link in new tab | Opens the link URL in a new browser tab |

**Suggested image size:** 320 × 56 px with a transparent background, displayed at 160 × 28 px (≈ 5.7:1 ratio).

## How it works

- Settings are stored at `/boot/config/plugins/custom.header.logo/custom.header.logo.cfg` (persists on the flash drive).
- A hidden `Menu="Buttons"` page (`CustomHeaderLogoHook.page`) is evaluated inside `<head>` on every webGUI page. When enabled it hides the stock `<a href="https://unraid.net">` logo link in the `#header` banner and inserts your `<a><img></a>` in the same spot. A `MutationObserver` re-applies the swap when Unraid's Vue header mounts or re-renders.
- This works across header layouts: 7.0–7.1 (`.logo` block), 7.2–7.3 (`<unraid-header-os-version>`) and 7.4+ (`<unraid-header>`). If the header ever renders inside a shadow root, the plugin hides the whole component and shows its own version label instead.
- The page files are cached on the flash drive (MD5-checked), so the plugin installs at boot without network access, and updates replace every installed file.
- Nothing outside the plugin folder under `/usr/local/emhttp` is modified.

## Requirements

- Unraid 7.0.0 or newer

## Development

The `.plg` pins the MD5 of each downloaded `.page` file. After editing one, run:

```
./update-md5.sh
```

and commit the `.plg` together with the page, otherwise installs fail with `bad file MD5`.

## Author

Gilles Reichert
