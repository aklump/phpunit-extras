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
   * Absolute path to a temporary dirctory for this test.
   *
   * @var null|string
   */
  protected static $sb = NULL;

  /**
   * Create or (destroy and create) a sandbox directory, and assign 'sb'.
   *
   * You have to decide if you want a new sandbox per test or suite.  For each
   * test put this in ::setUp; for one directory per test class put it in
   * ::setUpBeforeClass.  The path to the sandbox is available as either:
   * $this->sb or self::$sb.
   *
   * @code
   *   // Directory gets emptied for each new test.
   *   public function setUp() {
   *     self::setUpFileSandbox();
   *     ...
   *   }
   *
   *   // Directory does NOT get emptied across tests.
   *   public static function setUpBeforeClass() {
   *     self::setUpFileSandbox();
   *     ...
   *   }
   * @endcode
   */
  public static function setUpFileSandbox() {
    self::$sb = static::destroyAndCreateEmptyTestTempDir();
    // Add a trailing slash for brevity in test writing,
    // e.g. self::$sb . $path, instead of self::$sb . "/$path".
    self::$sb = rtrim(self::$sb, '/') . '/';
  }

  /**
   * Exposes self::$sb as $this->sb.
   *
   * @param string $key
   *   Expecting 'sb'.
   *
   * @return string|null
   *   The value of self::$sb if 'sb' === $key, otherwise defaults to ::parent.
   */
  public function __get($key) {
    if ($key === 'sb') {
      return self::$sb;
    }

    return parent::__get($key);
  }

  /**
   * Return a temporary build path where our jig will be instantiated.
   *
   * This uses the system temp directory and test class name.
   *
   * @return string
   *   The final path.
   */
  public static function getTestTempDir() {
    return sys_get_temp_dir() . '/' . str_replace('\\', '_', static::class);
  }

  /**
   * Remove the output directory and all it's contents from the file system.
   *
   * @return string
   *   The path to the output directory.
   *
   * @throws \RuntimeException
   *   If the directory cannot be destroyed.
   */
  public static function destroyTestTempDir() {
    $output_dir = static::getTestTempDir();
    is_dir($output_dir) && exec("[[ \"$output_dir\" ]] && [[ -d $output_dir ]] && rm -r $output_dir");
    if (file_exists($output_dir) && is_dir($output_dir)) {
      throw new \RuntimeException("Could not destroy output dir: \"$output_dir\"");
    }

    return $output_dir;
  }

  /**
   * Ensure the output directory is empty and present on the file system.
   *
   * @return string
   *   Absolute path to the output directory.
   */
  public static function destroyAndCreateEmptyTestTempDir() {
    $output_dir = static::destroyTestTempDir();
    if (!mkdir($output_dir, 0755, TRUE)) {
      throw new \RuntimeException("Could not create output directory: \"$output_dir\".");
    }

    return $output_dir;
  }

}
