<?php declare(strict_types=1);

namespace VitesseCms\Install\Utils;

use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Core\Utils\DirectoryUtil;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Install\Models\Migration;
use VitesseCms\Install\Repositories\MigrationCollectionInterface;

class MigrationUtil {
    public static function executeUp(
        ConfigService $configService,
        MigrationCollectionInterface $migrationCollection
    ): void {
        $files = DirectoryUtil::getFilelist(__DIR__ .'/../Migrations');
        echo '=== Started Migrations ==='.PHP_EOL;
        foreach ($files as $filePath => $fileName) :
            $name = str_replace('.php','',$fileName);
            if ($migrationCollection->migration->findFirst(new FindValueIterator([new FindValue('name', $name)])) === null ):
                echo '=== Started '.$name.' ==='.PHP_EOL;
                require_once $filePath;
                $className = 'VitesseCms\\Install\\Migrations\\'.$name;
                $result = $className::up(
                    $configService,
                    $migrationCollection
                );
                if($result):
                    (new Migration())->setName($name)->setPublished(true)->save();
                endif;

                echo '=== Finished '.$name.' ==='.PHP_EOL;
            endif;
        endforeach;
        echo '=== Finished Migrations ==='.PHP_EOL;
    }
}
