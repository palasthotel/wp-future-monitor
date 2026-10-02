# Contributing

## Branching

`main` is the default branch and always reflects what is released (or about to
be released). Work on a feature branch and open a pull request against `main`.

## Commit messages

Releases and the changelog are generated from the commit history, so commit
messages follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>[optional scope][!]: <description>

[optional body]

[optional footer]
```

| Type | Effect on the version | Appears in changelog |
|---|---|---|
| `fix:` | patch (1.0.3 → 1.0.4) | yes, "Bug Fixes" |
| `feat:` | minor (1.0.3 → 1.1.0) | yes, "Features" |
| `feat!:` or `BREAKING CHANGE:` footer | major (1.0.3 → 2.0.0) | yes, highlighted |
| `docs:`, `refactor:`, `chore:`, `deps:`, `style:`, `test:`, `ci:` | none | no |

Examples:

```
fix: keep pagination working for custom taxonomies
feat: allow overriding archives per language
feat!: drop support for PHP 7.4

BREAKING CHANGE: requires PHP 8.0 or newer
```

A pull request that should trigger a release needs at least one `fix:` or
`feat:` commit. When squash-merging, make sure the squash commit message itself
is a conventional commit — that is the message release-please reads.

### Which changes get `fix:` or `feat:`

Only changes that matter to someone using the plugin. `fix:` and `feat:` decide
the version *and* write the line that ends up in the changelog on the
wordpress.org plugin page, so the question to ask before committing is whether a
user of the plugin would care about that line.

Everything else takes a type that releases nothing — workflows and CI, release
tooling, repository documentation, internal refactoring, and anything touching
files that are not shipped. As a rule of thumb, a change confined to files
outside `public/` is almost never a `fix:`.

That includes hardening. Blocking direct access to a file that is not part of the
download is `chore:`, not `fix:` — nothing changes for anyone who installed the
plugin.

## Local setup

There is nothing to build. wp-env runs without a configuration file and mounts the
repository as the plugin; `Plugin.php` in the root is the development wrapper that loads
`public/`:

```sh
npx @wordpress/env start      # http://localhost:8888, admin / password
```

To see the ⚠️ verdict, schedule a post and remove its core event - the plugin's hourly
check is rescheduled on every admin request, so the dashboard shows ⚠️ rather than 🚨:

```sh
npx @wordpress/env run cli wp cron event delete publish_future_post
```

The main file `public/Plugin.php` must keep its name. WordPress identifies an installed
plugin by `<directory>/<main file>` and stores that pair in `active_plugins`; renaming it
deactivates the plugin on every site at the next update.

## Versions

Never edit version numbers by hand. `version.txt`, `CHANGELOG.md`,
`public/Plugin.php` and the `Stable tag:` in `public/readme.txt` are all
maintained by the release pipeline — see
[.github/WORKFLOWS.md](.github/WORKFLOWS.md).

Content changes to `public/readme.txt` (description, FAQ, screenshots,
tested-up-to) are of course done by hand; just leave `Stable tag:` and the
`== Changelog ==` entries alone.

## Checks

Every PR runs `php -l` against PHP 8.0, 8.2, 8.3 and 8.4, packs the plugin and checks
the payload (autoloader and translations present, no symlinks, no development wrapper),
and checks that the version carriers agree. The plugin declares
`Requires PHP: 8.0` — it uses typed and nullable properties — and
`Requires at least: 4.0` (WordPress).

Packing needs `composer`: the payload gets a `--no-dev` autoloader, and the composer
files are dropped from it rather than shipped.

The `de_CH` translations in `public/languages` are symlinks to the `de_DE` files, so
Swiss German is not maintained twice. Keep them that way and let `rsync -rL` resolve
them — a symlink does not survive the zip wordpress.org builds. See the note in the
[README](README.md).
