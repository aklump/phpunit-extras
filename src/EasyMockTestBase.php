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
   * return class instances for those services you require via `@service_id`.
   *
   * @param string $service_name
   *   The service id to lookup.
   *
   * @return null|mixed
   *   A service class instance or null.
   */
  public function getService($service_name) {
    return NULL;
  }

  /**
   * Return the schema array for this test case.
   *
   * @return array
   *   Must have the following keys:
   *   - classToBeTested string|false
   *   Should have these keys:
   *   - classArgumentsMap array
   *   - mockObjectsMap array
   */
  protected function getSchema() {
    return ['classToBeTested' => FALSE];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->schema = static::getSchema();
    if (empty($this->schema)) {
      throw new \RuntimeException(static::class . '::getSchema() must return a non-empty, array.');
    }
    $this->schema += [
      'classToBeTested' => FALSE,
      'classArgumentsMap' => [],
      'mockObjectsMap' => [],
    ];

    // Allow our tests classes to not use this convention by setting the class
    // to false.  This prevents mocking and instantiation.
    if ($this->schema['classToBeTested'] !== FALSE) {
      $provided_args = !empty($this->args) ? (array) $this->args : [];
      $args = [];
      foreach ($this->schema['classArgumentsMap'] as $property => $value) {
        // Only set the argument if it's empty.  This allows for seeding in the
        // caller's ::setUp method.
        if (array_key_exists($property, $provided_args)) {
          $args[$property] = $provided_args[$property];
        }
        else {
          $args[$property] = $this->createInstanceFromSchemaDirective($value);
        }
      }
      $this->args = (object) $args;
    }
    foreach ($this->schema['mockObjectsMap'] as $property => $value) {
      if (!property_exists($this, $property)) {
        $this->{$property} = $this->createInstanceFromSchemaDirective($value);
      }
    }
    if ($this->schema['classToBeTested'] !== FALSE) {
      $this->createObj();
    }
  }

  /**
   * Handle the value as passed to a schema map.
   *
   * @param mixed $value
   *   The type of value depends.
   *
   * @return mixed
   *   The expanded value.
   */
  private function createInstanceFromSchemaDirective($value) {
    if (is_callable($value)) {
      $value = [$value(), self::VALUE];
    }
    elseif (is_string($value)) {
      if (substr($value, 0, 1) === '@') {
        $value = substr($value, 1);
        if (!($service = $this->getService($value))) {
          throw new \RuntimeException("The following service is unavailable: \"$value\".");
        }
        $value = [$service, self::VALUE];
      }
      else {
        $value = [$value, self::FULL_MOCK];
      }
    }
    elseif (!is_array($value)) {
      $value = [$value, self::VALUE];
    }

    // Determine if mocking is required.
    list($value, $mock_type) = $value;
    switch ($mock_type) {
      case self::FULL_MOCK:
        $value = Mockery::mock($value);
        break;

      case self::PARTIAL_MOCK:
        $value = Mockery::mock($value);
        $value->makePartial();
        break;
    }

    return $value;
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
