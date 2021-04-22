<?php declare(strict_types=1);

namespace VitesseCms\Install\Migrations;

use VitesseCms\Cli\Services\TerminalServiceInterface;
use VitesseCms\Configuration\Services\ConfigServiceInterface;
use VitesseCms\Install\Interfaces\MigrationInterface;
use VitesseCms\Install\Repositories\MigrationCollectionInterface;

class Migration_20210416 implements MigrationInterface
{
    public static function up(
        ConfigServiceInterface $configService,
        MigrationCollectionInterface $migrationCollection,
        TerminalServiceInterface $terminalService
    ): bool
    {
        $result = true;
        if (!self::parseDatagroups($migrationCollection, $terminalService)) :
            $result = false;
        endif;
        if (!self::parseBlocks($migrationCollection, $terminalService)) :
            $result = false;
        endif;

        return $result;
    }

    private static function parseBlocks(
        MigrationCollectionInterface $migrationCollection,
        TerminalServiceInterface $terminalService
    ): bool
    {
        $result = true;
        $blocks  =$migrationCollection->block->findAll(null,false);
        $dir = str_replace('install/src/Migrations', 'core/src/Services/../../../../../vendor/vitessecms/mustache/src/', __DIR__);
        $search = [
            'Template/core/',
            'templates/default/',
            $dir
        ];
        $replace = [
            '',
            '',
            ''
        ];
        while ($blocks->valid()):
            $block = $blocks->current();
            $template = str_replace($search,$replace,$block->getTemplate());
            if (substr($template, 0, 6) === "views/") :
                $block->setTemplate($template)->save();
            else :
                $terminalService->printError('wrong template "' . $template . '" for block "' . $block->getNameField() . '"');
                $result = false;
            endif;

            $blocks->next();
        endwhile;

        $terminalService->printMessage('Block template repaired');

        return $result;
    }

    private static function parseDatagroups(
        MigrationCollectionInterface $migrationCollection,
        TerminalServiceInterface $terminalService
    ): bool
    {
        $result = true;
        $datagroups = $migrationCollection->datagroup->findAll(null, false);
        $dir = str_replace('install/src/Migrations', 'core/src/Services/../../../../../vendor/vitessecms/mustache/src/', __DIR__);
        $search = [
            'default/',
            'templates/',
            'Templates/',
            'Template/core/',
            $dir
        ];
        $replace = [
            'core/',
            'Template/',
            'Template/',
            '',
            ''
        ];
        while ($datagroups->valid()):
            $datagroup = $datagroups->current();
            $template = str_replace($search, $replace, $datagroup->getTemplate());

            if (substr($template, 0, 6) === "views/") :
                $datagroup->setTemplate($template)->save();
            else :
                $terminalService->printError('wrong template "' . $template . '" for datagroup "' . $datagroup->getNameField() . '"');
                $result = false;
            endif;

            $datagroups->next();
        endwhile;
        $terminalService->printMessage('Datagroups template repaired');

        return $result;
    }
}