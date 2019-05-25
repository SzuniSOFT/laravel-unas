<?php


namespace SzuniSoft\Unas\Tests\Client;


use Mockery;
use Spatie\Snapshots\MatchesSnapshots;
use SzuniSoft\Unas\Internal\Client;
use SzuniSoft\Unas\Tests\BaseTestCase;
use function array_merge;
use function call_user_func;
use function dd;
use function file_get_contents;

abstract class TestCase extends BaseTestCase
{

    use MatchesSnapshots {
        getSnapshotId as getMatchesSnapshotId;
    }

    /**
     * @return array
     */
    protected function fakeLegacyCredentials()
    {
        return [
            'username' => 'username',
            'password' => 'password',
            'shop_id' => 1,
            'auth_code' => 1,
        ];
    }

    /**
     * @return array
     */
    protected function fakePremiumCredentials()
    {
        return [
            'key' => 123,
        ];
    }

    /**
     * @param null  $cb
     *
     * @param array $config
     *
     * @return \Mockery\Mock|\Mockery\MockInterface|\SzuniSoft\Unas\Internal\Client
     */
    protected function createSnapshotClient($cb = null, $config = [])
    {
        // [sendRequest]

        $config = array_merge([
            'base_path' => 'https://api.unas.eu/shop/',
        ],
            empty($config) ? [
                'key' => 'key',
            ] : $config);

        return Mockery::mock(
            Client::class, [$config],
            function (Mockery\MockInterface $mock) use (&$cb) {

                $mock
                    ->shouldAllowMockingProtectedMethods()
                    ->makePartial();

                $mock
                    ->shouldReceive('sendRequest->getBody')
                    ->andReturnUsing(function () {
                        return $this->getResponseSnapshot();
                    });

                if ($cb) {
                    call_user_func($cb, $mock);
                }
            });
    }


    /**
     * @return false|string
     */
    protected function getResponseSnapshot()
    {
        $content = file_get_contents($this->getSnapshotDirectory() . '/' . $this->getSnapshotId() . '_response.xml');
        $this->snapshotIncrementor++;
        return $content;
    }

}
