<?php declare(strict_types=1);

namespace VitesseCms\Install\Utils;

use VitesseCms\Cli\Services\TerminalService;
use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Core\Utils\DirectoryUtil;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Install\Interfaces\MigrationInterface;
use VitesseCms\Install\Models\Migration;
use VitesseCms\Install\Repositories\MigrationCollectionInterface;

class MigrationUtil
{
    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * @var MigrationCollectionInterface
     */
    private $migrationCollection;

    /**
     * @var TerminalService
     */
    private $terminalService;

    public function __construct(
        ConfigService $configService,
        MigrationCollectionInterface $migrationCollection,
        TerminalService $terminalService
    )
    {
        $this->configService = $configService;
        $this->migrationCollection = $migrationCollection;
        $this->terminalService = $terminalService;
    }

    public function executeUp(): void
    {
        $files = DirectoryUtil::getFilelist(__DIR__ . '/../Migrations');
        $this->terminalService->printHeader('Started Migrations');

        foreach ($files as $filePath => $fileName) :
            $name = str_replace('.php', '', $fileName);
            if ($this->migrationCollection->migration->findFirst(new FindValueIterator([new FindValue('name', $name)])) === null):
                $this->terminalService->printHeader('Started ' . $name);
                require_once $filePath;
                /** @var MigrationInterface $className */
                $className = 'VitesseCms\\Install\\Migrations\\' . $name;
                $result = $className::up(
                    $this->configService,
                    $this->migrationCollection,
                    $this->terminalService
                );
                if ($result):
                    (new Migration())->setName($name)->setPublished(true)->save();
                endif;
                $this->terminalService->printHeader('Finished ' . $name);
            endif;
        endforeach;

        $this->terminalService->printHeader('Finished Migrations');
    }

    public function rerunAll(): void
    {
        $this->terminalService->printHeader('Started rerun of all Migrations');
        $this->terminalService->printHeader('Started deleting of all Migrations');

        $migrations = $this->migrationCollection->migration->findAll(null, false);
        while ($migrations->valid()):
            $name = $migrations->current()->getName();
            if (!$migrations->current()->delete()):
                $this->terminalService->printError('deleting migration "' . $name . '"');
            endif;
            $migrations->next();
        endwhile;

        $this->terminalService->printHeader('Finished deleting of all Migrations');
        $this->executeUp();
        $this->terminalService->printHeader('Finished rerun of all Migrations');
    }
}
