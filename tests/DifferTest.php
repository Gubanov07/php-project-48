<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function App\Differ\genDiff;

class DifferTest extends TestCase
{
  public function testGenDiffNested()
  {
      $expected = $this->getExpectedDiff();
      
      // Test JSON files
      $actualJson = genDiff(
          __DIR__ . '/fixtures/file1.json',
          __DIR__ . '/fixtures/file2.json'
      );
      $this->assertEquals($expected, $actualJson);

      // Test YAML files
      $actualYaml = genDiff(
          __DIR__ . '/fixtures/file1.yaml',
          __DIR__ . '/fixtures/file2.yaml'
      );
      $this->assertEquals($expected, $actualYaml);

      $actualYml = genDiff(
          __DIR__ . '/fixtures/file1.yml',
          __DIR__ . '/fixtures/file2.yml'
      );
      $this->assertEquals($expected, $actualYml);

      // Test mixed formats
      $actualMixed1 = genDiff(
          __DIR__ . '/fixtures/file1.json',
          __DIR__ . '/fixtures/file2.yaml'
      );
      $this->assertEquals($expected, $actualMixed1);

      $actualMixed2 = genDiff(
          __DIR__ . '/fixtures/file1.yaml',
          __DIR__ . '/fixtures/file2.json'
      );
      $this->assertEquals($expected, $actualMixed2); 
  }

  private function getExpectedDiff(): string
  {
      return <<<EOF
  {
      common: {
        + follow: false
          setting1: Value 1
        - setting2: 200
        - setting3: true
        + setting3: null
        + setting4: blah blah
        + setting5: {
              key5: value5
          }
          setting6: {
              doge: {
                - wow: 
                + wow: so much
              }
              key: value
            + ops: vops
          }
      }
      group1: {
        - baz: bas
        + baz: bars
          foo: bar
        - nest: {
              key: value
          }
        + nest: str
      }
    - group2: {
          abc: 12345
          deep: {
              id: 45
          }
      }
    + group3: {
          deep: {
              id: {
                  number: 45
              }
          }
          fee: 100500
      }
  }
  EOF;
  }
}
