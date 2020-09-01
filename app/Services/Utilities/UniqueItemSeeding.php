<?php

namespace App\Services\Utilities;

use Exception;
use Illuminate\Support\Str;

class UniqueItemSeeding
{
    /**
     * the artisan command 
     */
    private $command;

    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * seed data with ehecking unique constraint violation
     * 
     * @param string $model
     * @param int $rowsCount
     * 
     * @return void
     */
    public function seedWithUniqueConstraint(string $modelName, int $rowsCount, $extraData = null): void
    {
        $maxIterationCount = 10;
        $count = 0;
        while (true) {
            $count++;
            try {
                if ($extraData) {
                    factory($modelName, $rowsCount)->create($extraData);
                } else {
                    factory($modelName, $rowsCount)->create();
                }
                $this->command->info("{$rowsCount} {$this->getNameFromModelClass($modelName)} have been created successfully");
                break;
            } catch (Exception $e) {
                if ($count <= $maxIterationCount) {
                    $this->command->warn("Not all generated, trying again...");
                    sleep(1);
                    continue;
                }
                $this->command->error("Some items created but not all");
                break;
            }
        }
    }

    /**
     * get actual model name without the (::class) suffix
     * 
     * @param string $modelName
     * 
     * @return string
     */
    private function getNameFromModelClass(string $modelName): string
    {
        return Str::plural(
            explode(
                '-',
                Str::kebab(
                    class_basename($modelName)
                )
            )[0]
        );
    }
}
