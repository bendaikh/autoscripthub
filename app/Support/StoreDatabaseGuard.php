<?php

namespace Fickrr\Support;

use RuntimeException;

class StoreDatabaseGuard
{
    /**
     * Databases that belong to the digital marketplace and must never be
     * opened by the physical store, in any environment except automated tests.
     *
     * @return array<int, string>
     */
    public static function alwaysForbidden(): array
    {
        $configured = config('store.forbidden_databases', []);
        $names = array_merge(
            ['autoscripthub', 'auto_script_hub', 'auto-script-hub'],
            is_array($configured) ? $configured : []
        );

        return array_values(array_unique(array_map(function ($name) {
            return strtolower(trim((string) $name));
        }, $names)));
    }

    public static function violation(?string $database, bool $enforceSeparate): ?string
    {
        $name = strtolower(trim((string) $database));

        if (in_array($name, self::alwaysForbidden(), true)) {
            return 'Refusing to use the auto-script-hub database "' . $database . '". The physical store at esipres.com requires its own database.';
        }

        if ($enforceSeparate) {
            $sharedDefaults = ['', 'homestead', 'forge', 'laravel', 'mysql', 'test', 'testing', 'database'];
            if (in_array($name, $sharedDefaults, true)) {
                return 'Physical store deployment requires DB_DATABASE to be a dedicated database (for example esipres), not "' . ($database === null || $database === '' ? '(empty)' : $database) . '".';
            }
        }

        return null;
    }

    public static function currentDatabaseName(): string
    {
        $connection = (string) config('database.default', 'mysql');

        return (string) config('database.connections.' . $connection . '.database', '');
    }

    public static function assertSeparate(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        if (!Store::isPhysical()) {
            return;
        }

        $enforce = app()->environment('production') || (bool) config('store.enforce_separate_database');
        $violation = self::violation(self::currentDatabaseName(), $enforce);

        if ($violation !== null) {
            throw new RuntimeException($violation);
        }
    }
}
