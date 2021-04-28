<?php declare(strict_types=1);

namespace VitesseCms\Install\Interfaces;

use VitesseCms\Cli\Services\TerminalServiceInterface;
use VitesseCms\Configuration\Services\ConfigServiceInterface;

interface MigrationInterface
{
    public function up(
        ConfigServiceInterface $configService,
        TerminalServiceInterface $terminalService
    ): bool;
}