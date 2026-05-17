<?php

namespace larablocks\MapAi;

class Installer
{
    public const array FILES = [
        'AGENTS.md',
        'CLAUDE.md',
        'GEMINI.md',
        'HANDOFF.example.md',
        'CLAUDE.local.example.md',
        '.claude/rules/security.md',
        '.claude/rules/testing.md',
        '.github/copilot-instructions.md',
        '.cursor/rules/agents.mdc',
        'docs/ARCHITECTURE.md',
        'docs/ARCHITECTURE_HISTORY.md',
        'docs/BUGS.md',
        'docs/BUGS_ARCHIVE.md',
        'docs/CODE_PATTERNS.md',
        'docs/DOCKER.md',
        'docs/GLOSSARY.md',
        'docs/MEMORY.example.md',
        'docs/SCHEMA.md',
        'docs/SETUP.md',
        'docs/STATUS.md',
        'docs/TESTING_COVERAGE.md',
        'docs/agents/agent.example.md',
        'docs/api/api.example.md',
        'docs/integrations/integration.example.md',
        'docs/memory/agents.example.md',
        'docs/memory/database.example.md',
        'docs/memory/environment.example.md',
        'docs/memory/framework.example.md',
        'docs/memory/gotchas.example.md',
        'docs/memory/shared.example.md',
        'docs/memory/testing.example.md',
    ];

    private const string GITIGNORE_BLOCK = <<<'BLOCK'

# MAP — developer-specific files (do not commit)
# Claude session state — developer specific, not shared
HANDOFF.md
.claude/settings.local.json

# Claude personal local rules — developer specific, not shared
CLAUDE.local.md

# Claude auto-memory — session/machine specific
# Copy *.example.md files to their non-example versions on first clone
docs/MEMORY.md
docs/memory/*.md
!docs/memory/*.example.md
!docs/memory/shared.md
BLOCK;

    private const array GITIGNORE_SENTINELS = [
        'HANDOFF.md',
        '.claude/settings.local.json',
        'CLAUDE.local.md',
        'docs/MEMORY.md',
        'docs/memory/*.md',
    ];

    public static function stubsPath(): string
    {
        return dirname(__DIR__).'/stubs';
    }

    /**
     * @return array{
     *     files: list<array{action: 'copy'|'update'|'skip'|'missing', file: string}>,
     *     gitignore: 'updated'|'skipped'
     * }
     */
    public function install(string $stubsPath, string $targetPath, bool $force = false): array
    {
        $files = [];

        foreach (self::FILES as $file) {
            $files[] = $this->copyFile($stubsPath, $targetPath, $file, $force);
        }

        return [
            'files' => $files,
            'gitignore' => $this->mergeGitignore($targetPath),
        ];
    }

    /** @return array{action: 'copy'|'update'|'skip'|'missing', file: string} */
    private function copyFile(string $stubsPath, string $targetPath, string $file, bool $force): array
    {
        $src = $stubsPath.'/'.$file;
        $dst = $targetPath.'/'.$file;

        if (! file_exists($src)) {
            return ['action' => 'missing', 'file' => $file];
        }

        if (file_exists($dst) && ! $force) {
            return ['action' => 'skip', 'file' => $file];
        }

        $action = file_exists($dst) ? 'update' : 'copy';

        if (! is_dir(dirname($dst))) {
            mkdir(dirname($dst), 0755, true);
        }

        copy($src, $dst);

        return ['action' => $action, 'file' => $file];
    }

    private function mergeGitignore(string $targetPath): string
    {
        $gitignorePath = $targetPath.'/.gitignore';
        $existing = file_exists($gitignorePath) ? file_get_contents($gitignorePath) : '';
        $existingLines = $existing !== '' ? array_map('trim', explode("\n", $existing)) : [];

        foreach (self::GITIGNORE_SENTINELS as $sentinel) {
            if (! in_array($sentinel, $existingLines, true)) {
                file_put_contents($gitignorePath, $existing.self::GITIGNORE_BLOCK."\n");

                return 'updated';
            }
        }

        return 'skipped';
    }
}
