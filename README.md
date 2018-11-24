# PHPUnit Extras

![phpunit_extras](docs/images/screenshot.jpg)

## Summary

Classes to extend `\PHPUnit_Framework_TestCase`.

## Quick Start

    composer require aklump\phpunit-extras

## Contributing

If you find this project useful... please consider [making a donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4E5KZHDQCEUV8&item_name=Gratitude%20for%20aklump%2Fphpunit_extras).

## Usage

### Test a Class With String Arguments

In this example the class being tested only takes string arguments, so there is no mocking involved.  Here's how you would set that up.

    <?php
    
    class Foo {
    
      protected $schema = [
        'classToBeTested' => MyClassToBeTested::class,
        
        // You still need to defined the properties, which the constructor should set, but the values are null, indicating there is no automatic mocking.
        'classArgumentsMap' => [
          'alpha' => NULL,
          'bravo' => NULL,
        ],
      ];
    
      public function testSomething() {
      
        // You hard code the argument values here.
        $this->args->alpha = 'lorem';
        $this->args->bravo = 'ipsum';
        
        // The create the object before any asserts.
        $this->createObj();
       
       ... one or more assertions.
      }
      
    }
