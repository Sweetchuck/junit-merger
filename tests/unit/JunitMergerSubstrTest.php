<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger\Tests\Unit;

use Sweetchuck\JunitMerger\JunitMergerInterface;
use Sweetchuck\JunitMerger\JunitMergerSubstr;

/**
 * @covers \Sweetchuck\JunitMerger\JunitMergerSubstr<extended>
 */
class JunitMergerSubstrTest extends JunitMergerTestBase
{

    /**
     * @return \Sweetchuck\JunitMerger\JunitMergerSubstr
     */
    protected function createInstance(): JunitMergerInterface
    {
        return new JunitMergerSubstr();
    }

    public function casesDetectHeaderFooterLength()
    {
        $dir = codecept_data_dir('fixtures/junit');
        $cases = [
            'a.xml' => [
                [
                    'headerLength' => 52,
                    'footerLength' => 14,
                ],
            ],
            'empty-long-new-line.xml' => [
                [
                    'headerLength' => 52,
                    'footerLength' => 14
                ],
            ],
            'empty-long-same-line.xml' => [
                [
                    'headerLength' => 52,
                    'footerLength' => 14,
                ],
            ],
            'empty-short-space.xml' => [
                [
                    'headerLength' => 54,
                    'footerLength' => 15,
                ],
            ],
            'empty-short-tight.xml' => [
                [
                    'headerLength' => 53,
                    'footerLength' => 14,
                ],
            ],
        ];
        foreach (array_keys($cases) as $filename) {
            $cases[$filename][1] = file_get_contents("$dir/$filename");
        }

        return $cases;
    }

    /**
     * @dataProvider casesDetectHeaderFooterLength
     */
    public function testDetectHeaderFooterLength(array $expected, string $xmlString)
    {
        $merger = $this->createInstance();

        $merger->detectHeaderLength($xmlString);
        $this->assertSame($expected['headerLength'], $merger->getHeaderLength(), 'headerLength');

        $merger->detectFooterLength($xmlString);
        $this->assertSame($expected['footerLength'], $merger->getFooterLength(), 'footerLength');
    }
}
