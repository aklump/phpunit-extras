<?php

// @loftDocs.title(Using mockObjectsMap with EasyMock::PARTIAL)

namespace AKlump\PHPUnit\Test\EasyMock;

use AKlump\PHPUnit\EasyMock;
use AKlump\PHPUnit\EasyMockTrait;
use PHPUnit\Framework\TestCase;

// @loftDocs.markdown(## Here is the Test)
// @loftDocs.markdown(In this implementation of `getSchema` using `mockObjectsMap` combined with `EasyMock::PARTIAL`, `$this->colorHandler` becomes a [partial mock](http://docs.mockery.io/en/latest/reference/partial_mocks.html).)

class MockObjectsMapPartialMockUnitTest extends TestCase {

  use EasyMockTrait;

  /**
   * {@inheritdoc}
   *
   * Demonstrate the use of mockObjectsMap, with a partial mock.
   */
  protected function getSchema() {
    return [
      'classToBeTested' => "AKlump\PHPUnit\Test\EasyMock\Foxtrot",
      'mockObjectsMap' => [
        'colorHandler' => [
          '\AKlump\PHPUnit\Test\EasyMock\ColorHandler',
          EasyMock::PARTIAL,
        ],
      ],
    ];
  }

  public function testCanSetColor() {
    $this->obj->setHandler($this->colorHandler);

    // As a partial mock, the default implementation is used...
    $this->assertSame('blue', $this->obj->getHandler()->getColor());

    // ... unless we set up a mocked expectation...
    $this->colorHandler->allows('getColor')->andReturns('red');

    // ... in which case our expectation is used instead.
    $this->assertSame('red', $this->obj->getHandler()->getColor());
  }

}

// @loftDocs.markdown(## Class Being Partially Mocked)

class ColorHandler {

  public function getColor() {
    return 'blue';
  }

}

// @loftDocs.markdown(## Class Being Tested)

final class Foxtrot {

  private $handler;

  public function setHandler(ColorHandler $handler) {
    $this->handler = $handler;

    return $this;
  }

  public function getHandler() {
    return $this->handler;
  }

}

