<?php

namespace AKlump\PHPUnit;

/**
 * A class to hold configuration info.
 */
class EasyMock {

  /**
   * A flag to indicate a full mocked object.
   *
   * This is the default.
   *
   * @var int
   */
  const FULL = 0;

  /**
   * A Flag to indicate a partial mock should be created.
   *
   * Used for element values as arrays on the classArgumentsMap config.
   *
   * @var int
   */
  const PARTIAL = 1;

  /**
   * A flag to indicate the argument value is not a classname but a value.
   *
   * Use this when a constructor argument is not a class instance.
   *
   * @var int
   */
  const VALUE = 2;

  /**
   * A flag to indicate that the argument value is a shared service id.
   *
   * @link https://symfony.com/doc/current/service_container/shared.html
   */
  const SERVICE = 3;

  /**
   * A flag to indicate that the argument value is NOT a shared service id,
   * that is: each time the service is requested from the container, you
   * receive a new object instance.
   *
   * @link https://symfony.com/doc/current/service_container/shared.html
   */
  const NON_SHARED_SERVICE = 4;

}
