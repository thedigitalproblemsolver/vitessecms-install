<?php declare(strict_types=1);

namespace VitesseCms\Install\Utils;

use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Core\Utils\DirectoryUtil;
use VitesseCms\Install\Repositories\MigrationCollectionInterface;

class MigrationUtil {
    public static function executeUp(
        ConfigService $configService,
        MigrationCollectionInterface $migrationCollection
    ): void {
        $files = DirectoryUtil::getFilelist(__DIR__ .'/../Migrations');
        echo '=== Started Migrations ==='.PHP_EOL;
        foreach ($files as $filePath => $fileName) :
            echo '=== Started '.$fileName.' ==='.PHP_EOL;
            require_once $filePath;
            $className = 'VitesseCms\\Install\\Migrations\\'.str_replace('.php','',$fileName);
            $result = $className::up(
                $configService,
                $migrationCollection
            );
            if($result):

            endif;

            echo '=== Finished '.$fileName.' ==='.PHP_EOL;
        endforeach;
        echo '=== Finished Migrations ==='.PHP_EOL;
    }
}
