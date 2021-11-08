<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger\Tests\Unit;

use Sweetchuck\JunitMerger\JunitMergerDomReadWrite;
use Sweetchuck\JunitMerger\JunitMergerInterface;

/**
 * @covers \Sweetchuck\JunitMerger\JunitMergerDomReadWrite<extended>
 */
class JunitMergerDomReadWriteTest extends JunitMergerTestBase
{

    protected function createInstance(): JunitMergerInterface
    {
        return new JunitMergerDomReadWrite();
    }

    public function casesMergeXmlFiles(): array
    {
        $fixturesDir = codecept_data_dir('fixtures');
        $cases = parent::casesMergeXmlFiles();
        $cases['basic'][0] = file_get_contents("$fixturesDir/junit-expected/a-b-DocReadWrite.xml");
        $cases['merge-01-01'] = [
            file_get_contents("$fixturesDir/junit-expected/merge-01-01.xml"),
            new \ArrayIterator([
                "$fixturesDir/junit/merge-01-01-01.xml",
                "$fixturesDir/junit/merge-01-01-02.xml",
            ]),
        ];

        return $cases;
    }
}
