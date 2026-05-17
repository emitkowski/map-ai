# map-ai

Framework-agnostic core for the **MAP AI** documentation scaffold. Contains the stubs and installer logic used by framework-specific packages.

## What is MAP?

MAP (Multi-Agent Project) is a documentation standard for AI-assisted development. It provides a structured set of markdown files that AI coding agents (Claude, Gemini, Copilot, Cursor) read at session start to get consistent context about your project.

## Framework packages

Install MAP via your framework's package — this core package is a dependency, not meant to be required directly:

| Framework | Package |
|-----------|---------|
| Laravel | [`larablocks/map-ai-laravel`](https://github.com/larablocks/map-ai-laravel) |

## For package authors

If you want to build a MAP installer for another framework, require this package and use:

```php
use larablocks\MapAi\Installer;

$result = (new Installer)->install(
    stubsPath: Installer::stubsPath(),
    targetPath: '/path/to/project',
    force: false,
);
```

`$result` contains a `files` array with per-file `action` (`copy`, `update`, `skip`, `missing`) and a `gitignore` key (`updated` or `skipped`).

## Development

```bash
composer install
composer test
composer analyse
composer format
```

## License

MIT
