<?php

namespace Tests\Unit;

use App\Icon;

use Tests\TestCase;

/**
 * Description of IconTest
 *
 * @author tibo
 */
class IconTest extends TestCase
{
    public function testDefaultIcon()
    {
        $icon = new Icon(null);

        $this->assertEquals("server.png", $icon->name());
        $this->assertEquals("/images/icons/server.png", $icon->url());
    }

    public function testCustomIcon()
    {
        $icon = new Icon("switch.png");

        $this->assertEquals("switch.png", $icon->name());
        $this->assertEquals("/images/icons/switch.png", $icon->url());
    }

    public function testRejectsPathTraversal()
    {
        $invalid = [
            "../evil.png",
            "sub/dir.png",
            "dir\\file.png",
            "evil\0icon.png",
        ];

        foreach ($invalid as $name) {
            $this->expectException(\InvalidArgumentException::class);
            new Icon($name);
        }
    }

    public function testAllListsAvailableIcons()
    {
        $icons = Icon::all();

        $this->assertIsArray($icons);
        $this->assertNotEmpty($icons);

        $names = array_map(fn(Icon $icon) => $icon->name(), $icons);
        $this->assertContains("server.png", $names);

        foreach ($icons as $icon) {
            $this->assertStringNotContainsString("/", $icon->name());
        }
    }
}
