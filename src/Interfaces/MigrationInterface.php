<?php declare(strict_types=1);

namespace VitesseCms\Install\Interfaces;

use VitesseCms\Cli\Services\TerminalServiceInterface;
use VitesseCms\Configuration\Services\ConfigServiceInterface;
use VitesseCms\Install\Repositories\MigrationCollectionInterface;

interface MigrationInterface
{
    public static function up(
        ConfigServiceInterface $configService,
        MigrationCollectionInterface $migrationCollection,
        TerminalServiceInterface $terminalService
    ): bool;
}