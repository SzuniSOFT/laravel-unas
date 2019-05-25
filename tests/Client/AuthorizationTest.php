<?php


namespace SzuniSoft\Unas\Tests\Client;


use function call_user_func;
use function dd;
use SzuniSoft\Unas\Exceptions\AuthenticationException;
use SzuniSoft\Unas\Exceptions\PremiumAuthenticationException;

class AuthorizationTest extends TestCase
{

    /** @test */
    public function throws_premium_exception_when_package_is_insufficient_for_api_keys()
    {
        $this->expectException(PremiumAuthenticationException::class);
        $this->createSnapshotClient(
            null,
            $this->fakePremiumCredentials()
        )->authorize();
    }

    /** @test */
    public function throws_exception_when_legacy_credentials_are_invalid()
    {
        $client = $this->createSnapshotClient(null, $this->fakeLegacyCredentials());
        try {
            call_user_func([$client, 'request'], 'something');
        } catch (AuthenticationException $exception) {
            $this->assertSame('shop_id', $exception->getField());
        }
    }

    /** @test */
    public function throws_exception_when_failed_to_authorize_premium()
    {
        $this->expectException(AuthenticationException::class);
        $client = $this->createSnapshotClient(null, $this->fakePremiumCredentials());

        $client->authorize();

        $client->shouldNotHaveReceived('legacyAuthorization');
    }

    /** @test */
    public function returns_false_when_failed_to_authorize_premium()
    {
        $premiumClient = $this->createSnapshotClient(
            null, $this->fakePremiumCredentials()
        );

        $this->assertFalse($premiumClient->authorize(true));
        $premiumClient->shouldNotHaveReceived('legacyAuthorization');
    }

    /** @test */
    public function legacy_adds_body_payload()
    {
        $legacyClient = $this->createSnapshotClient(
            null, $this->fakeLegacyCredentials()
        );
        $this->assertTrue($legacyClient->authorize(true, $body));

        $legacyClient->shouldNotHaveReceived('premiumAuthorization');

        $this->assertMatchesXmlSnapshot($body['auth']);
    }

    /** @test */
    public function sets_token_and_expiry_when_succeed_to_authorize()
    {
        $client = $this->createSnapshotClient();
        $this->assertTrue($client->authorize());
        $this->assertEquals(
            '2019-01-01 10:00:00',
            $client->getTokenExpiresAt()->format('Y-m-d H:i:s')
        );
        $this->assertEquals(
            'xyz789',
            $client->getToken()
        );
    }

}
