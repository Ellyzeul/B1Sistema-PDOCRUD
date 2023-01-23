<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DeleteOldPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old-photos:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apagar as fotos com seis meses ou mais gravadas na pasta /public/static/photos';

    /**
     * Total de meses que uma foto ficará gravada no servidor.
     * 
     * @var int
     */
    protected $monthsIntervalToDelete = 6;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::notice('Rotina de exclusão de fotos antigas');

        $disk = Storage::disk('orders-photos');
        $today = Date::today();

        foreach($disk->files() as $photo) {
            $lastMod = Date::createFromTimestamp($disk->lastModified($photo));
            $monthsPassed = intval($today->diffAsCarbonInterval($lastMod)->format('%m'));

            if($monthsPassed >= $this->monthsIntervalToDelete) {
                Log::info("Foto $photo deletada");
                $disk->delete($photo);
            }
        }

        return 0;
    }
}
