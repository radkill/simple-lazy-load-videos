# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A WordPress plugin ("Simple Lazy Load Videos", published on wordpress.org) that replaces embedded YouTube/Vimeo iframes with a preview image + play button; the real iframe is only created by JS on click. Supports PHP 5.6+ and WordPress 4.9+, so avoid newer PHP syntax (no typed properties, arrow functions, `??=`, etc.) and newer WP APIs without guards. Code follows WordPress Coding Standards (WPCS); see Coding style below.

## Build (front-end assets)

All tooling lives in `dev/` (Grunt via `load-grunt-config`; each task config is a file in `dev/grunt/`, task aliases in `dev/grunt/aliases.js`). Run from `dev/`:

- `npm install` — install dev dependencies (needs global `grunt-cli`)
- `grunt` (or `grunt prod`) — production build: clean, imagemin, sass+postcss, jshint+jscs, concat+terser
- `grunt dev` — dev build (images copied, JS not minified)
- `grunt js` / `grunt css` — run just one pipeline; `grunt jshint jscs` is the only JS lint step
- `npm start` / `npm run start-dev` — build then watch (Windows `call` syntax; also `_start.cmd` / `_dev.cmd`)

Sources → outputs:
- `dev/src/sass/main.scss` → `assets/css/main.full.css` + `main.min.css` (the plugin enqueues `main.min.css`)
- `dev/src/js/**` → concatenated into `assets/js/scripts.js`
- `dev/src/img/*.svg` → `assets/img/` (play button SVGs, inlined into markup by `Functions::get_svg()`)

`assets/` is gitignored — never edit files there directly; edit `dev/src/` and rebuild. There is no PHP test suite or PHP lint config in the repo.

## Architecture

Entry point `simple-lazy-load-videos.php` defines `SLLV_*` constants, requires every class in `includes/`, and instantiates `\SLLV\Plugin` on `plugins_loaded`. All classes are in the `SLLV` namespace, wrapped in `class_exists` guards, and mostly use static methods.

Render flow (PHP):
1. Hooks in `Plugin`: `embed_oembed_html` (core/Gutenberg embeds; frontend only, plus admin-ajax requests except the editor's `parse-embed` preview), `bp_embed_oembed_html` (BuddyPress), and the `[sllv_video]` shortcode (attrs `thumbnail`, `play`, `hide_play`).
2. All three call `Template::get_html_from_url()`, which uses `Functions::determine_video_url()` to parse the URL into `type`/`id`/`hash` (unlisted Vimeo)/`start`, resolves a thumbnail (`get_youtube_thumb` checks size existence with fallback to hqdefault, prefers WEBP; `get_vimeo_thumb` hits the Vimeo API), and renders the `.sllv-video` container with `data-provider`/`data-video`/`data-hash`/`data-start` attributes. Returns `false` for non-video URLs so the original HTML is kept.
3. Remote HTTP calls are cached in transients via `Functions::remote_api_get()` / `check_thumbnail_exists()` (failures cached too, shorter TTL).

Frontend JS (`dev/src/js/main.js`, vanilla, no jQuery): finds `.sllv-video` elements, on click builds the provider embed URL from the data attributes and appends an iframe; a debounced `MutationObserver` picks up videos added later (AJAX/infinite scroll); starting one video/media pauses or removes others. The PHP markup and JS data-attribute contract must stay in sync.

Settings: single option `sllv_settings` (thumbnail sizes) — defaults and `get_settings()` in `Plugin`, settings page and whitelist sanitization in `Options` (admin only). `sllv_version` option drives upgrade routines in `Plugin::check_version()` (run on `admin_init`). `Actions::uninstall` deletes both options. `Oembed_Cache::flush_old_cache()` is a legacy upgrade step (clears oEmbed caches from versions ≤ 0.9.0).

Assets: `Resources` loads the text domain and enqueues `assets/js/scripts.js` / `assets/css/main.min.css` on the frontend.

Public filters (keep backward compatible): `sllv_youtube_button`, `sllv_vimeo_button`, `sllv_video_template`.

## Coding style

All PHP follows WordPress Coding Standards (WPCS):

- Indent with tabs, not spaces.
- `snake_case` for functions, methods, variables and array keys; `Capitalized_Words_With_Underscores` for classes (e.g. `Oembed_Cache`); `UPPER_CASE` for constants (prefixed `SLLV_`); class files are named `class-{name}.php`.
- Yoda conditions: `if ( 'youtube' === $type )`, constant/literal on the left; always strict comparison (`===`, `!==`).
- Long array syntax `array()`, never `[]`.
- Spaces inside parentheses and around operators: `foo( $a, $b )`, `! $value`.
- Escape all output (`esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses()`), sanitize all input; translatable strings use the `simple-lazy-load-videos` text domain.
- Every file, class, method and function has a full docblock (`@since`, `@param`, `@return`).
- All comments, docblocks, commit messages and user-facing strings are in English.

## Conventions

- New/changed functions in unreleased work get `@since X.X.X`; it is replaced with the real number at release.
- Release ("Go to x.y.z") bumps the version in: plugin header + `SLLV_VERSION` in `simple-lazy-load-videos.php`, `Stable tag` + changelog in `readme.txt`, changelog in `README.md` and `CHANGELOG.md`, `dev/package.json` (+ lock), and the `@since X.X.X` placeholders. The POT file `languages/simple-lazy-load-videos.pot` is regenerated separately (text domain `simple-lazy-load-videos`).
- `README.md` (GitHub) and `readme.txt` (wordpress.org format) carry the same FAQ/changelog content — update both.
