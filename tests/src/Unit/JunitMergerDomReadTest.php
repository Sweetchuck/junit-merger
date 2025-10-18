<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use Sweetchuck\JunitMerger\JunitMergerDomRead;
use Sweetchuck\JunitMerger\JunitMergerInterface;

#[CoversClass(JunitMergerDomRead::class)]
class JunitMergerDomReadTest extends JunitMergerTestBase
{

    protected function createInstance(): JunitMergerInterface
    {
        return new JunitMergerDomRead();
    }
}
