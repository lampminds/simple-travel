<?php

namespace Tests\Unit\Services;

use App\Models\CommercialModulePrice;
use App\Models\CommercialModulePriceTier;
use App\Services\ModulePricingCalculator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModulePricingCalculatorTest extends TestCase
{
    private ModulePricingCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new ModulePricingCalculator;
    }

    #[Test]
    public function it_returns_fixed_price_regardless_of_user_count(): void
    {
        $price = $this->makePrice([
            'billing_type' => 'fixed',
            'base_price' => 120.50,
        ]);

        $this->assertSame(120.50, $this->calculator->monthlyAmount($price, 1));
        $this->assertSame(120.50, $this->calculator->monthlyAmount($price, 25));
    }

    #[Test]
    public function it_uses_tier_rate_when_available_otherwise_falls_back_to_base_rate(): void
    {
        $price = $this->makePrice([
            'billing_type' => 'per_user',
            'price_per_user' => 10.0,
            'tiers' => [
                ['from_users' => 1, 'to_users' => 5, 'price_per_user' => 8.0],
            ],
        ]);

        $this->assertSame(40.0, $this->calculator->monthlyAmount($price, 5));
        $this->assertSame(70.0, $this->calculator->monthlyAmount($price, 7));
    }

    #[Test]
    public function it_adds_base_price_to_per_user_total_for_all_users(): void
    {
        $price = $this->makePrice([
            'billing_type' => 'per_user',
            'base_price' => 250.0,
            'price_per_user' => 25.0,
        ]);

        $this->assertSame(325.0, $this->calculator->monthlyAmount($price, 3));
        $this->assertSame(275.0, $this->calculator->monthlyAmount($price, 1));
    }

    #[Test]
    public function it_adds_base_price_with_tier_rate_for_per_user_billing(): void
    {
        $price = $this->makePrice([
            'billing_type' => 'per_user',
            'base_price' => 250.0,
            'price_per_user' => 25.0,
            'tiers' => [
                ['from_users' => 2, 'to_users' => 4, 'price_per_user' => 25.0],
                ['from_users' => 5, 'to_users' => 10, 'price_per_user' => 15.0],
            ],
        ]);

        $this->assertSame(325.0, $this->calculator->monthlyAmount($price, 5));
        $this->assertSame(350.0, $this->calculator->monthlyAmount($price, 4));
    }

    #[Test]
    public function it_calculates_hybrid_price_with_included_users_and_extra_rate(): void
    {
        $price = $this->makePrice([
            'billing_type' => 'hybrid',
            'base_price' => 100.0,
            'included_users' => 3,
            'price_per_user' => 12.0,
            'tiers' => [
                ['from_users' => 4, 'to_users' => 10, 'price_per_user' => 9.0],
            ],
        ]);

        $this->assertSame(100.0, $this->calculator->monthlyAmount($price, 3));
        $this->assertSame(127.0, $this->calculator->monthlyAmount($price, 6));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makePrice(array $attributes): CommercialModulePrice
    {
        $price = new CommercialModulePrice([
            'billing_type' => $attributes['billing_type'],
            'base_price' => $attributes['base_price'] ?? null,
            'included_users' => $attributes['included_users'] ?? null,
            'price_per_user' => $attributes['price_per_user'] ?? null,
            'active' => true,
        ]);

        $tiers = collect($attributes['tiers'] ?? [])
            ->map(fn (array $tier): CommercialModulePriceTier => new CommercialModulePriceTier([
                'from_users' => $tier['from_users'] ?? null,
                'to_users' => $tier['to_users'] ?? null,
                'price_per_user' => $tier['price_per_user'] ?? null,
            ]));

        $price->setRelation('tiers', new Collection($tiers->all()));

        return $price;
    }
}
