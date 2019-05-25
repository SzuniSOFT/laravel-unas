<?php


namespace SzuniSoft\Unas\Tests\Client;


use function call_user_func;
use Carbon\Carbon;
use function dd;
use Mockery\MockInterface;

class AuthenticatedTestCase extends TestCase
{

    protected function createSnapshotClient($cb = null, $config = [])
    {
        return parent::createSnapshotClient(function (MockInterface $mock) use (&$cb) {

            $this->mockProperty($mock, 'token', 'token');
            $this->mockProperty($mock, 'tokenExpiresAt', Carbon::now()->addHour());

            if ($cb) {
                call_user_func($cb, [$mock]);
            }
        }, $config);
    }

}
