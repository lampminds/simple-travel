<?php

namespace Tests\Feature;

use App\Listeners\RecordUserLoginSession;
use App\Models\LmpIp;
use App\Models\LmpSession;
use App\Models\User;
use App\Support\WebsiteImpersonationSession;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecordUserLoginSessionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
            'activitylog.enabled' => false,
            'services.ip_geolocation.url' => 'https://ipwho.is',
            'services.ip_geolocation.timeout' => 2,
            'services.ip_geolocation.retry_after_minutes' => 1440,
        ]);

        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
    }

    #[Test]
    public function it_looks_up_a_public_ip_once_and_reuses_the_cache(): void
    {
        Http::fake([
            'ipwho.is/*' => Http::response($this->geoPayload()),
        ]);

        $user = $this->makeUser();

        $this->loginFrom('8.8.8.8', $user);
        $this->loginFrom('8.8.8.8', $user);

        Http::assertSentCount(1);

        $ip = LmpIp::query()->where('ip_address', '8.8.8.8')->first();
        $this->assertNotNull($ip);
        $this->assertSame(LmpIp::STATUS_RESOLVED, $ip->status);
        $this->assertSame('US', $ip->country_code);
        $this->assertSame('United States', $ip->country_name);
        $this->assertSame('California', $ip->region_name);
        $this->assertSame('Mountain View', $ip->city);

        $this->assertSame(2, LmpSession::query()->where('user_id', $user->id)->count());
        $this->assertSame('PHPUnit Agent', LmpSession::query()->where('user_id', $user->id)->value('user_agent'));
    }

    #[Test]
    public function it_does_not_call_the_service_for_a_local_ip(): void
    {
        Http::fake();
        app()->setLocale('es');

        $user = $this->makeUser();
        $this->loginFrom('127.0.0.1', $user);

        Http::assertNothingSent();

        $ip = LmpIp::query()->where('ip_address', '127.0.0.1')->first();
        $this->assertNotNull($ip);
        $this->assertSame(LmpIp::STATUS_LOCAL, $ip->status);
        $this->assertSame('Red local', $ip->originLabel());
        $this->assertSame(1, LmpSession::query()->count());
    }

    #[Test]
    public function it_does_not_record_support_impersonation(): void
    {
        Http::fake();

        $user = $this->makeUser();
        $request = $this->bindRequest('8.8.8.8');
        WebsiteImpersonationSession::markActive($request);

        app(RecordUserLoginSession::class)->handle(new Login('web', $user, false));

        Http::assertNothingSent();
        $this->assertSame(0, LmpSession::query()->count());
    }

    #[Test]
    public function it_retries_a_failed_lookup_only_after_the_cooldown(): void
    {
        Http::fake([
            'ipwho.is/*' => Http::sequence()
                ->push(['success' => false], 500)
                ->push($this->geoPayload(), 200),
        ]);

        $user = $this->makeUser();

        $this->loginFrom('1.1.1.1', $user);
        $this->loginFrom('1.1.1.1', $user);

        Http::assertSentCount(1);
        $this->assertSame(LmpIp::STATUS_FAILED, LmpIp::query()->where('ip_address', '1.1.1.1')->value('status'));

        $this->travel(2)->days();
        $this->loginFrom('1.1.1.1', $user);

        Http::assertSentCount(2);

        $ip = LmpIp::query()->where('ip_address', '1.1.1.1')->first();
        $this->assertNotNull($ip);
        $this->assertSame(LmpIp::STATUS_RESOLVED, $ip->status);
        $this->assertSame('Mountain View', $ip->city);
        $this->assertSame(3, LmpSession::query()->where('user_id', $user->id)->count());
    }

    private function makeUser(): User
    {
        return User::query()->create([
            'name' => 'Session Tester',
            'email' => 'session-tester-'.uniqid().'@example.com',
            'password' => 'password',
        ]);
    }

    private function loginFrom(string $ipAddress, User $user): void
    {
        $this->bindRequest($ipAddress);

        app(RecordUserLoginSession::class)->handle(new Login('web', $user, false));
    }

    private function bindRequest(string $ipAddress): Request
    {
        $request = Request::create('/', 'GET', server: [
            'REMOTE_ADDR' => $ipAddress,
            'HTTP_USER_AGENT' => 'PHPUnit Agent',
        ]);

        $session = $this->app['session']->driver();
        $session->start();
        $request->setLaravelSession($session);
        $this->app->instance('request', $request);

        return $request;
    }

    /**
     * @return array<string, mixed>
     */
    private function geoPayload(): array
    {
        return [
            'success' => true,
            'country' => 'United States',
            'country_code' => 'US',
            'region' => 'California',
            'city' => 'Mountain View',
            'latitude' => 37.40599,
            'longitude' => -122.077515,
            'timezone' => ['id' => 'America/Los_Angeles'],
        ];
    }

    private function createSchema(): void
    {
        if (DB::getDefaultConnection() !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            $this->fail('Refusing to build the test schema outside an in-memory sqlite database.');
        }

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('kicked_out')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->string('activation_state', 32)->default('active');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('lmp_ips', function (Blueprint $table): void {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->string('status', 16)->default('pending');
            $table->string('country_code', 2)->nullable();
            $table->string('country_name')->nullable();
            $table->string('region_name')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('timezone')->nullable();
            $table->timestamp('looked_up_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lmp_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lmp_ip_id')->nullable()->constrained('lmp_ips')->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('logged_in_at');
            $table->timestamps();
        });
    }
}
