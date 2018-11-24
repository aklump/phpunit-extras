<?php

namespace AKlump\PHPUnit;

/**
 * Trait FileSandboxTrait.
 *
 * Provides a temporary system directory to use for file operations in tests.
 *
 * @package AKlump\PHPUnit
 */
trait FileSandboxTrait {

  /**
   * Create or (destroy and create) a sandbox directory.
   *
   * Put this in ::setUp method usually and use $this->sb as directory to
   * the sandbox.
   *
   * @return $this
   */
  protected function newFileSandbox() {
    $this->sb = sys_get_temp_dir() . '/' . str_replace('\\', '_', get_class($this));
    $this->sb = rtrim($this->sb, '/') . '/';
    if (is_dir($this->sb)) {
      $this->assertNotSame(FALSE, system("[[ -d $this->sb ]] && chmod -R u=rwx $this->sb && rm -r $this->sb"));
      $this->assertFileNotExists($this->sb);
    }
    $this->assertNotSame(FALSE, mkdir($this->sb, 0700, TRUE));

    return $this;
  }

}
