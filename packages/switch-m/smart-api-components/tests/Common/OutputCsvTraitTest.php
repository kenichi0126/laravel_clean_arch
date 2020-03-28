<?php

namespace Switchm\SmartApi\Components\Tests\Common;

use stdClass;
use Switchm\SmartApi\Components\Common\OutputCsvTrait;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputCsvTraitTest extends TestCase
{
    use OutputCsvTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function outputCsv_test(): void
    {
        $expected = "header\nbody\n";

        ob_start();
        $this->outputCsv('test.csv', ['header' => ['header']], ['body' => ['body']], true)->send();
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function outputCsvGenerator_test(): void
    {
        $expected = "header\ntest1\ntest2\n";

        $dummyClass = new class() {
            public function csvGenerator(stdClass $data)
            {
                return $data;
            }
        };

        $generator = [$dummyClass, 'csvGenerator'];

        $data = (object) ['test' => [['test1'], ['test2']]];

        ob_start();
        $this->outputCsvGenerator('test.csv', ['header' => ['header']], $generator, $data)->send();
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expected, $actual);
    }
}
