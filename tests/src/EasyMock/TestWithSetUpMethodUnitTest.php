<?php

// @loftDocs.id(test-with-setup)
// @loftDocs.title(If Your Test Class Needs a SetUp Method)

namespace AKlump\PHPUnit\Test\EasyMock;

use AKlump\PHPUnit\EasyMock;
use AKlump\PHPUnit\EasyMockTrait;

// @loftDocs.markdown(## Here is the Test)
// @loftDocs.markdown(This shows how you will use `setUp` by aliasing `EasyMockTrait::setUp`.)

class TestWithSetUpMethodUnitTest extends \PHPUnit_Framework_TestCase {

  use EasyMockTrait {
    EasyMockTrait::setUp as easyMockSetUp;
  }

  protected function getSchema() {
    return [
      'classToBeTested' => "AKlump\PHPUnit\Test\EasyMock\Quebec",
      'classArgumentsMap' => [
        'list' => [['do', 're', 'mi'], EasyMock::VALUE],
      ],
    ];
  }

  public function testTestDataObject() {
    $this->assertSame(['fa', 'so'], $this->data);
    $this->assertSame(['do', 're', 'mi'], $this->obj->getList());
  }

  public function testConstructor() {
    $this->assertConstructorSetsInternalProperties();
  }

  public function setUp() {
    $this->easyMockSetUp();

    $this->data = ['fa', 'so'];
  }

}

// @loftDocs.markdown(## Class Being Tested)

class Quebec {

  protected $list;

  public function __construct(array $list) {
    $this->list = $list;
  }

  public function getList() {
    return $this->list;
  }

}

