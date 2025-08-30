<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function App\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiffFlatJson()
    {
        $expected = <<<EOF
{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}
EOF;

        $actual = genDiff(
            __DIR__ . '/fixtures/file1.json',
            __DIR__ . '/fixtures/file2.json'
        );

        $this->assertEquals($expected, $actual);
    }
}
