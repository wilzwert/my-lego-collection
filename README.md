# my-lego-collection

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

## Roadmap
Backend :
- shift to TDD or at least better testing strategy
- setup quality control tools (coverage, linting, static analysis)
- add sets and parts to the user's collection
- browse user's collection (view sets, search for part/elements)

Frontend :
- everything ;)

## Usage

### Docker for dev

You can use the docker/dev/docker-compose.yml to provide
- a Caddy / FrankenPHP server (with XDebug)
- a PostgreSQL server
- a Redis cache server

## Testing

### Backend


`cd back`

To execute tests with code coverage and HTML report :  
  `XDEBUG_MODE=coverage vendor/bin/phpunit`

To execute tests without coverage :
 `vendor/bin/phpunit --no-coverage`

  There are 2 tests suites : 'Unit' and 'Integration'. You can use the `--testsuites` command line option to select one.