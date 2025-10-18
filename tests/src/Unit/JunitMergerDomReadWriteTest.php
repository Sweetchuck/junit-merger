<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use Sweetchuck\JunitMerger\JunitMergerDomReadWrite;
use Sweetchuck\JunitMerger\JunitMergerInterface;

#[CoversClass(JunitMergerDomReadWrite::class)]
class JunitMergerDomReadWriteTest extends JunitMergerTestBase
{

    protected function createInstance(): JunitMergerInterface
    {
        return new JunitMergerDomReadWrite();
    }

    /**
     * @return array<string, mixed>
     */
    public static function casesMergeXmlFiles(): array
    {
        $fixturesDir = static::getFixturesDir();
        $cases = parent::casesMergeXmlFiles();
        $cases['basic']['expected'] = file_get_contents("$fixturesDir/junit-expected/a-b-DocReadWrite.xml");
        $cases['merge-01-01'] = [
            'expected' => file_get_contents("$fixturesDir/junit-expected/merge-01-01.xml"),
            'xmlItems' => new \ArrayIterator([
                "$fixturesDir/junit/merge-01-01-01.xml",
                "$fixturesDir/junit/merge-01-01-02.xml",
            ]),
        ];

        return $cases;
    }
}
