<?php

namespace Tests;

use DutchCodingCompany\CsvCollection\CsvCollection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        mkdir(__DIR__ . '/temporary');
    }

    /**
     * @test
     * @return void
     */
    public function testCanOpenWithoutHeader(): void
    {
        $collection = CsvCollection::make()->options(['header' => false])
            ->open(__DIR__ . '/resources/without-header.csv');

        $this->assertSame($collection->first(), [
            0 => '1',
            1 => 'Wait',
            2 => 'Blampy',
            3 => 'wblampy0@addtoany.com',
            4 => 'Male',
            5 => '93.96.91.96',
        ]);

        $this->assertSame(10, $collection->count());
    }

    /**
     * @test
     * @return void
     */
    public function testCanOpenWithHeader(): void
    {
        $collection = CsvCollection::make()
            ->open(__DIR__ . '/resources/with-header.csv');

        $this->assertSame($collection->first(), [
            'id' => '1',
            'first_name' => 'Dolley',
            'last_name' => 'Songer',
            'email' => 'dsonger0@businessweek.com',
            'gender' => 'Female',
            'ip_address' => '120.107.3.146',
        ]);

        $this->assertSame(10, $collection->count());
    }

    /**
     * @test
     * @return void
     */
    public function testCanSaveWithoutHeader(): void
    {
        $path = __DIR__ . '/temporary/save-without-header.csv';

        $data = [
            'Wait',
            'Blampy',
        ];

        $collection = CsvCollection::make([$data])
            ->options(['header' => false])
            ->save($path)
            ->open($path);

        $this->assertSame($collection->first(), $data);
        $this->assertSame(1, $collection->count());

        unlink($path);
    }

    /**
     * @test
     * @return void
     */
    public function testCanSaveWithHeader(): void
    {
        $path = __DIR__ . '/temporary/save-with-header.csv';

        $data = [
            'first_name' => 'Wait',
            'last_name' => 'Blampy',
        ];

        $collection = CsvCollection::make([$data])
            ->options(['header' => true])
            ->save($path)
            ->open($path);

        $this->assertSame($collection->first(), $data);
        $this->assertSame(1, $collection->count());

        unlink($path);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        rmdir(__DIR__ . '/temporary');

        parent::tearDown();
    }
}
