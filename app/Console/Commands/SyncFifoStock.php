<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\IncomingGood;

class SyncFifoStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wms:sync-fifo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync incoming goods remaining_quantity based on current global stock (assume FIFO)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting FIFO synchronization...');
        
        $items = Item::all();
        $bar = $this->output->createProgressBar(count($items));
        
        foreach ($items as $item) {
            $currentStock = $item->stock_quantity;
            
            // Get all incoming goods sorted by date DESC (Newest first)
            // LIFO for allocation means FIFO for usage
            $incomingGoods = IncomingGood::where('item_id', $item->id)
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->get();
                
            foreach ($incomingGoods as $batch) {
                if ($currentStock <= 0) {
                    // Stock exhausted, this batch is fully used
                    $batch->update(['remaining_quantity' => 0]);
                } elseif ($currentStock >= $batch->quantity) {
                    // Current stock covers this entire batch
                    $batch->update(['remaining_quantity' => $batch->quantity]);
                    $currentStock -= $batch->quantity;
                } else {
                    // Current stock covers only part of this batch
                    $batch->update(['remaining_quantity' => $currentStock]);
                    $currentStock = 0;
                }
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('FIFO synchronization completed successfully!');
    }
}
