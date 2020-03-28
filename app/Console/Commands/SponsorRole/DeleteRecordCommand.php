<?php

namespace App\Console\Commands\SponsorRole;

use Illuminate\Console\Command;
use Smart2\CommandModel\Eloquent\SponsorRole;

class DeleteRecordCommand extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsorRole:deleteRecord';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        $this->setSignature([
            'sponsor_id? : type:numeric',
        ]);

        $this->description = $this->descriptionForInteractive;

        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $sponsorId = $this->argumentsSponsorId();

        $this->confirmApply('delete', [
            'sponsor_id: ' . $sponsorId,
        ]);

        $sponsorRole = SponsorRole::findOrFail($sponsorId);
        $sponsorRole->delete();

        $this->infoApplied();
    }
}
