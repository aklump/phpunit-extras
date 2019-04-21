<?php

// @loftDocs.id(noconstructunittest)
// @loftDocs.title(Testing Classes Without a Constructor)

namespace AKlump\PHPUnit\Test\EasyMock;

use AKlump\PHPUnit\EasyMockTrait;

// @loftDocs.markdown(## Here is the Test)

class NoConstructorUnitTest extends \PHPUnit_Framework_TestCase {

  use EasyMockTrait;

  /**
   * {@inheritdoc}
   *
   * This implementation only indicates a class to be tested, which takes no
   * constructor arguments.  Each test method will automatically have a new
   * instance of this class available as $this->obj.
   */
  protected function getSchema() {
    return [
      'classToBeTested' => "AKlump\PHPUnit\Test\EasyMock\Alpha",
    ];
  }

  public function testDateReturnsString() {
    $this->assertInternalType('string', $this->obj->date());
  }

}

// @loftDocs.markdown(## Class Being Tested)

class Alpha {

  public function date() {
    return date_create()->format('r');
  }

}

