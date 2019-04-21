<?php

// @loftDocs.title(Testing Classes with Service-Based Dependency Injection)

namespace AKlump\PHPUnit\Test\EasyMock;

use AKlump\PHPUnit\EasyMock;
use AKlump\PHPUnit\EasyMockTrait;

// @loftDocs.markdown(## Abstract Test Base Providing Services)
// @loftDocs.markdown(To be able to provide class arguments based on service ids, you must implement `getService` in your test class (or abstract parent class), which hooks into your app's [service container](https://symfony.com/doc/current/service_container.html).)

abstract class TestBase extends \PHPUnit_Framework_TestCase {

  use EasyMockTrait;

  static $lima, $november;

  /**
   * A contrived example of a service container retrieval.
   */
  public function getService($service_name) {
    switch ($service_name) {
      case 'my_app.lima':
        if (!static::$lima) {
          static::$lima = new Lima();
        }

        return static::$lima;

      case 'my_app.mike':
        // Non-shared: each call is a new instance.
        return new Mike();

      case 'my_app.november':
        if (!static::$november) {
          static::$november = new November();
        }

        return static::$november;
    }

    return NULL;
  }

}

// @loftDocs.markdown(## Here is the Test)

class ConstructorArgumentsUsingServiceIdsUnitTest extends TestBase {

  protected function getSchema() {
    return [
      'classToBeTested' => "AKlump\PHPUnit\Test\EasyMock\Kilo",

      // This is how we provide arguments to the class being tested using
      // service ids, in this example we have a service id 'my_app.lima'.
      // The order must match that of the constructor.
      'classArgumentsMap' => [

        'lima' => ['my_app.lima', EasyMock::SERVICE],

        // This is a shorthand form of the above; a string prefixed with '@'
        // means a shared service, where the id is 'my_app.november'.
        'november' => '@my_app.november',

        'mike' => ['my_app.mike', EasyMock::NON_SHARED_SERVICE],
      ],
    ];
  }

  public function testAbleToCallServiceMethodsAsExpectedViaDependencyGetter() {
    $this->assertSame('parrotlet', $this->obj->getLima()->getAnimal());
    $this->assertSame('zebra', $this->obj->getMike()->getAnimal());
    $this->assertSame('cat', $this->obj->getNovember()->getAnimal());
  }

  public function testConstructor() {

    // Use this assertion to quickly make sure constructor works as expected.
    $this->assertConstructorSetsInternalProperties();
  }

}

// @loftDocs.markdown(## Class Being Tested)

class Kilo {

  protected $lima, $mike, $november;

  public function __construct(Lima $lima, November $november, Mike $mike) {
    $this->lima = $lima;
    $this->mike = $mike;
    $this->november = $november;
  }

  public function getLima() {
    return $this->lima;
  }

  public function getMike() {
    return $this->mike;
  }

  public function getNovember() {
    return $this->november;
  }

}

// @loftDocs.markdown(## Dependency Injected Service Classes)

/**
 * Lima is registered in our app under the service id `my_app.lima`.
 */
class Lima {

  public function getAnimal() {
    return 'parrotlet';
  }

}

/**
 * November is registered in our app under the service id `my_app.november`.
 */
class November {

  public function getAnimal() {
    return 'cat';
  }

}

// @loftDocs.markdown(## Dependency Injected Non-Shared Service Class)
// @loftDocs.markdown(Learn more about [non-shared services](https://symfony.com/doc/current/service_container/shared.html).)

/**
 * Mike is registered in our app under the service id `my_app.mike`. It is a
 * non-shared service.
 */
class Mike {

  public function getAnimal() {
    return 'zebra';
  }

}

