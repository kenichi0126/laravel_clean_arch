<?php

namespace App\Console\Commands\SponsorRole;

use Illuminate\Console\Command;
use Smart2\CommandModel\Eloquent\SponsorRole;

class GetPermissionCommand extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsorRole:getPermission';

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
            'permission_key? : type:string',
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
        // smart2::spot_prices::view
        $permissionKey = $this->argumentsPermissionKey();

        $this->info("sponsor_id: {$sponsorId}");
        $this->info("permissions_key: {$permissionKey}");

        $sponsorRole = SponsorRole::findOrFail($sponsorId);

        if (isset($sponsorRole->permissions[$permissionKey])) {
            $permissions = $sponsorRole->permissions[$permissionKey];
            $this->info("permissions->{$permissionKey}: " . json_encode($permissions, JSON_PRETTY_PRINT));
        } else {
            $this->info("permissions->{$permissionKey}: Undefined");
        }
    }
}
