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
