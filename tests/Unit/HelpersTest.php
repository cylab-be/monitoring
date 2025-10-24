<?php

namespace Tests\Unit;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    public function testReturnsFirstMatchGroup(): void
    {
        $result = preg_match_one('/(\d+)/', 'abc123def456');
        $this->assertSame('123', $result);
    }

    /** @test */
    public function testReturnsSpecificMatchGroup(): void
    {
        $result = preg_match_one('/([a-z]+)([0-9]+)/', 'abc123', 2);
        $this->assertSame('123', $result);
    }

    /** @test */
    public function testReturnsFalseWhenNoMatchFound(): void
    {
        $result = preg_match_one('/(\d+)/', 'abcdef');
        $this->assertFalse($result);
    }
}
