# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

API Platform Core is a PHP framework for building API-driven projects, supporting both Symfony and Laravel. It is structured as a **monorepo** with 21 components under `src/`, each with its own `composer.json` and test directory. Requires PHP 8.2+.

## Common Commands

### Testing

**Never run the full test suite.** Always filter by file or method.

```bash
# PHPUnit - preferred for new tests
vendor/bin/phpunit tests/Functional/MyTest.php
vendor/bin/phpunit --filter testMethodName

# Behat - legacy, do NOT add new Behat tests
vendor/bin/behat features/main/crud.feature:120 --format=progress

# Component tests (requires soyuka/pmu globally installed)
cd src/Metadata && composer link ../../ && ./vendor/bin/phpunit
```

**Clear cache when switching branches, modes, or env config:**
```bash
rm -rf tests/Fixtures/app/var/cache/test
```

**Event Listeners mode** (CI tests both modes):
```bash
export USE_SYMFONY_LISTENERS=1
rm -rf tests/Fixtures/app/var/cache/test
vendor/bin/phpunit tests/Functional/MyTest.php
```

### Code Quality

```bash
# Code style (Symfony standards)
vendor/bin/php-cs-fixer fix --diff

# Static analysis (level 6)
vendor/bin/phpstan analyse

# PHPStan with MongoDB requires: composer require --dev doctrine/mongodb-odm-bundle doctrine/mongodb-odm

# Symfony container lint
tests/Fixtures/app/console lint:container
```

### Development Server

```bash
symfony server:start --dir tests/Fixtures/app
# or: php -S localhost:8000 -t tests/Fixtures/app/public/
```

### Symfony Console (test app)

```bash
tests/Fixtures/app/console <command>
```

## Architecture

### Provider/Processor Pipeline

Every API request flows through: **Parameter Resolution → Deserialization → Provider → Processor → Serialization → Response**.

- `ProviderInterface::provide(Operation, array $uriVariables, array $context)` — fetches data
- `ProcessorInterface::process(mixed $data, Operation, array $uriVariables, array $context)` — writes/transforms data

### Key Components (`src/`)

- **Metadata** — PHP attributes (`#[ApiResource]`, `#[ApiProperty]`, `#[ApiFilter]`) and operation definitions (`Get`, `Post`, `Patch`, `Delete`, `GetCollection`)
- **State** — Provider/Processor interfaces and default implementations
- **Doctrine/Orm** and **Doctrine/Odm** — Doctrine ORM and MongoDB ODM integrations, including query filters (`SearchFilter`, `OrderFilter`, `RangeFilter`, `DateFilter`, `ExistsFilter`, etc.)
- **Serializer** — Multi-format serialization built on Symfony Serializer
- **JsonLd**, **Hydra**, **JsonApi**, **Hal** — Output format handlers
- **JsonSchema**, **OpenApi** — Schema/documentation generation
- **Symfony** — Symfony bundle, controllers, routing, security, event listeners
- **Laravel** — Laravel integration
- **GraphQl** — GraphQL support
- **Validator** — Constraint validation integration

### Resources and Operations

API endpoints are defined via `#[ApiResource]` attributes on PHP classes. Each operation (GET, POST, etc.) can specify its own provider, processor, input/output DTOs, serialization groups, and filters. If database persistence isn't required, use a static provider instead of Doctrine.

## Test Conventions

- **Do NOT modify existing fixtures** — always create new Entity/ApiResource/DTO classes in `tests/Fixtures/TestBundle/`
- **Do NOT add new Behat tests** — use PHPUnit functional tests in `tests/Functional/`
- For functional tests, register your ApiResource class in `tests/PhpUnitResourceNameCollectionFactory.php`
- Use PHPUnit's stub system, not Prophesize
- Excluded test groups by default: `mongodb`, `mercure`
- Each component under `src/` has its own `Tests/` directory for unit tests

## Coding Standards

- PHP 8.2+ with strict typing
- Imports grouped by type (class, function, const), sorted alphabetically
- Follows [Symfony coding standards](https://symfony.com/doc/current/contributing/code/standards.html)
- Never break backward compatibility — use deprecations for removals

## Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/). Allowed types: `fix`, `feat`, `docs`, `spec`, `test`, `perf`, `ci`, `chore`. Use a scope: `feat(metadata): add filter validation`. Use `!` for breaking changes: `feat(metadata)!: redesign operations`.

## Dependency Management

This is a monorepo. Use `composer blend --all` after changing version constraints in root `composer.json` to propagate to sub-components. Use `soyuka/pmu` for linking components during development.
