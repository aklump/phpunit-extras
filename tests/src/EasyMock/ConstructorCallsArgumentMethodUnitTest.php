<?php
// @loftDocs.id(constructor-calls-argument-method)
// @loftDocs.title(Testing Classes that Call an Argument Method in Constructor)

namespace AKlump\PHPUnit\Test\EasyMock;

use AKlump\PHPUnit\EasyMockTrait;

// @loftDocs.markdown(## Here is the Test)

class ConstructorCallsArgumentMethodUnitTest extends \PHPUnit_Framework_TestCase {

  use EasyMockTrait;

  protected function getSchema() {
    return [
      'classToBeTested' => '\AKlump\PHPUnit\Test\EasyMock\Oscar',
      'classArgumentsMap' => [

        // We can manually create a mocked object with a pre-defined expectation
        // so that the constructor is able to call the expectation method, when
        // we use a callable like this:
        'pappa' => function () {
          $mock = \Mockery::mock('\AKlump\PHPUnit\Test\EasyMock\Pappa');
          $mock->allows('setDate');

          return $mock;
        },
      ],
    ];
  }

  public function testConstructor() {
    $this->assertConstructorSetsInternalProperties();
  }
}

// @loftDocs.markdown(## Class Being Tested)

class Oscar {

  private $pappa;

  public function __construct(Pappa $pappa) {
    $this->pappa = $pappa;
    $this->pappa->setDate(date_create());
  }

}

// @loftDocs.markdown(## Class With a Method That Gets Called in Oscar::__construct)

class Pappa {

  private $date;

  public function setDate(\DateTime $date) {
    $this->date = $date;
  }

}
