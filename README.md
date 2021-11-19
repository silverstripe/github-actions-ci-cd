# Silverstripe GitHub Actions shared CI/CD config

Will use feature detection based on files in the root folder such as phpunit.xml.dist to build dynmaic matrix of tests to run

It's highly recommended that you use a tagged version (e.g. 0.2.0) to ensure stability of your builds. If you have a relatively simple build that you have no intention of ever making more complex e.g. only phpunit tests using phpunit.xml.dist, then this is probably all you need for long term use.

This repository is currently in development and code on the `main` branch could change at any time, including taking on a whole new direction. It's expected that new functionality will be added.

### CI Usage

Create the following file in your module

(subsitute the tagged version for the most recent tag from this module)

*.github/workflows/main.yml*
```
name: Module CI

on:
  push:
  pull_request:
  workflow_dispatch:

jobs:
  ci:
    uses: silverstripe/github-actions-ci-cd/.github/workflows/ci.yml@0.2.0
```

Use the following if your module does not have a `phpcs.xml.dist` file

(or better still, copy paste this [sample phpcs.xml.dist](https://raw.githubusercontent.com/silverstripe/silverstripe-elemental/4/phpcs.xml.dist) file in to your module)

```
jobs:
  ci:
    uses: silverstripe/github-actions-ci-cd/.github/workflows/ci.yml@0.2.0
    with:
      run_phplinting: false
```

#### Some other "with" options

Extra composer requirements
You do not need to quote the string for multiple requirements
`composer_require_extra: silverstripe/widgets:^2 silverstripe/comments:^3`

Simple matrix - php 7.4 with mysql 5.7 only
`simple_matrix: true`

Enable php coverage (codecov - no feature detection)
Modules on the silverstripe account will automaticaly have this enabled
`run_phpcoverage: true`

Disable end-to-end tests (behat.yml):
`run_endtoend: false`

Disable JS tests (package.json - yarn lint, test and build diff):
`run_js: false`

Disable phpunit tests (phpunit.xml.dist / phpunit.xml)
`run_phpunit: false`

Disable php linting (phpcs.xml.dist, phpstan.neon.dist)
`run_phplinting: false`

Extra jobs
Define php version and/or db
Omit the 'run_' prefix
```
extra_jobs: |
  - php: '8.0'
    endtoend: true
```

### Update JS dependencies

This workflow will automatically run `yarn upgrade` to update js dependencies and create a pull-request authored by a github-actions user. Non-admin modules will have the admin module installed in a sibling directory

The cron will automatically run using the modules default branch on the first day of every 3rd month - Jan, Apr, Jul, Oct. The action can also be triggered manually.

```
name: Update JS deps

on:
  cron: '0 0 1 */3 *'
  workflow_dispatch:

jobs:
  ci:
    uses: silverstripe/github-actions-ci-cd/.github/workflows/js-upgrade.yml@0.2.0
```
