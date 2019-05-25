<?php


namespace SzuniSoft\Unas\Tests\Client;


use function array_merge;
use function call_user_func;
use Illuminate\Support\Facades\Event;
use SzuniSoft\Unas\Internal\Client;
use SzuniSoft\Unas\Tests\Client\__stubs__\EventExceptionStub;
use SzuniSoft\Unas\Tests\Client\__stubs__\EventStub;

class ErrorTest extends TestCase
{


    /** @test */
    public function can_convert_exception_to_event()
    {

        $client = $this->createSnapshotClient(null, array_merge(
            $this->fakeLegacyCredentials(),
            [
                'events' => [EventStub::class],
            ]
        ));

        $stub = new EventExceptionStub();

        Event::fake(EventStub::class);

        call_user_func([$client, 'error'], $stub);

        Event::assertDispatched(EventStub::class);
    }

    /** @test */
    public function does_not_convert_exception_to_event_when_not_wanted()
    {
        $this->expectException(EventExceptionStub::class);
        $client = $this->createSnapshotClient(null, array_merge(
            $this->fakeLegacyCredentials(),
            [
                'events' => [],
            ]
        ));

        $stub = new EventExceptionStub();

        Event::fake(EventStub::class);

        call_user_func([$client, 'error'], $stub);

        Event::assertNotDispatched(EventStub::class);
    }

}
