<?php

namespace Tests\Unit;

use App\HasStatus;
use App\Status;

use Illuminate\Support\Collection;

use Tests\TestCase;

/**
 * Description of StatusTest
 *
 * @author tibo
 */
class StatusTest extends TestCase
{
    private function item(int $code) : HasStatus
    {
        return new class($code) implements HasStatus {
            private $code;

            public function __construct(int $code)
            {
                $this->code = $code;
            }

            public function status() : Status
            {
                return new Status($this->code);
            }
        };
    }

    public function testOk()
    {
        $status = Status::ok();

        $this->assertEquals(0, $status->code());
        $this->assertEquals("OK", (string) $status);
        $this->assertEquals('success', $status->color());
        $this->assertStringContainsString('badge-success', $status->badge());
        $this->assertEquals($status->badge(), $status->badgeIfExists());
    }

    public function testWarning()
    {
        $status = Status::warning();

        $this->assertEquals(10, $status->code());
        $this->assertEquals("WARNING", (string) $status);
        $this->assertEquals('warning', $status->color());
        $this->assertStringContainsString('badge-warning', $status->badge());
    }

    public function testError()
    {
        $status = Status::error();

        $this->assertEquals(20, $status->code());
        $this->assertEquals("ERROR", (string) $status);
        $this->assertEquals('danger', $status->color());
        $this->assertStringContainsString('badge-danger', $status->badge());
    }

    public function testUnknown()
    {
        $status = Status::unknown();

        $this->assertEquals(-1, $status->code());
        $this->assertEquals("Unknown", (string) $status);
        $this->assertEquals('secondary', $status->color());
        // badgeIfExists must hide the badge for unknown statuses
        $this->assertEquals("", $status->badgeIfExists());
    }

    public function testJsonSerialize()
    {
        $status = Status::warning();

        $this->assertEquals(
            (object) ["code" => 10, "name" => "WARNING", "color" => "warning"],
            $status->jsonSerialize()
        );
    }

    public function testMaxOfArray()
    {
        $this->assertEquals(Status::warning(), Status::max([
            $this->item(Status::OK),
            $this->item(Status::WARNING),
        ]));

        $this->assertEquals(Status::error(), Status::max([
            $this->item(Status::OK),
            $this->item(Status::ERROR),
            $this->item(Status::WARNING),
        ]));
    }

    public function testMaxOfEmptyArrayIsUnknown()
    {
        $this->assertEquals(Status::unknown(), Status::max([]));
    }

    public function testMaxOfCollection()
    {
        $this->assertEquals(Status::warning(), Status::max(Collection::make([
            $this->item(Status::OK),
            $this->item(Status::WARNING),
        ])));
    }

    public function testMaxOfEmptyCollectionIsUnknown()
    {
        $this->assertEquals(Status::unknown(), Status::max(new Collection()));
    }
}
