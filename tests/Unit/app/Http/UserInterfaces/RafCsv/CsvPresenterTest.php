<?php

namespace Tests\Unit\App\Http\UserInterfaces\RafCsv;

use App\Http\UserInterfaces\RafCsv\Get\CsvPresenter;
use stdClass;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\RafCsv\Get\UseCases\OutputData;
use Tests\TestCase;
use Traversable;

class CsvPresenterTest extends TestCase
{
    private $presenterOutput;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->presenterOutput = $this->prophesize(PresenterOutput::class);

        $this->target = new CsvPresenter($this->presenterOutput->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $expected = "header1\nheader2\n1\n2\n";

        $data = (object) [
            'body' => [
                [
                    'col' => 1,
                ],
                [
                    'col' => 2,
                ],
            ],
        ];
        $generator = new GeneratorMock();
        $output = new OutputData('TestDivision', 'TestShortStartString', 'TestShortEndString', [['header1'], ['header2']], [$generator, 'generator'], $data);

        $this->target->__invoke($output);

        $actual = ob_get_contents();

        $this->assertSame($expected, $actual);
    }
}

class GeneratorMock
{
    public function generator(stdClass $data): Traversable
    {
        yield $data->body;
    }
}
