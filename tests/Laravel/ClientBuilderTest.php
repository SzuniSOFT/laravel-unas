<?php


namespace SzuniSoft\Unas\Tests\Laravel;


use SzuniSoft\Unas\Laravel\Support\ClientBuilder;
use SzuniSoft\Unas\Laravel\Support\UnasServiceProvider;
use SzuniSoft\Unas\Tests\BaseTestCase;

class ClientBuilderTest extends BaseTestCase
{

    /**
     * @var ClientBuilder
     */
    private $builder;

    protected function getPackageProviders($app)
    {
        return [UnasServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->app->get(ClientBuilder::class);
    }

    /** @test */
    public function allowed_events_always_unique()
    {
        $this->builder->allowedEvents('whatever', 'whatever');
        $this->assertEquals(1, count($this->builder->config()['events']));
    }

    /** @test */
    public function disallowed_events_can_be_deleted()
    {
        $this->builder->allowedEvents('whatever');
        $this->builder->disallowedEvents('whatever');
        $this->assertEquals(0, count($this->builder->config()['events']));
    }

    /** @test */
    public function premium_overwrites_legacy()
    {
        $this->builder
            ->withLegacy('1', '1', '1', '1')
            ->withPremium('key');

        $this->assertArrayNotHasKey('username', $this->builder->config());
        $this->assertArrayNotHasKey('password', $this->builder->config());
        $this->assertArrayNotHasKey('shop_id', $this->builder->config());
        $this->assertArrayNotHasKey('auth_code', $this->builder->config());
        $this->assertEquals('key', $this->builder->config()['key']);
    }

    /** @test */
    public function legacy_overwrites_premium()
    {
        $this->builder
            ->withPremium('key')
            ->withLegacy('1', '1', '1', '1');

        $this->assertEquals('1', $this->builder->config()['username']);
        $this->assertEquals('1', $this->builder->config()['password']);
        $this->assertEquals('1', $this->builder->config()['shop_id']);
        $this->assertEquals('1', $this->builder->config()['auth_code']);
        $this->assertArrayNotHasKey('key', $this->builder->config());
    }

}
