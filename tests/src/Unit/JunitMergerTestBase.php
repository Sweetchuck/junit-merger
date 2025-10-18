<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sweetchuck\JunitMerger\JunitMergerInterface;
use Symfony\Component\Console\Output\BufferedOutput;

abstract class JunitMergerTestBase extends TestCase
{
    protected static function getProjectRootDir(): string
    {
        return __DIR__ . '/../../..';
    }

    protected static function getFixturesDir(): string
    {
        return static::getProjectRootDir() . '/tests/fixtures';
    }

    abstract protected function createInstance(): JunitMergerInterface;

    /**
     * @return array<string, mixed>
     */
    public static function casesMergeXmlFiles(): array
    {
        $fixturesDir = static::getFixturesDir();

        return [
            'basic' => [
                'expected' => file_get_contents("$fixturesDir/junit-expected/a-b.xml"),
                'xmlItems' => new \ArrayIterator([
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

    /**
     * @return array<string, mixed>
     */
    public static function casesMergeXmlStrings(): array
    {
        $cases = static::casesMergeXmlFiles();
        foreach ($cases as &$case) {
            $strings = [];
            /** @var string|\SplFileInfo $filename */
            foreach ($case['xmlItems'] as $filename) {
                $strings[] = file_get_contents(is_string($filename) ? $filename : $filename->getPathname());
            }
            $case['xmlItems'] = new \ArrayIterator($strings);
        }

        return $cases;
    }

    #[DataProvider('casesMergeXmlFiles')]
    public function testMergeXmlFiles(string $expected, \Iterator $xmlItems): void
    {
        $merger = $this->createInstance();

        $output = new BufferedOutput();
        $merger->mergeXmlFiles($xmlItems, $output);

        $eXml = new \DOMDocument();
        $eXml->formatOutput = true;
        $eXml->preserveWhiteSpace = false;
        $eXml->loadXML($expected);

        $aXml = new \DOMDocument();
        $aXml->formatOutput = true;
        $aXml->preserveWhiteSpace = false;
        $aXml->loadXML($output->fetch());

        static::assertSame($eXml->saveXML(), $aXml->saveXML());
    }

    #[DataProvider('casesMergeXmlStrings')]
    public function testMergeXmlStrings(string $expected, \Iterator $xmlItems): void
    {
        $merger = $this->createInstance();

        $output = new BufferedOutput();
        $merger->mergeXmlStrings($xmlItems, $output);

        $eXml = new \DOMDocument();
        $eXml->formatOutput = true;
        $eXml->preserveWhiteSpace = false;
        $eXml->loadXML($expected);

        $aXml = new \DOMDocument();
        $aXml->formatOutput = true;
        $aXml->preserveWhiteSpace = false;
        $aXml->loadXML($output->fetch());

        static::assertSame($eXml->saveXML(), $aXml->saveXML());
    }
}
