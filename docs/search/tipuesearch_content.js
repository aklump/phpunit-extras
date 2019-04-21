var tipuesearch = {"pages":[{"title":"1.2","text":"   BREAKING CHANGES!!! EasyMockTestBase has been replaced with EasyMockTrait and EasyMock.  This will provide greater flexibility in systems that already extend \\PHPUnit_Framework_TestCase. Instead of extends EasyMockTestBase you need to use EasyMockTrait. Replace self::FULL_MOCK with EasyMock::FULL Replace self::PARTIAL_MOCK with EasyMock::PARTIAL Replace self::VALUE with EasyMock::VALUE If you were doing this before this update:  use AKlump\\PhpUnit\\EasyMockTestBase;  class MyTestWithSetUp extends EasyMockTestBase {    \/**    * {@inheritdoc}    *\/   public function setUp() {      \/\/ Do whatever else you need to do.      parent::setUp();    }  }  Then you need to update your code to do like this:  use AKlump\\PhpUnit\\EasyMockTrait;  class MyTestWithSetUp extends \\PHPUnit_Framework_TestCase {    use EasyMockTrait;    \/**    * {@inheritdoc}    *\/   public function setUp() {     $this-&gt;easyMockSetUp();      \/\/ Do whatever else you need to do.      parent::setUp();   }  }    1.1 2019-01-10T18:04, aklump   TestClass::$schema has been replaced with TestClass::getSchema(). You must replace all usages of the class property with a class method.  ","tags":"","url":"CHANGELOG.html"},{"title":"PHPUnit Extras","text":"    Summary  Classes to extend \\PHPUnit_Framework_TestCase.  Quick Start  composer require aklump\/phpunit-extras   Visit https:\/\/aklump.github.io\/phpunit-extras\/ for full documentation.  Contributing  If you find this project useful... please consider making a donation. ","tags":"","url":"README.html"},{"title":"Testing Classes With a Constructor","text":"  &lt;?php  namespace AKlump\\PHPUnit\\Test\\EasyMock;  use AKlump\\PHPUnit\\EasyMock; use AKlump\\PHPUnit\\EasyMockTrait;  Here is the Test  class ConstructorArgumentsUnitTest extends \\PHPUnit_Framework_TestCase {    use EasyMockTrait;    protected function getSchema() {     return [       'classToBeTested' => \"AKlump\\PHPUnit\\Test\\EasyMock\\Bravo\",        \/\/ These are the __construct arguments for class Bravo.  Each element key       \/\/ is the class property, the value is an array where: 0 = the value of       \/\/ the argument, and 1 = indicates the type of argument is a value.       \/\/ The order must match that of the constructor.       'classArgumentsMap' => [         'string' => ['aardvark', EasyMock::VALUE],         'int' => [123, EasyMock::VALUE],         'float' => [4.56, EasyMock::VALUE],         'array' => [['big' => 'fish'], EasyMock::VALUE],         'object' => [(object) ['small' => 'fry'], EasyMock::VALUE],       ],     ];   }    public function testGetValueReturnsExpectedConstructorValue() {     $this->assertSame('aardvark', $this->obj->getValue('string'));     $this->assertSame(123, $this->obj->getValue('int'));     $this->assertSame(4.56, $this->obj->getValue('float'));     $this->assertSame(['big' => 'fish'], $this->obj->getValue('array'));     $this->assertEquals((object) ['small' => 'fry'], $this->obj->getValue('object'));   }    public function testConstructor() {      \/\/ Use this assertion to quickly make sure constructor works as expected.     $this->assertConstructorSetsInternalProperties();   }  }  Class Being Tested  final class Bravo {    protected $string, $int, $float, $array, $object;    public function __construct($string, $int, $float, $array, $object) {     $this->string = $string;     $this->int = $int;     $this->float = $float;     $this->array = $array;     $this->object = $object;   }    public function getValue($key) {     return $this->{$key};   }  } ","tags":"","url":"em--ConstructorArgumentsUnitTest.html"},{"title":"Testing Classes with Service-Based Dependency Injection","text":"  &lt;?php  namespace AKlump\\PHPUnit\\Test\\EasyMock;  use AKlump\\PHPUnit\\EasyMock; use AKlump\\PHPUnit\\EasyMockTrait;  Abstract Test Base Providing Services  To be able to provide class arguments based on service ids, you must implement getService in your test class (or abstract parent class), which hooks into your app's service container.  abstract class TestBase extends \\PHPUnit_Framework_TestCase {    use EasyMockTrait;    static $lima, $november;    \/**    * A contrived example of a service container retrieval.    *\/   public function getService($service_name) {     switch ($service_name) {       case 'my_app.lima':         if (!static::$lima) {           static::$lima = new Lima();         }          return static::$lima;        case 'my_app.mike':         \/\/ Non-shared: each call is a new instance.         return new Mike();        case 'my_app.november':         if (!static::$november) {           static::$november = new November();         }          return static::$november;     }      return NULL;   }  }  Here is the Test  class ConstructorArgumentsUsingServiceIdsUnitTest extends TestBase {    protected function getSchema() {     return [       'classToBeTested' => \"AKlump\\PHPUnit\\Test\\EasyMock\\Kilo\",        \/\/ This is how we provide arguments to the class being tested using       \/\/ service ids, in this example we have a service id 'my_app.lima'.       \/\/ The order must match that of the constructor.       'classArgumentsMap' => [          'lima' => ['my_app.lima', EasyMock::SERVICE],          \/\/ This is a shorthand form of the above; a string prefixed with '@'         \/\/ means a shared service, where the id is 'my_app.november'.         'november' => '@my_app.november',          'mike' => ['my_app.mike', EasyMock::NON_SHARED_SERVICE],       ],     ];   }    public function testAbleToCallServiceMethodsAsExpectedViaDependencyGetter() {     $this->assertSame('parrotlet', $this->obj->getLima()->getAnimal());     $this->assertSame('zebra', $this->obj->getMike()->getAnimal());     $this->assertSame('cat', $this->obj->getNovember()->getAnimal());   }    public function testConstructor() {      \/\/ Use this assertion to quickly make sure constructor works as expected.     $this->assertConstructorSetsInternalProperties();   }  }  Class Being Tested  class Kilo {    protected $lima, $mike, $november;    public function __construct(Lima $lima, November $november, Mike $mike) {     $this->lima = $lima;     $this->mike = $mike;     $this->november = $november;   }    public function getLima() {     return $this->lima;   }    public function getMike() {     return $this->mike;   }    public function getNovember() {     return $this->november;   }  }  Dependency Injected Service Classes  \/**  * Lima is registered in our app under the service id `my_app.lima`.  *\/ class Lima {    public function getAnimal() {     return 'parrotlet';   }  }  \/**  * November is registered in our app under the service id `my_app.november`.  *\/ class November {    public function getAnimal() {     return 'cat';   }  }  Dependency Injected Non-Shared Service Class  Learn more about non-shared services.  \/**  * Mike is registered in our app under the service id `my_app.mike`. It is a  * non-shared service.  *\/ class Mike {    public function getAnimal() {     return 'zebra';   }  } ","tags":"","url":"em--ConstructorArgumentsUsingServiceIdsUnitTest.html"},{"title":"Testing Classes that Call an Argument Method in Constructor","text":"  &lt;?php  namespace AKlump\\PHPUnit\\Test\\EasyMock;  use AKlump\\PHPUnit\\EasyMockTrait;  Here is the Test  class ConstructorCallsArgumentMethodUnitTest extends \\PHPUnit_Framework_TestCase {    use EasyMockTrait;    protected function getSchema() {     return [       'classToBeTested' => '\\AKlump\\PHPUnit\\Test\\EasyMock\\Oscar',       'classArgumentsMap' => [          \/\/ We can manually create a mocked object with a pre-defined expectation         \/\/ so that the constructor is able to call the expectation method, when         \/\/ we use a callable like this:         'pappa' => function () {           $mock = \\Mockery::mock('\\AKlump\\PHPUnit\\Test\\EasyMock\\Pappa');           $mock->allows('setDate');            return $mock;         },       ],     ];   }    public function testConstructor() {     $this->assertConstructorSetsInternalProperties();   } }  Class Being Tested  class Oscar {    private $pappa;    public function __construct(Pappa $pappa) {     $this->pappa = $pappa;     $this->pappa->setDate(date_create());   }  }  Class With a Method That Gets Called in Oscar::__construct  class Pappa {    private $date;    public function setDate(\\DateTime $date) {     $this->date = $date;   }  } ","tags":"","url":"em--ConstructorCallsArgumentMethodUnitTest.html"},{"title":"Testing Classes With Dependency Injection","text":"  &lt;?php  namespace AKlump\\PHPUnit\\Test\\EasyMock;  use AKlump\\PHPUnit\\EasyMock; use AKlump\\PHPUnit\\EasyMockTrait;  Here is the Test  The test class will have the following properties setted automatically $this-&gt;obj and $this-&gt;args to be used by your methods.  class ConstructorMockArgumentsUnitTest extends \\PHPUnit_Framework_TestCase {    use EasyMockTrait;    protected function getSchema() {     return [       'classToBeTested' => \"AKlump\\PHPUnit\\Test\\EasyMock\\Hotel\",        \/\/ These are the constructor arguments for Hotel, we are going to set up       \/\/ india as a full mock, and juliette as a partial mock.       \/\/ The order must match that of the constructor.       'classArgumentsMap' => [          'india' => '\\AKlump\\PHPUnit\\Test\\EasyMock\\India',          'juliette' => [           '\\AKlump\\PHPUnit\\Test\\EasyMock\\Juliette',           EasyMock::PARTIAL,         ],       ],     ];   }    \/**    * @expectedException \\Mockery\\Exception\\BadMethodCallException    *\/   public function testFullMockWithoutExpectationThrows() {     $this->assertSame(India::class, $this->obj->getIndiaName());   }    public function testFullMockWithExpectationReturnsExpectation() {     $this->args->india->allows('getName')->andReturns('David');     $this->assertSame('David', $this->obj->getIndiaName());   }    public function testPartialMockCallsDefinedMethod() {     $this->assertSame(Juliette::class, $this->obj->getJulietteName());   }    public function testPartialMockWithExpectationReturnsExpectation() {     $this->args->juliette->allows('getName')->andReturns('Caesar');     $this->assertSame('Caesar', $this->obj->getJulietteName());   }    public function testConstructor() {      \/\/ Use this assertion to quickly make sure constructor works as expected.     $this->assertConstructorSetsInternalProperties();   }  }  Class Being Tested  class Hotel {    protected $india, $juliette;    public function __construct(India $india, Juliette $juliette) {     $this->india = $india;     $this->juliette = $juliette;   }    public function getIndiaName() {     return $this->india->getName();   }    public function getJulietteName() {     return $this->juliette->getName();   }  }  Fully Mocked Class  class India {    public function getName() {     return __CLASS__;   }  }  Partial Mocked Class  class Juliette {    public function getName() {     return __CLASS__;   }  } ","tags":"","url":"em--ConstructorMockArgumentsUnitTest.html"},{"title":"Using mockObjectsMap with EasyMock::PARTIAL","text":"  &lt;?php  namespace AKlump\\PHPUnit\\Test\\EasyMock;  use AKlump\\PHPUnit\\EasyMock; use AKlump\\PHPUnit\\EasyMockTrait;  Here is the Test  In this implementation of getSchema using mockObjectsMap combined with EasyMock::PARTIAL, $this-&gt;colorHandler becomes a partial mock.  class MockObjectsMapPartialMockUnitTest extends \\PHPUnit_Framework_TestCase {    use EasyMockTrait;    \/**    * {@inheritdoc}    *    * Demonstrate the use of mockObjectsMap, with a partial mock.    *\/   protected function getSchema() {     return [       'classToBeTested' => \"AKlump\\PHPUnit\\Test\\EasyMock\\Foxtrot\",       'mockObjectsMap' => [         'colorHandler' => [           '\\AKlump\\PHPUnit\\Test\\EasyMock\\ColorHandler',           EasyMock::PARTIAL,         ],       ],     ];   }    public function testCanSetColor() {     $this->obj->setHandler($this->colorHandler);      \/\/ As a partial mock, the default implementation is used...     $this->assertSame('blue', $this->obj->getHandler()->getColor());      \/\/ ... unless we set up a mocked expectation...     $this->colorHandler->allows('getColor')->andReturns('red');      \/\/ ... in which case our expectation is used instead.     $this->assertSame('red', $this->obj->getHandler()->getColor());   }  }  Class Being Partially Mocked  class ColorHandler {    public function getColor() {     return 'blue';   }  }  Class Being Tested  final class Foxtrot {    private $handler;    public function setHandler(ColorHandler $handler) {     $this->handler = $handler;      return $this;   }    public function getHandler() {     return $this->handler;   }  } ","tags":"","url":"em--MockObjectsMapPartialMockUnitTest.html"},{"title":"Using mockObjectsMap in Tests","text":"  &lt;?php  namespace AKlump\\PHPUnit\\Test\\EasyMock;  use AKlump\\PHPUnit\\EasyMockTrait;  Here is the Test  The mockObjectsMap portion of getSchema provides a means of having one or more mocked objects automatically created before the start of each test method.  class MockObjectsMapUnitTest extends \\PHPUnit_Framework_TestCase {    use EasyMockTrait;    \/**    * {@inheritdoc}    *    * Demonstrate the use of mockObjectsMap, which creates mocked instances of a    * set of classes and adds them to the test class as properties.    *\/   protected function getSchema() {     return [       'classToBeTested' => \"AKlump\\PHPUnit\\Test\\EasyMock\\Charlie\",       'mockObjectsMap' => [         'delta' => '\\AKlump\\PHPUnit\\Test\\EasyMock\\Delta',       ],     ];   }    public function testCanSetDelta() {     $this->delta->allows('getName')->andReturns('Juliette');      $this->obj->setDelta($this->delta);     $this->assertSame('Juliette', $this->obj->getDelta()->getName());   }  }  Class Being Tested  class Charlie {    private $delta;    public function setDelta(Delta $delta) {     $this->delta = $delta;      return $this;   }    public function getDelta() {     return $this->delta;   }  }  Class Being Mocked  This class gets added in the mockObjectsMap array and then automatically mocked to $this-&gt;delta.  class Delta {    public function getName() {     return 'Frank';   }  } ","tags":"","url":"em--MockObjectsMapUnitTest.html"},{"title":"Testing Classes Without a Constructor","text":"  &lt;?php   namespace AKlump\\PHPUnit\\Test\\EasyMock;  use AKlump\\PHPUnit\\EasyMockTrait;  Here is the Test  class NoConstructorUnitTest extends \\PHPUnit_Framework_TestCase {    use EasyMockTrait;    \/**    * {@inheritdoc}    *    * This implementation only indicates a class to be tested, which takes no    * constructor arguments.  Each test method will automatically have a new    * instance of this class available as $this->obj.    *\/   protected function getSchema() {     return [       'classToBeTested' => \"AKlump\\PHPUnit\\Test\\EasyMock\\Alpha\",     ];   }    public function testDateReturnsString() {     $this->assertInternalType('string', $this->obj->date());   }  }  Class Being Tested  class Alpha {    public function date() {     return date_create()->format('r');   }  } ","tags":"","url":"em--NoConstructorUnitTest.html"},{"title":"If Your Test Class Needs a SetUp Method","text":"  &lt;?php   namespace AKlump\\PHPUnit\\Test\\EasyMock;  use AKlump\\PHPUnit\\EasyMock; use AKlump\\PHPUnit\\EasyMockTrait;  Here is the Test  This shows how you will use setUp by aliasing EasyMockTrait::setUp.  class TestWithSetUpMethodUnitTest extends \\PHPUnit_Framework_TestCase {    use EasyMockTrait {     EasyMockTrait::setUp as easyMockSetUp;   }    protected function getSchema() {     return [       'classToBeTested' => \"AKlump\\PHPUnit\\Test\\EasyMock\\Quebec\",       'classArgumentsMap' => [         'list' => [['do', 're', 'mi'], EasyMock::VALUE],       ],     ];   }    public function testTestDataObject() {     $this->assertSame(['fa', 'so'], $this->data);     $this->assertSame(['do', 're', 'mi'], $this->obj->getList());   }    public function testConstructor() {     $this->assertConstructorSetsInternalProperties();   }    public function setUp() {     $this->easyMockSetUp();      $this->data = ['fa', 'so'];   }  }  Class Being Tested  class Quebec {    protected $list;    public function __construct(array $list) {     $this->list = $list;   }    public function getList() {     return $this->list;   }  } ","tags":"","url":"em--TestWithSetUpMethodUnitTest.html"},{"title":"Easy Mock","text":"  The trait \\AKlump\\PHPUnit\\EasyMockTrait is meant to simplify mocking objects for unit testing.  It uses the Mockery library for mocking.  To get a sense of how to use it please read the various examples provided. ","tags":"","url":"em--easy-mock.html"},{"title":"Working With Files","text":"  If you need to CRUD files during a test you may use the FileSandboxTrait.  Quick Start   Put the following in your test's ::setUp or ::setUpBeforeClass method depending upon if you want the directory to empty for each new test (::setUp) or not.    public function setUp() {     parent::setUp();     $this-&gt;setUpFileSandbox();   }    public function setUpBeforeClass() {     parent::setUp();     self::setUpFileSandbox();   }  Use $this-&gt;sb or self::$sb as the scratch directory path. Call $this-&gt;setUpFileSandbox() at any time to empty it's contents.  ","tags":"","url":"files.html"},{"title":"Search Results","text":" ","tags":"","url":"search--results.html"}]};
