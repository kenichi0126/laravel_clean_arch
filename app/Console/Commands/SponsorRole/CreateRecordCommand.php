<?php

namespace App\Console\Commands\SponsorRole;

use Illuminate\Console\Command;
use Smart2\CommandModel\Eloquent\SponsorRole;

class CreateRecordCommand extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsorRole:createRecord';

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
            'permissions? : "type:json',
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
        // {"smart2::real_time::view": {"contract": {"start": "1900-01-01 00:00:00","end": "9999-12-31 23:59:59"}}, "smart2::region_kanto::view": {"contract": {"start": "1900-01-01 00:00:00","end": "9999-12-31 23:59:59"}}}
        $permissions = $this->argumentsPermissions();

        $this->confirmApply('insert', [
            'sponsor_id: ' . $sponsorId,
            'permissions: ' . json_encode($permissions, JSON_PRETTY_PRINT),
        ]);

        $sponsorRole = new SponsorRole();
        $sponsorRole->forcefill([
            'sponsor_id' => $sponsorId,
            'permissions' => $permissions,
        ])->save();

        $this->infoApplied();
    }
}
