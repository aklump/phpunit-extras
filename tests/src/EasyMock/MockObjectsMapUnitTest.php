<?php

// @loftDocs.title(Using mockObjectsMap in Tests)

namespace AKlump\PHPUnit\Test\EasyMock;

use AKlump\PHPUnit\EasyMockTrait;

// @loftDocs.markdown(## Here is the Test)
// @loftDocs.markdown(The `mockObjectsMap` portion of `getSchema` provides a means of having one or more mocked objects automatically created before the start of each test method.)

class MockObjectsMapUnitTest extends \PHPUnit_Framework_TestCase {

  use EasyMockTrait;

  /**
   * {@inheritdoc}
   *
   * Demonstrate the use of mockObjectsMap, which creates mocked instances of a
   * set of classes and adds them to the test class as properties.
   */
  protected function getSchema() {
    return [
      'classToBeTested' => "AKlump\PHPUnit\Test\EasyMock\Charlie",
      'mockObjectsMap' => [
        'delta' => '\AKlump\PHPUnit\Test\EasyMock\Delta',
      ],
    ];
  }

  public function testCanSetDelta() {
    $this->delta->allows('getName')->andReturns('Juliette');

    $this->obj->setDelta($this->delta);
    $this->assertSame('Juliette', $this->obj->getDelta()->getName());
  }

}


// @loftDocs.markdown(## Class Being Tested)

class Charlie {

  private $delta;

  public function setDelta(Delta $delta) {
    $this->delta = $delta;

    return $this;
  }

  public function getDelta() {
    return $this->delta;
  }

}

// @loftDocs.markdown(## Class Being Mocked)
// @loftDocs.markdown(This class gets added in the `mockObjectsMap` array and then automatically mocked to `$this->delta`.)

class Delta {

  public function getName() {
    return 'Frank';
  }

}
