<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

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
    protected $description = 'Apagar as fotos com três meses ou mais gravadas na pasta /public/static/photos';

    /**
     * Total de meses que uma foto ficará gravada no servidor.
     * 
     * @var int
     */
    private const MONTHS_INTERVAL = 3;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $disk = Storage::disk('orders-photos');
        $today = Date::today();

        foreach($disk->files() as $photo) {
            try {
                $lastMod = Date::createFromTimestamp($disk->lastModified($photo));
                $monthsPassed = intval($today->diffAsCarbonInterval($lastMod)->format('%m'));
    
                if($monthsPassed >= self::MONTHS_INTERVAL) {
                    $disk->delete($photo);
                }
            }
            catch(\Throwable) {
                continue;
            }
        }

        return 0;
    }
}
