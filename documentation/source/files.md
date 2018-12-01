# Working With Files

If you need to CRUD files during a test you may use the `FileSandboxTrait`.

## Quick Start

1. Put the following in your test's `::setUp` or `::setUpBeforeClass` method depending upon if you want the directory to empty for each new test (`::setUp`) or not.

          public function setUp() {
            parent::setUp();
            $this->setUpFileSandbox();
          }
          
          public function setUpBeforeClass() {
            parent::setUp();
            self::setUpFileSandbox();
          }
      
1. Use `$this->sb` or `self::$sb` as the scratch directory path.
1. Call `$this->setUpFileSandbox()` at any time to empty it's contents.
