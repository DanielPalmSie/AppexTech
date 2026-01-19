<?php

declare(strict_types=1);

namespace App\Tests;

use App\Catalog;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Catalog
 */
final class CatalogTest extends TestCase
{
    public function testPricesReturnsCatalogMap(): void
    {
        $catalog = new Catalog(['TS1' => 123]);

        $this->assertSame(['TS1' => 123], $catalog->prices());
    }
}
