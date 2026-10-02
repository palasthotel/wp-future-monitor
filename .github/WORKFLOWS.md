# CI/CD Workflows

The four workflows in `.github/workflows/` call the shared ones in
[palasthotel/github-workflows](https://github.com/palasthotel/github-workflows). How
they work, every input and what to do when a deploy fails is described there, in
[docs/wp-plugin.md](https://github.com/palasthotel/github-workflows/blob/main/docs/wp-plugin.md).

What is specific to this plugin:

| | |
|---|---|
| wordpress.org slug | `future-monitor` |
| version file | `version.txt` (`release-type: simple`) - keep it, release-please and the scripts read the version there |
| build step | none |
| composer | `public/composer.json` only provides the autoloader; the pack installs it with `--no-dev --optimize` and drops the composer files from the payload |
| PHP | `php -l` runs on 8.0, 8.2, 8.3 and 8.4 - the plugin declares `Requires PHP: 8.0` |
| SVN | up to 1.0.2 the trunk held the Swiss translations as symlinks; since 1.0.3 they are real files, resolved by `rsync -rL` |

The organisation-wide variable `RELEASE_BOT_APP_ID` and the secrets
`RELEASE_BOT_PRIVATE_KEY`, `SVN_USERNAME` and `SVN_PASSWORD` have to be available to
this repository. The repository variable `SVN_REPO_URL` is no longer used and can be
deleted.
