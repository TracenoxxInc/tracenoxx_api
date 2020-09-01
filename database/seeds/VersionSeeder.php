<?php

use App\Models\Version\Version;
use Illuminate\Database\Seeder;

class VersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $version = factory(Version::class)->create();
        $this->command->info("A version has been created with id: " . $version->id);
    }
}
