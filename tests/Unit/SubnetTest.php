<?php

namespace Tests\Unit;

use App\Subnet;

use Tests\TestCase;

/**
 * Description of SubnetTest
 *
 * @author tibo
 */
class SubnetTest extends TestCase
{
    private function subnet(string $address, int $mask) : Subnet
    {
        $subnet = new Subnet();
        $subnet->address = $address;
        $subnet->mask = $mask;
        return $subnet;
    }

    public function testHasAddressOnClassC()
    {
        $subnet = $this->subnet("192.168.1.0", 24);

        $this->assertTrue($subnet->hasAddress("192.168.1.50"));
        $this->assertTrue($subnet->hasAddress("192.168.1.0"));
        $this->assertTrue($subnet->hasAddress("192.168.1.255"));
        $this->assertFalse($subnet->hasAddress("192.168.2.50"));
    }

    public function testHasAddressOnClassA()
    {
        $subnet = $this->subnet("10.0.0.0", 8);

        $this->assertTrue($subnet->hasAddress("10.0.0.1"));
        $this->assertTrue($subnet->hasAddress("10.255.255.254"));
        $this->assertFalse($subnet->hasAddress("11.0.0.0"));
    }

    public function testHasAddressOnClassB()
    {
        $subnet = $this->subnet("172.16.0.0", 16);

        $this->assertTrue($subnet->hasAddress("172.16.255.254"));
        $this->assertFalse($subnet->hasAddress("172.17.0.1"));
    }

    public function testHasAddressOnHost()
    {
        $subnet = $this->subnet("8.8.8.8", 32);

        $this->assertTrue($subnet->hasAddress("8.8.8.8"));
        $this->assertFalse($subnet->hasAddress("8.8.8.9"));
    }

    public function testDefaultColor()
    {
        $subnet = new Subnet();

        $this->assertEquals("#007bff", $subnet->color());
    }

    public function testCustomColorFromProperties()
    {
        $subnet = new Subnet();
        $subnet->properties = ["color" => "#123456"];

        $this->assertEquals("#123456", $subnet->color());
    }

    public function testCytoId()
    {
        $subnet = new Subnet();
        $subnet->id = 42;

        $this->assertEquals("#subnet-42", $subnet->cytoId());
    }
}
