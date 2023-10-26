<?php

namespace Tests\Unit;

use App\Models\FilterApplication;
use Tests\TestCase;

class FilterApplicationTest extends TestCase
{
    /**
     * Assert equality filter works returning some values.
     * @test
     */
    public function some_equal(): void
    {
        $object = [
            ['category' => 'Foo'],
            ['category' => 'Bar'],
            ['category' => 'Foo'],
        ];
        $filterApplication = new FilterApplication([
            'filterable' => $object,
            'inputValue' => 'Foo',
            'targetKey' => 'category',
            'operation' => 'isEqual'
        ]);
        $this->assertCount(2, $filterApplication->apply(), 'Asserting equality filter returns 2 items.');
    }
    /**
     * Assert equality filter works when it should be empty.
     * @test
     */
    public function empty_equal(): void
    {
        $object = [
            ['category' => 'Foo'],
            ['category' => 'Bar'],
            ['category' => 'Foo'],
        ];
        $filterApplication = new FilterApplication([
            'filterable' => $object,
            'inputValue' => 'Baz',
            'targetKey' => 'category',
            'operation' => 'isEqual'
        ]);
        $this->assertCount(0, $filterApplication->apply(), 'Asserting equality filter gives empty array.');
    }
    /**
     * Assert before filter works returning some values.
     * @test
     */
    public function some_dates(): void
    {
        $object = [
            ['date' => '2023-10-16T17:38:47Z'],
            ['date' => '2023-10-17T23:38:47Z'],
            ['date' => '2023-10-18T00:00:00Z'],
        ];
        $filterApplication = new FilterApplication([
            'filterable' => $object,
            'inputValue' => '2023-10-17T23:59:59Z',
            'targetKey' => 'date',
            'operation' => 'isBefore'
        ]);
        $this->assertCount(2, $filterApplication->apply(), 'Asserting before filter returns 2 items.');
    }
    /**
     * Assert before filter works when it should be empty.
     * @test
     */
    public function empty_dates(): void
    {
        $object = [
            ['date' => '2023-10-16T17:38:47Z'],
            ['date' => '2023-10-17T23:38:47Z'],
            ['date' => '2023-10-18T00:00:00Z'],
        ];
        $filterApplication = new FilterApplication([
            'filterable' => $object,
            'inputValue' => '2023-10-15T23:59:59Z',
            'targetKey' => 'date',
            'operation' => 'isBefore'
        ]);
        $this->assertCount(0, $filterApplication->apply(), 'Asserting before filter returns empty array.');
    }
}
