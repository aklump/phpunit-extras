<?php

// @loftDocs.title(Testing Classes With a Constructor)

namespace AKlump\PHPUnit\Test\EasyMock;

use AKlump\PHPUnit\EasyMock;
use AKlump\PHPUnit\EasyMockTrait;
use PHPUnit\Framework\TestCase;

// @loftDocs.markdown(## Here is the Test)

class ConstructorArgumentsUnitTest extends TestCase {

  use EasyMockTrait;

  protected function getSchema() {
    return [
      'classToBeTested' => "AKlump\PHPUnit\Test\EasyMock\Bravo",

      // These are the __construct arguments for class Bravo.  Each element key
      // is the class property, the value is an array where: 0 = the value of
      // the argument, and 1 = indicates the type of argument is a value.
      // The order must match that of the constructor.
      'classArgumentsMap' => [
        'string' => ['aardvark', EasyMock::VALUE],
        'int' => [123, EasyMock::VALUE],
        'float' => [4.56, EasyMock::VALUE],
        'array' => [['big' => 'fish'], EasyMock::VALUE],
        'object' => [(object) ['small' => 'fry'], EasyMock::VALUE],
      ],
    ];
  }

  public function testGetValueReturnsExpectedConstructorValue() {
    $this->assertSame('aardvark', $this->obj->getValue('string'));
    $this->assertSame(123, $this->obj->getValue('int'));
    $this->assertSame(4.56, $this->obj->getValue('float'));
    $this->assertSame(['big' => 'fish'], $this->obj->getValue('array'));
    $this->assertEquals((object) ['small' => 'fry'], $this->obj->getValue('object'));
  }

  public function testConstructor() {

    // Use this assertion to quickly make sure constructor works as expected.
    $this->assertConstructorSetsInternalProperties();
  }

}

// @loftDocs.markdown(## Class Being Tested)

final class Bravo {

  protected $string, $int, $float, $array, $object;

  public function __construct($string, $int, $float, $array, $object) {
    $this->string = $string;
    $this->int = $int;
    $this->float = $float;
    $this->array = $array;
    $this->object = $object;
  }

  public function getValue($key) {
    return $this->{$key};
  }

}

