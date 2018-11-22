<?php

namespace AKlump\PHPUnit;

/**
 * Interface ContainerInterface.
 *
 * @package AKlump\Phpunit
 */
interface ContainerInterface {

  /**
   * Gets a service.
   *
   * @param string $id
   *   The service identifier.
   */
  public function get($id);

}
