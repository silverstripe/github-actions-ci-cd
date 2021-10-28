# Silverstripe GitHub Actions shared CI config

By default will run Silverstripe Unit tests and PHPCS code linting.

It's highly recommended that you use a tagged version (e.g. 0.1.2) to ensure stability of your builds. If you have a relatively simple build that you have no intention of ever making more complex e.g. only phpunit tests using phpunit.xml.dist, then this is probably all you need for long term use.

This repository is currently in development and code on the `main` branch could change at any time, including taking on a whole new direction. It's expected that new functionality will be added.

### Usage

Create the following file in your module

(subsitute the tagged version for the most recent tag from this module)

*.github/workflows/main.yml*
```
name: Module CI

on:
  push:
  pull_request:

jobs:
  ci:
    uses: silverstripe/github-actions-ci-cd/.github/workflows/ci.yml@0.1.5
```

Use the following if your module does not have a `phpcs.xml.dist` file

(or better still, copy paste this [sample phpcs.xml.dist](https://raw.githubusercontent.com/silverstripe/silverstripe-elemental/4/phpcs.xml.dist) file in to your module)


```
jobs:
  ci:
    uses: silverstripe/github-actions-ci-cd/.github/workflows/ci.yml@0.1.5
    with:
      run_phplinting: false
```

#### Some other "with" options

Run php coverage
`run_phpcoverage: true`

Run behat tests:
`run_endtoend: true`

Run js tests, linting and build diff:
`run_js: true`

Don't run phpunit tests
`run_phpunit: false`

Don't run php linting (phpcs, phpstan)
`run_phplinting: false`
