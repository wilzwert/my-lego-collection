# my-lego-collection

[![Backend CI](https://img.shields.io/github/actions/workflow/status/wilzwert/my-lego-collection/ci_backend.yml?label=Backend%20CI&logo=Github)](https://github.com/wilzwert/my-lego-collection/actions/workflows/ci_backend.yml)

[![Backend coverage](https://img.shields.io/codecov/c/github/wilzwert/my-lego-collection/main?flag=backend&label=Backend%20coverage&logo=PHP)](https://wilzwert.github.io/my-lego-collection/backend-coverage/)
[![Backend Quality Gate Status](https://img.shields.io/sonar/quality_gate/my-lego-collection-backend?server=https%3A%2F%2Fsonarcloud.io&logo=sonarcloud&label=Backend%20quality%20gate)](https://sonarcloud.io/summary/new_code?id=my-lego-collection-backend)

[Backend coverage report](https://wilzwert.github.io/my-lego-collection/backend-coverage/)

## Overview

### Goals

This project has several goals :
1. help me manage my Lego collection (handling sets and their current state, parts/elements and especially spare/missing elements...)
2. attempt to implement a vertical slice architecture, with event-driven communication between slices
3. Try, fail, misunderstand, try again, fail again, succeed, do better, wish I knew more and better, refactor, unlearn, relearn, fail again, succeed, question everything, go to sleep
4. Learn about React or Vue (later, for now I'm focusing on the backend API)

### Features (for now...)

Backend :
- fetch sets and parts from Rebrickable API with Redis caching
- user registration
- authentication (JWT and refresh tokens in cookies)
- quality control tools (coverage, linting, static analysis)

## Roadmap
  Backend :
- shift to TDD or at least better testing strategy
- add sets and parts to the user's collection
- browse user's collection (view sets, search for part/elements)

Frontend :
- everything ;)

## Usage

### Docker for dev backend

You can use the docker/dev/docker-compose.yml to provide
- a Caddy / FrankenPHP server (with XDebug)
- a PostgreSQL server
- a Redis cache server

## Testing

### Backend

Your can run tests (unit and integration) in your docker dev container :

`cd back`

To run tests with code coverage, HTML and XML reports, minimum coverage check (80%) :  
`composer test:full`

To execute tests with code coverage and HTML report :  
  `XDEBUG_MODE=coverage vendor/bin/phpunit`

To execute tests without coverage :
 `vendor/bin/phpunit --no-coverage`

There are 2 tests suites : 'Unit' and 'Integration'. You can use the `--testsuites` command line option to select one.

You may also execute tests in your IDE but this requires a bit of configuration (at least in PHPStorm). 

## Quality

### Backend

Run PHPStan in your docker container.

`cd back`

`vendor/bin/phpstan`