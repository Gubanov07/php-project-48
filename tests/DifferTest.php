<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function getFullPath($fixtureName): string
    {
        return __DIR__ . '/fixtures/' . $fixtureName;
    }

    public static function additionProvider(): array
    {
        return [
            ['json'],
            ['yaml'],
            ['yml']
        ];
    }

    /**
    * @dataProvider additionProvider
    */
    public function testDefaultFormat($formatInput): void
    {
        $diff = genDiff($this->getFullPath("file1.$formatInput"), $this->getFullPath("file2.$formatInput"));
        $this->assertStringEqualsFile($this->getFullPath('TestsStylish.txt'), $diff);
    }

    /**
    * @dataProvider additionProvider
    */
    public function testStylishFormat($formatInput): void
    {
        $format = "stylish";
        $diff = genDiff($this->getFullPath("file1.$formatInput"), $this->getFullPath("file2.$formatInput"), $format);
        $this->assertStringEqualsFile($this->getFullPath('TestsStylish.txt'), $diff);
    }

    /**
    * @dataProvider additionProvider
    */
    public function testPlainFormat($formatInput): void
    {
        $format = "plain";
        $diff = genDiff($this->getFullPath("file1.$formatInput"), $this->getFullPath("file2.$formatInput"), $format);
        $this->assertStringEqualsFile($this->getFullPath('TestsPlain.txt'), $diff);
    }

    /**
    * @dataProvider additionProvider
    */
    public function testJsonFormat($formatInput): void
    {
        $format = "json";
        $diff = genDiff($this->getFullPath("file1.$formatInput"), $this->getFullPath("file2.$formatInput"), $format);
        $this->assertStringEqualsFile($this->getFullPath('TestsJson.txt'), $diff);
    }
}
