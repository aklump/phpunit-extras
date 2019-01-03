<?php

namespace AKlump\PHPUnit;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Abstract test class to easily test objects with auto mocking.
 */
abstract class EasyMockTestBase extends \PHPUnit_Framework_TestCase {

  use MockeryPHPUnitIntegration;

  /**
   * A flag to indicate a full mocked object.
   *
   * This is the default.
   *
   * @var int
   */
  const FULL_MOCK = 0;

  /**
   * A Flag to indicate a partial mock should be created.
   *
   * Used for element values as arrays on the classArgumentsMap config.
   *
   * @var int
   */
  const PARTIAL_MOCK = 1;

  /**
   * A flag to indicate the argument value is not a classname but a value.
   *
   * Use this when a constructor argument is not a class instance.
   *
   * @var int
   */
  const VALUE = 2;

  /**
   * An instance of the class being tested.
   *
   * @var mixed
   */
  protected $obj;

  /**
   * All mocked objects from $argsMap.
   *
   * @var object
   */
  protected $args;

  /**
   * Return an instance of a service by name.
   *
   * To support services in your tests, you must implement this method to
   * return class instances for those services you require for your tests.
   *
   * @param string $service_name
   *   The service name will begin with an '@', e.g. "@database".
   *
   * @return null|mixed
   *   A service class instance or null.
   */
  public function getService($service_name) {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    if (empty($this->schema)) {
      throw new \RuntimeException("Missing protected property test definition: \$schema in " . static::class . '.');
    }

    $this->schema += [
      'classArgumentsMap' => [],
      'mockObjectsMap' => [],
    ];

    // Allow our tests classes to not use this convention by setting the class
    // to false.  This prevents mocking and instantiation.
    if ($this->schema['classToBeTested'] !== FALSE) {

      $provided_args = !empty($this->args) ? (array) $this->args : [];
      $args = [];
      foreach ($this->schema['classArgumentsMap'] as $property => $value) {

        if (!is_array($value)) {
          $value = [$value, self::FULL_MOCK];
        }
        list($value, $mock_type) = $value;

        // Only set the argument if it's empty.  This allows for seeding in the
        // caller's ::setUp method.
        if (!isset($provided_args[$property])) {
          if (strpos($value, '@') === 0) {

            // We are asking for a service.
            if (!($service = $this->getService($value))) {
              throw new \RuntimeException("The following service is unavailable: \"$value\".");
            }
            $args[$property] = $service;
          }
          else {
            switch ($mock_type) {
              case self::FULL_MOCK:
                $args[$property] = Mockery::mock($value);
                break;

              case self::PARTIAL_MOCK:
                $args[$property]->makePartial();
                break;

              case self::VALUE:
                $args[$property] = $value;
                break;
            }
          }
        }
        else {
          $args[$property] = $provided_args[$property];
        }
      }
      $this->args = (object) $args;
    }

    foreach ($this->schema['mockObjectsMap'] as $property => $class) {
      if (!isset($this->{$property})) {
        if (!is_array($class)) {
          $class = [$class, self::FULL_MOCK];
        }
        list($class, $mock_type) = $class;
        $this->{$property} = Mockery::mock($class);
        if ($mock_type === self::PARTIAL_MOCK) {
          $this->{$property}->makePartial();
        }
      }
    }
    if ($this->schema['classToBeTested'] !== FALSE) {
      $this->createObj();
    }
  }

  /**
   * Create a new instance of $this->obj using $this->args.
   *
   * @return $this
   *   Self for chaining.
   *
   * @throws \ReflectionException
   */
  protected function createObj() {
    $objectReflection = new \ReflectionClass($this->schema['classToBeTested']);
    $this->obj = $objectReflection->newInstanceArgs(array_values(get_object_vars($this->args)));

    return $this;
  }

  /**
   * Create a runtime partial test double as $this->obj.
   *
   * @return $this
   *   Self for chaining.
   *
   * @link http://docs.mockery.io/en/latest/reference/creating_test_doubles.html#runtime-partial-test-doubles
   */
  protected function createMockedObj() {
    $this->obj = Mockery::mock(get_class($this->obj), array_values(get_object_vars($this->args)))
      ->shouldAllowMockingProtectedMethods()
      ->makePartial();

    return $this;
  }

  /**
   * Assert constructor sets internal properties.
   *
   * @throws \ReflectionException
   */
  public function assertConstructorSetsInternalProperties() {
    foreach (array_keys(get_object_vars($this->args)) as $property) {
      $definition = $this->schema['classArgumentsMap'][$property];
      if (!is_array($definition)) {
        $definition = [$definition, self::PARTIAL_MOCK];
      }
      switch ($definition[1]) {
        case self::VALUE:
          $this->assertSame($this->args->{$property}, $definition[0]);
          break;

        default:
          $reflection = new \ReflectionClass(get_class($this->obj));
          $value = $reflection->getProperty($property);
          $value->setAccessible(TRUE);
          $this->assertSame($this->args->{$property}, $value->getValue($this->obj));
          break;
      }
    }
  }

}
