<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\ProductController;
use Tests\TestCase;

/**
 * Product admin-এর Size Chart নরমালাইজেশন।
 *
 * মূল ঝুঁকি: Product মডেলে 'size_chart' => 'array' cast আছে। Laravel-এর
 * setAttribute() json-castable অ্যাট্রিবিটে castAttributeAsJson() (Json::encode)
 * চালায়, তাই এখান থেকে json_encode করা স্ট্রিং রিটার্ন করলে ডেটাবেসে
 * ডাবল-এনকোডেড মান বসত এবং পড়ার সময় অ্যারের বদলে স্ট্রিং ফিরত —
 * ফলে ল্যান্ডিং পেজের Size Chart সেকশন আর দেখাত না।
 */
class ProductSizeChartTest extends TestCase
{
    protected function normalize($rows)
    {
        $method = new \ReflectionMethod(ProductController::class, 'normalizeSizeChart');

        return $method->invoke(
            (new \ReflectionClass(ProductController::class))->newInstanceWithoutConstructor(),
            $rows
        );
    }

    public function test_returns_an_array_so_the_model_cast_can_encode_it_once(): void
    {
        $result = $this->normalize([
            ['size' => 'M', 'chest' => '38', 'length' => '27'],
        ]);

        $this->assertIsArray($result, 'Must return an array; a pre-encoded JSON string would be double-encoded by the model cast.');
        $this->assertSame(['size' => 'M', 'chest' => '38', 'length' => '27'], $result[0]);
    }

    public function test_rows_without_a_size_are_dropped(): void
    {
        $result = $this->normalize([
            ['size' => 'M', 'chest' => '38', 'length' => '27'],
            ['size' => '', 'chest' => '40', 'length' => '28'],
            ['size' => '   ', 'chest' => '42', 'length' => '29'],
            'not an array',
        ]);

        $this->assertCount(1, $result);
        $this->assertSame('M', $result[0]['size']);
    }

    public function test_values_are_trimmed_and_missing_measurements_become_empty_strings(): void
    {
        $result = $this->normalize([
            ['size' => '  XXL  ', 'chest' => ' 44 ', 'length' => null],
        ]);

        $this->assertSame(
            ['size' => 'XXL', 'chest' => '44', 'length' => ''],
            $result[0]
        );
    }

    /**
     * @dataProvider unusableInputProvider
     */
    public function test_returns_null_when_there_is_nothing_usable($input): void
    {
        $this->assertNull($this->normalize($input));
    }

    public static function unusableInputProvider(): array
    {
        return [
            'null'            => [null],
            'not an array'    => ['M,38,27'],
            'empty array'     => [[]],
            'only blank rows' => [[['size' => '', 'chest' => '', 'length' => '']]],
        ];
    }

    public function test_result_round_trips_through_json_exactly_once(): void
    {
        $rows = [
            ['size' => 'M', 'chest' => '38', 'length' => '27'],
            ['size' => 'L', 'chest' => '40', 'length' => '28'],
        ];

        // মডেল যা করে তার অনুকরণ: একবার encode, একবার decode।
        $stored = json_encode($this->normalize($rows));
        $readBack = json_decode($stored, true);

        $this->assertSame($rows, $readBack);
    }
}
