# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

Full project guidance (commands, theme vs `skduo-core` split, deploy/versioning) lives in the site root `CLAUDE.md` (`~/Local Sites/skduo-laravel/CLAUDE.md`).

Theme specific reminders:
- CPTs, data helpers and REST API are in the `skduo-core` plugin, not in `inc/`. `inc/helpers.php` only has presentation helpers plus fallback stubs.
- `inc/*.php` is auto-loaded by `functions.php`; new admin screens go there as submenus of "Page options" (`inc/home-hero.php`).
- Heavy JS is dynamically imported in `resources/js/app.js`, gated on a data attribute.
