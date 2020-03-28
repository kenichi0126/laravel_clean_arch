<?php

namespace Tests\Unit\App\Http\UserInterfaces;

use Illuminate\Validation\Factory;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Tests\TestCase as BaseTestCase;

abstract class ValidationTestCase extends BaseTestCase
{
    /**
     * @var Factory
     */
    protected $validator;

    /**
     * @var FormRequest
     */
    protected $target;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = $this->app->validator;
    }

    /**
     * @test
     * @dataProvider  dataValidationSuccess
     * @param array $inputs
     */
    public function validationSuccess(array $inputs): void
    {
        $validator = $this->validator->make($inputs, $this->target->rules());

        $expected = [];

        $actual = $validator->messages()->all();

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    abstract public function dataValidationSuccess(): array;

    /**
     * @test
     * @dataProvider  dataValidationError
     * @param string $target
     * @param array $inputs
     * @param mixed $expected
     */
    public function validationError(string $target, array $inputs, $expected): void
    {
        $validator = $this->validator->make($inputs, $this->target->rules(), $this->target->messages(), $this->target->attributes());

        $messages = $validator->messages()->get($target);

        $actual = $messages;

        if (in_array($expected, $messages, true)) {
            $actual = $messages[array_search($expected, $messages)];
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    abstract public function dataValidationError(): array;

    /**
     * @test
     */
    public function validationErrorCountCheck(): void
    {
        $rules = array_map(function ($rules) {
            if (is_object($rules)) {
                $rules = get_class($rules);
            }

            $rules = explode('|', $rules);

            if (($key = array_search('nullable', $rules)) !== false) {
                unset($rules[$key]);
            }

            return $rules;
        }, $this->target->rules());

        $expected = count(\Arr::flatten($rules));

        $actual = count($this->dataValidationError());

        $this->assertGreaterThanOrEqual($expected, $actual);
    }
}
