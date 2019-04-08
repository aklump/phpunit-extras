## 1.2

* BREAKING CHANGES!!!
* `EasyMockTestBase` has been replaced with `EasyMockTrait` and `EasyMock`.  This will provide greater flexibility in systems that already extend `\PHPUnit_Framework_TestCase`.
* Instead of `extends EasyMockTestBase` you need to `use EasyMockTrait`.
* Replace `self::FULL_MOCK` with `EasyMock::FULL`
* Replace `self::PARTIAL_MOCK` with `EasyMock::PARTIAL`
* Replace `self::VALUE` with `EasyMock::VALUE`
* If you were doing this before this update:

        use AKlump\PhpUnit\EasyMockTestBase;
        
        class MyTestWithSetUp extends EasyMockTestBase {
        
          /**
           * {@inheritdoc}
           */
          public function setUp() {
            
            // Do whatever else you need to do.
            
            parent::setUp();
        
          }
        
        }

* Then you need to update your code to do like this:
        
        use AKlump\PhpUnit\EasyMockTrait;
        
        class MyTestWithSetUp extends \PHPUnit_Framework_TestCase {
        
          use EasyMockTrait;
        
          /**
           * {@inheritdoc}
           */
          public function setUp() {
            $this->easyMockSetUp();
        
            // Do whatever else you need to do.
        
            parent::setUp();
          }
        
        }

## 1.1 2019-01-10T18:04, aklump

* `TestClass::$schema` has been replaced with `TestClass::getSchema()`.
* You must replace all usages of the class property with a class method.
