<?php

namespace App\Console\Commands;

use App\Models\Wilayah;
use App\Models\Keluarga;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;



class penduduk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'faker:penduduk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate data penduduk';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $kd_wilayah = Faker::create('id_ID');

        for ($i=0; $i < 30; $i++) {
            $dusun = $kd_wilayah->streetName;
            $lat = $kd_wilayah->latitude();
            $lng = $kd_wilayah->longitude();

            $wilayah = Wilayah::insert([
                [
                    'rt' => 0,
                    'rw'=>
                ]
            ])
        }
    }
}
