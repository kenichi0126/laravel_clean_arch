<?php

namespace App\Console\Commands\SponsorRole;

use Illuminate\Console\Command;
use Smart2\CommandModel\Eloquent\SponsorRole;

class GetPermissionsCommand extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsorRole:getPermissions';

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

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sponsorId = $this->argumentsSponsorId();

        $this->info("sponsor_id: {$sponsorId}");

        $sponsorRole = SponsorRole::findOrFail($sponsorId);

        $this->info('permissions: ' . json_encode($sponsorRole->permissions, JSON_PRETTY_PRINT));
    }
}
