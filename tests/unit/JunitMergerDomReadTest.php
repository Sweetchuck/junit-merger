<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger\Tests\Unit;

use Sweetchuck\JunitMerger\JunitMergerDomRead;
use Sweetchuck\JunitMerger\JunitMergerInterface;

/**
 * @covers \Sweetchuck\JunitMerger\JunitMergerDomRead<extended>
 */
class JunitMergerDomReadTest extends JunitMergerTestBase
{

    protected function createInstance(): JunitMergerInterface
    {
        return new JunitMergerDomRead();
    }
}
