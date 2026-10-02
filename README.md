# Future Monitor (WordPress-Plugin)

Tells you whether your scheduled posts will actually publish — and publishes them
if WordPress forgot to.

- **WordPress.org:** https://wordpress.org/plugins/future-monitor/
- **User documentation:** [public/readme.txt](public/readme.txt) (the text shown on WordPress.org)
- **Changelog:** [CHANGELOG.md](CHANGELOG.md) — release-please owns that file, so do
  not add notes to it by hand. Entries up to 1.0.2 are in the `== Changelog ==`
  section of [public/readme.txt](public/readme.txt).

## The problem it solves

WordPress schedules each future post with its own cron event: one
`publish_future_post` entry per post ID. If that entry is lost — a plugin clearing
the cron array, a failed write to the `cron` option, a restored database dump — the
post silently stays in `future` forever. This is the "missed schedule" problem, and
core gives you no way to see it: the *Activity* dashboard widget lists scheduled
posts but never checks whether their cron events still exist.

## What it does

A dashboard widget lists every scheduled post with a verdict:

| | Meaning |
|---|---|
| ✅ | a `publish_future_post` event exists — core will publish it |
| ⚠️ | no core event, but this plugin's hourly job watches the post and will publish it |
| 🚨 | neither — the post will never publish. Open and resave it |

The verdicts come from comparing the `cron` option against the posts actually in
`future` status. Alongside that, an hourly job publishes any `future` post whose
date has passed, as a safety net.

**Know the limit of that safety net.** It is itself a cron event. If WP-Cron is
broken as a whole — the failure this plugin diagnoses — the safety net does not run
either. It helps when *individual* post events went missing while cron still works.
The hourly job is also only (re)scheduled on `admin_init`, so if it is lost it
stays lost until someone opens the admin.

The widget requires `edit_posts`, and each post is only listed to users who may
edit that post.

## Repository layout

`public/` is exactly what ships to WordPress.org. Everything outside it is
repository-only.

| Path | Description |
|---|---|
| `public/Plugin.php` | plugin header and bootstrap |
| `public/classes/` | `DashboardWidget`, `Schedule`, `Store` and the shared component base |
| `public/languages/` | translations (`de_DE`, `de_CH`, `de_CH_informal` + `.pot`) |
| `public/readme.txt` | WordPress.org plugin page |
| `public/LICENSE` | GPL-3.0 text, shipped with the plugin |
| `assets/` | media for the WordPress.org plugin page — not part of the download |
| `Plugin.php` | DEV wrapper, loads `public/Plugin.php` when the repository is checked out into `wp-content/plugins/` |
| `.github/workflows/` | CI/CD — see [.github/WORKFLOWS.md](.github/WORKFLOWS.md) |

### The translation symlinks

`public/languages` keeps the `de_CH` and `de_CH_informal` files as symlinks to the
`de_DE` files, so Swiss German does not have to be maintained twice. The shared pack
script resolves them with `rsync -rL`, because a symlink does not survive the zip
wordpress.org builds — until 1.0.3 those four translations were silently missing from
every download.

## Releasing

Releases are automated with [release-please](https://github.com/googleapis/release-please)
and deployed to the WordPress.org SVN repository. There is nothing to bump by
hand — commit with [conventional commits](https://www.conventionalcommits.org/)
and merge the release PR:

```
fix: …   → patch    feat: …  → minor    feat!: … → major
```

The full pipeline is documented in [.github/WORKFLOWS.md](.github/WORKFLOWS.md),
the commit conventions in [CONTRIBUTING.md](CONTRIBUTING.md).

## Building locally

```sh
SLUG=future-monitor bash ../github-workflows/wp-plugin/bin/pack.sh    # → future-monitor.zip
```

The script lives in [palasthotel/github-workflows](https://github.com/palasthotel/github-workflows),
which has to be checked out next to this repository. It needs `composer`: the packed
payload carries a `--no-dev` autoloader and drops the composer files. The result in
`build/future-monitor/` is byte for byte what the release deploys.

## License

GNU General Public License v3.0 or later — see [LICENSE](LICENSE).
