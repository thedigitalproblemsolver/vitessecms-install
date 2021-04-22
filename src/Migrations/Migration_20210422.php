<?php declare(strict_types=1);

namespace VitesseCms\Install\Migrations;

use VitesseCms\Cli\Services\TerminalServiceInterface;
use VitesseCms\Configuration\Services\ConfigServiceInterface;
use VitesseCms\Install\Interfaces\MigrationInterface;
use VitesseCms\Install\Repositories\MigrationCollectionInterface;

class Migration_20210422 implements MigrationInterface
{
    public static function up(
        ConfigServiceInterface $configService,
        MigrationCollectionInterface $migrationCollection,
        TerminalServiceInterface $terminalService
    ): bool
    {
        $result = true;
        if (!self::parseDatafieldType($migrationCollection, $terminalService)) :
            $result = false;
        endif;
        if (!self::parseDatafieldModels($migrationCollection, $terminalService)) :
            $result = false;
        endif;

        return $result;
    }

    private static function parseDatafieldType(
        MigrationCollectionInterface $migrationCollection,
        TerminalServiceInterface $terminalService
    ): bool
    {
        $result = true;
        $datafields = $migrationCollection->datafield->findAll(null, false);
        $search = ['VitesseCms\Datafield\Models\\','Modules\Datafield\Models\\'];
        $replace = ['',''];
        while ($datafields->valid()):
            $datafield = $datafields->current();
            $type = str_replace($search,$replace,$datafield->getType());
            if(substr_count($type,'\\') === 0 ):
                $datafield->setType($type)->save();
            else :
                $terminalService->printError('srong type "'.str_replace($search,$replace,$datafield->getType()).'" for datafiel '.$datafield->getNameField());
            endif;
            $datafields->next();
        endwhile;

        $terminalService->printMessage('datafields type repaired');
        return $result;
    }

    private static function parseDatafieldModels(
        MigrationCollectionInterface $migrationCollection,
        TerminalServiceInterface $terminalService
    ): bool
    {
        $result = true;
        $datafields = $migrationCollection->datafield->findAll(null, false);
        $search = [
            'Modules\\'
        ];
        $replace = [
            'VitesseCms\\'
        ];
        while ($datafields->valid()):
            $datafield = $datafields->current();
            $model = str_replace($search, $replace, $datafield->getModel());
            if (!empty($model) && substr($model, 0, 10) === 'VitesseCms') :
                $datafield->setModel($model)->save();
            elseif(!empty($model)) :
                $terminalService->printError('wrong model "' . $model . '" for datafield "' . $datafield->getNameField() . '"');
                $result = false;
            endif;

            $datafields->next();
        endwhile;
        $terminalService->printMessage('datafields model repaired');

        return $result;
    }
}