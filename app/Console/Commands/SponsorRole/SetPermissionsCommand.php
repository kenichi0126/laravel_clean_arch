<?php

namespace App\Console\Commands\SponsorRole;

use Illuminate\Console\Command;
use Smart2\CommandModel\Eloquent\SponsorRole;

class SetPermissionsCommand extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsorRole:setPermissions';

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
            'update_permissions? : type:json',
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

        // {"permissions->smart2::spot_prices::view->contract->start": "2012-10-10 00:00:00","permissions->smart2::spot_prices::view->contract->end": "2012-10-10 23:59:59"}
        $updatePermissions = $this->argumentsUpdatePermissions();

        $this->confirmApply('update', [
            'sponsor_id: ' . $sponsorId,
            'update_permissions: ' . json_encode($updatePermissions, JSON_PRETTY_PRINT),
        ]);

        $sponsorRole = SponsorRole::findOrFail($sponsorId);
        $sponsorRole->forcefill($updatePermissions)->save();

        $this->infoApplied();
    }
}
