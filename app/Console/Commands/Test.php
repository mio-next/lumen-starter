<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\JWTService;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Command';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $entity = new User(['id' => 1]);
        $jwt = JWTService::issue($entity, 3);

        sleep(2);
        $token = JWTService::validate($jwt->toString());

        dd($token);
    }
}
