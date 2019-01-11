# Examples

## Testing a Class with Constructor String Arguments

Here is a class that takes string as constructor arguments:
      
      class Bid {
      
        public function __construct(
          string $path_to_source,
          string $path_to_templates
        ) {
          $this->sourcePath = $path_to_source;
          $this->templateDirs = [$path_to_templates];
        }
      
Here's how you'd set the test up:

      protected $schema = [
        'classToBeTested' => Bid::class,
        'classArgumentsMap' => [
          'sourcePath' => [
            __DIR__ . '/providers/data',
            self::VALUE,
          ],
          'templateDirs' => [
            __DIR__ . '/providers/templates',
            self::VALUE,
          ],
        ],
        'mockObjectsMap' => [],
      ];      

## Testing a Class that Calls a method on an argument to the Constructor

Here is a summary of the class:

      <?php
      
      class Api {
      
        public function __construct(
          Data $dataApiData,
          string $method,
          ApiResourceInterface $resource
        ) {
          $this->setDataApiData($dataApiData);
          $this->method = strtoupper($method);
          $this->setResource($resource);
        }
      
        public function setResource(ApiResourceInterface $resource) {
          $resource->getAllowedMethods();
          ...
        }
      }

Setup the Kernel test:

      <?php
      
      namespace Drupal\Tests\microservice;
      
      use AKlump\DrupalTest\KernelTestBase;
      use Drupal\microservice\Api;
      
      class ApiKernelTest extends KernelTestBase {
      
        protected $schema = [
          'classToBeTested' => Api::class,
          'classArgumentsMap' => [
            // Set this to NULL, the actual unmocked value will be set in setUp.
            'dataApiData' => NULL,
            
            // Hardcode a value of 'get' here.
            'method' => ['get', self::VALUE],
            
            // Set this to NULL, see ::setUp where this gets mocked.
            'resource' => NULL,
          ],
          'mockObjectsMap' => [],
        ];
      
        public function setUp() {
          
          // Pull an actual value based on a function after bootstrap.
          $this->args->dataApiData = data_api();
          
          // We have to mock this here, because the constructor requires a call to getAllowedMethods, which we much mock before ::setUp.
          $this->args->resource = \Mockery::mock('Drupal\microservice\Resource\ApiResourceInterface');
          // Setup an expectation that will be used in the constructor.
          $this->args->resource
            ->allows('getAllowedMethods')
            ->andReturns(['GET']);
          parent::setUp();
        }
      
        public function testConstructorSetsInternalProperties() {
          $this->assertConstructorSetsInternalProperties();
        }
      
      }
