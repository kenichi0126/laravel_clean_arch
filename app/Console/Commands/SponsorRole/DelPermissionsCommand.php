<?php

namespace App\Console\Commands\SponsorRole;

use DB;
use Illuminate\Console\Command;
use Smart2\CommandModel\Eloquent\SponsorRole;

class DelPermissionsCommand extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsorRole:delPermissions';

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

        $this->description = $this->descriptionForInteractive;

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
        // smart2::spot_prices::view,contract,
        $permissionKey = $this->argumentsPermissionKey();

        $this->confirmApply('delete', [
            'sponsor_id: ' . $sponsorId,
            'permission_key: ' . $permissionKey,
        ]);

        SponsorRole::where('sponsor_id', '=', $sponsorId)->update([
            'permissions' => DB::raw("JSONB_DELETE_PATH(permissions, '{ {$permissionKey} }')"),
        ]);

        $this->infoApplied();
    }
}
