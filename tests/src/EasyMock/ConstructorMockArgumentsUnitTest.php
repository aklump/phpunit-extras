<?php

// @loftDocs.title(Testing Classes With Dependency Injection)

namespace AKlump\PHPUnit\Test\EasyMock;

use AKlump\PHPUnit\EasyMock;
use AKlump\PHPUnit\EasyMockTrait;

// @loftDocs.markdown(## Here is the Test)
// @loftDocs.markdown(The test class will have the following properties setted automatically `$this->obj` and `$this->args` to be used by your methods.)

class ConstructorMockArgumentsUnitTest extends \PHPUnit_Framework_TestCase {

  use EasyMockTrait;

  protected function getSchema() {
    return [
      'classToBeTested' => "AKlump\PHPUnit\Test\EasyMock\Hotel",

      // These are the constructor arguments for Hotel, we are going to set up
      // india as a full mock, and juliette as a partial mock.
      // The order must match that of the constructor.
      'classArgumentsMap' => [

        'india' => '\AKlump\PHPUnit\Test\EasyMock\India',

        'juliette' => [
          '\AKlump\PHPUnit\Test\EasyMock\Juliette',
          EasyMock::PARTIAL,
        ],
      ],
    ];
  }

  /**
   * @expectedException \Mockery\Exception\BadMethodCallException
   */
  public function testFullMockWithoutExpectationThrows() {
    $this->assertSame(India::class, $this->obj->getIndiaName());
  }

  public function testFullMockWithExpectationReturnsExpectation() {
    $this->args->india->allows('getName')->andReturns('David');
    $this->assertSame('David', $this->obj->getIndiaName());
  }

  public function testPartialMockCallsDefinedMethod() {
    $this->assertSame(Juliette::class, $this->obj->getJulietteName());
  }

  public function testPartialMockWithExpectationReturnsExpectation() {
    $this->args->juliette->allows('getName')->andReturns('Caesar');
    $this->assertSame('Caesar', $this->obj->getJulietteName());
  }

  public function testConstructor() {

    // Use this assertion to quickly make sure constructor works as expected.
    $this->assertConstructorSetsInternalProperties();
  }

}

// @loftDocs.markdown(## Class Being Tested)

class Hotel {

  protected $india, $juliette;

  public function __construct(India $india, Juliette $juliette) {
    $this->india = $india;
    $this->juliette = $juliette;
  }

  public function getIndiaName() {
    return $this->india->getName();
  }

  public function getJulietteName() {
    return $this->juliette->getName();
  }

}

// @loftDocs.markdown(## Fully Mocked Class)

class India {

  public function getName() {
    return __CLASS__;
  }

}

// @loftDocs.markdown(## Partial Mocked Class)

class Juliette {

  public function getName() {
    return __CLASS__;
  }

}
