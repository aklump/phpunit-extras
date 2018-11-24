# Working With Files

If you need to CRUD files during a test you may use the `FileSandboxTrait`.

## Quick Start

1. Put the following in your test's `setUp` method:

          public function setUp() {
            parent::setUp();
            $this->newFileSandbox();
          }
      
1. Use `$this->sb` as the scratch directory path.
1. Call `$this->newFileSandbox()` at any time to empty it's contents.
