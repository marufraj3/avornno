<?php

namespace Tests\Unit;

use App\Services\FacebookCapiService;
use Tests\TestCase;

/**
 * Facebook Conversions API (server-side tracking) এর payload তৈরির ইউনিট টেস্ট।
 *
 * এগুলো শুধু payload-building লজিক পরীক্ষা করে — কোন HTTP রিকোয়েস্ট যায় না,
 * তাই initialize() কল হয় না (credentials লাগে না)।
 */
class FacebookCapiServiceTest extends TestCase
{
    protected function prepareUserData(array $userData): array
    {
        $method = new \ReflectionMethod(FacebookCapiService::class, 'prepareUserData');

        return $method->invoke(new FacebookCapiService(), $userData);
    }

    protected function prepareCustomData(array $data, string $eventName = 'Purchase'): array
    {
        $method = new \ReflectionMethod(FacebookCapiService::class, 'prepareCustomData');

        return $method->invoke(new FacebookCapiService(), $data, $eventName);
    }

    /**
     * বাংলাদেশি চেকআউট ফর্ম 017xxxxxxxx ফরম্যাটে ফোন পাঠায়। Facebook কিন্তু
     * international format (8801712345678) চায় — না হলে Event Match Quality কমে
     * যায় এবং অ্যাড অপ্টিমাইজেশন ঠিকমতো শেখে না।
     *
     * @dataProvider bangladeshiPhoneProvider
     */
    public function test_bangladeshi_phone_numbers_are_hashed_in_international_format(string $input): void
    {
        $prepared = $this->prepareUserData(['phone' => $input]);

        $this->assertSame(hash('sha256', '8801712345678'), $prepared['ph']);
    }

    public static function bangladeshiPhoneProvider(): array
    {
        return [
            'local mobile format (checkout form)' => ['01712345678'],
            'spaced'                              => ['017 1234 5678'],
            'already international'               => ['8801712345678'],
            'with plus sign'                      => ['+8801712345678'],
            'formatted with plus and dashes'      => ['+880 1712-345678'],
            'double-zero international prefix'    => ['008801712345678'],
        ];
    }

    public function test_foreign_phone_numbers_are_left_untouched(): void
    {
        $this->assertSame(
            hash('sha256', '14155552671'),
            $this->prepareUserData(['phone' => '14155552671'])['ph'],
            'US number must not be prefixed with 88'
        );

        $this->assertSame(
            hash('sha256', '447911123456'),
            $this->prepareUserData(['phone' => '447911123456'])['ph'],
            'UK number must not be prefixed with 88'
        );
    }

    public function test_empty_phone_is_omitted_instead_of_hashing_blanks(): void
    {
        $this->assertArrayNotHasKey('ph', $this->prepareUserData(['phone' => '']));
    }

    public function test_email_is_lowercased_and_trimmed_before_hashing(): void
    {
        $prepared = $this->prepareUserData(['email' => '  Rahim.Uddin@Example.com  ']);

        $this->assertSame(hash('sha256', 'rahim.uddin@example.com'), $prepared['em']);
    }

    public function test_content_type_is_forwarded_to_custom_data(): void
    {
        $prepared = $this->prepareCustomData([
            'currency'     => 'BDT',
            'value'        => 1300,
            'content_ids'  => ['44', '77'],
            'content_type' => 'product',
        ]);

        $this->assertSame('product', $prepared['content_type']);
        $this->assertSame('BDT', $prepared['currency']);
        $this->assertSame(1300.0, $prepared['value']);
        $this->assertSame(['44', '77'], $prepared['content_ids']);
    }

    public function test_currency_defaults_to_bdt_when_missing(): void
    {
        $this->assertSame('BDT', $this->prepareCustomData(['value' => 500])['currency']);
    }
}
