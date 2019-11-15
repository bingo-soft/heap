<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use heap\tree\FibonacciHeap;
use heap\tree\FibonacciHeapNode;

class FibonacciHeapTest extends TestCase
{
    public function testFibonacciHeapCreation(): void
    {
        $heap = new FibonacciHeap();
        $this->assertNotNull($heap);

        $node = new FibonacciHeapNode($heap, 1, 10);
        $this->assertNotNull($node);
    }
}
