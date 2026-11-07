<?php

namespace Tests\Unit;

use App\Sensor\ColorPalette;

use Tests\TestCase;

/**
 * Description of ColorPaletteTest
 *
 * @author tibo
 */
class ColorPaletteTest extends TestCase
{
    public function testPick1ColorReturnsPairedColor()
    {
        $this->assertEquals("#d6e6ff", ColorPalette::pick1Color(0));
        $this->assertEquals("#d7f9f8", ColorPalette::pick1Color(1));
        $this->assertEquals("#e5d4ef", ColorPalette::pick1Color(5));
    }

    public function testPick1ColorWrapsAround()
    {
        $this->assertEquals(ColorPalette::pick1Color(0), ColorPalette::pick1Color(6));
        $this->assertEquals(ColorPalette::pick1Color(1), ColorPalette::pick1Color(7));
    }

    public function testPick2ColorsReturnsAdjacentColors()
    {
        $this->assertEquals(["#d6e6ff", "#d7f9f8"], ColorPalette::pick2Colors(0));
        $this->assertEquals(["#fbe0e0", "#e5d4ef"], ColorPalette::pick2Colors(2));
        // key 3 wraps around the palette
        $this->assertEquals(["#d6e6ff", "#d7f9f8"], ColorPalette::pick2Colors(3));
    }

    public function testNextCyclesThroughPalette()
    {
        $palette = new ColorPalette();

        $this->assertEquals("#d6e6ff", $palette->next());
        $this->assertEquals("#d7f9f8", $palette->next());
        $this->assertEquals("#ffffea", $palette->next());
        // after 6 calls we come back to the first color
        $palette->next();
        $palette->next();
        $palette->next();
        $this->assertEquals("#d6e6ff", $palette->next());
    }

    public function testLightenShortHex()
    {
        // ratio 0 must return the same color (expanded to 6 digits)
        $this->assertEquals("#ffffff", ColorPalette::lighten("#fff", 0));
        $this->assertEquals("#000000", ColorPalette::lighten("#000", 0));
    }

    public function testLightenLongHex()
    {
        $this->assertEquals("#008080", ColorPalette::lighten("#008080", 0));
        // ratio 1 doubles each channel, capped at 255
        $this->assertEquals("#ffffff", ColorPalette::lighten("#808080", 1));
        // zero channels stay zero
        $this->assertEquals("#00ff00", ColorPalette::lighten("#00ff00", 0.5));
    }

    public function testLightenRejectsInvalidColor()
    {
        $this->expectException(\Exception::class);
        ColorPalette::lighten("not-a-color", 0.5);
    }
}
