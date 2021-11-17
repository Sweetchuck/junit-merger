<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger\Tests\Unit;

use Codeception\Test\Unit;
use Sweetchuck\JunitMerger\JunitMergerInterface;
use Symfony\Component\Console\Output\BufferedOutput;

abstract class JunitMergerTestBase extends Unit
{

    public function casesMergeXmlFiles(): array
    {
        $fixturesDir = codecept_data_dir('fixtures');

        return [
            'basic' => [
                file_get_contents("$fixturesDir/junit-expected/a-b.xml"),
                new \ArrayIterator([
                    new \SplFileObject("$fixturesDir/junit/a.xml"),
                    "$fixturesDir/junit/empty-long-new-line.xml",
                    "$fixturesDir/junit/empty-long-same-line.xml",
                    "$fixturesDir/junit/empty-short-space.xml",
                    "$fixturesDir/junit/empty-short-tight.xml",
                    "$fixturesDir/junit/b.xml",
                ]),
            ],
        ];
    }

    public function casesMergeXmlStrings(): array
    {
        $cases = $this->casesMergeXmlFiles();
        foreach ($cases as &$case) {
            $strings = [];
            /** @var string|\SplFileInfo $filename */
            foreach ($case[1] as $filename) {
                $strings[] = file_get_contents(is_string($filename) ? $filename : $filename->getPathname());
            }
            $case[1] = new \ArrayIterator($strings);
        }

        return $cases;
    }

    /**
     * @dataProvider casesMergeXmlFiles
     */
    public function testMergeXmlFiles(string $expected, \Iterator $xmlFiles)
    {
        $merger = $this->createInstance();

        $output = new BufferedOutput();
        $merger->mergeXmlFiles($xmlFiles, $output);

        $eXml = new \DOMDocument();
        $eXml->formatOutput = true;
        $eXml->preserveWhiteSpace = false;
        $eXml->loadXML($expected);

        $aXml = new \DOMDocument();
        $aXml->formatOutput = true;
        $aXml->preserveWhiteSpace = false;
        $aXml->loadXML($output->fetch());

        $this->assertSame($eXml->saveXML(), $aXml->saveXML());
    }

    /**
     * @dataProvider casesMergeXmlStrings
     */
    public function testMergeXmlStrings(string $expected, \Iterator $xmlStrings)
    {
        $merger = $this->createInstance();

        $output = new BufferedOutput();
        $merger->mergeXmlStrings($xmlStrings, $output);

        $eXml = new \DOMDocument();
        $eXml->formatOutput = true;
        $eXml->preserveWhiteSpace = false;
        $eXml->loadXML($expected);

        $aXml = new \DOMDocument();
        $aXml->formatOutput = true;
        $aXml->preserveWhiteSpace = false;
        $aXml->loadXML($output->fetch());

        $this->assertSame($eXml->saveXML(), $aXml->saveXML());
    }

    abstract protected function createInstance(): JunitMergerInterface;
}
