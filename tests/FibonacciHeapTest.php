<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use heap\FibonacciHeap;
use heap\FibonacciHeapNode;

class FibonacciHeapTest extends TestCase
{
    private const SIZE = 100;

    public function testInsert(): void
    {
        $heap = new FibonacciHeap();
       
        for ($i = 0; $i < self::SIZE; $i += 1) {
            $heap->insert($i);
            $this->assertEquals(0, $heap->findMin()->getKey());
            $this->assertFalse($heap->isEmpty());
            $this->assertEquals($heap->size(), $i + 1);
        }

        for ($i = self::SIZE - 1; $i >= 0; $i -= 1) {
            $this->assertEquals($heap->findMin()->getKey(), self::SIZE - $i - 1);
            $heap->deleteMin();
        }
    }

    public function testSetValue(): void
    {
        $heap = new FibonacciHeap();
        $heap->insert(1, "value1")->setValue("value2");
        $this->assertEquals("value2", $heap->findMin()->getValue());
    }

    public function testEmptyAfterDeleteAll(): void
    {
        $heap = new FibonacciHeap();
        $heap->insert(780);
        $this->assertEquals($heap->size(), 1);
        $this->assertEquals(780, $heap->findMin()->getKey());

        $heap->insert(-389);
        $this->assertEquals($heap->size(), 2);
        $this->assertEquals(-389, $heap->findMin()->getKey());

        $heap->insert(306);
        $this->assertEquals($heap->size(), 3);
        $this->assertEquals(-389, $heap->findMin()->getKey());

        $heap->insert(579);
        $this->assertEquals($heap->size(), 4);
        $this->assertEquals(-389, $heap->findMin()->getKey());

        $heap->deleteMin();
        $this->assertEquals($heap->size(), 3);
        $this->assertEquals(306, $heap->findMin()->getKey());

        $heap->deleteMin();
        $this->assertEquals($heap->size(), 2);
        $this->assertEquals(579, $heap->findMin()->getKey());

        $heap->deleteMin();
        $this->assertEquals($heap->size(), 1);
        $this->assertEquals(780, $heap->findMin()->getKey());

        $heap->deleteMin();
        $this->assertEquals($heap->size(), 0);

        $this->assertTrue($heap->isEmpty());
    }

    public function testRandomDelete(): void
    {
        $heap = new FibonacciHeap();

        for ($i = 0; $i < self::SIZE; $i += 1) {
            $heap->insert(random_int(0, 100000));
        }

        $prev = null;
        while (!$heap->isEmpty()) {
            $cur = $heap->findMin()->getKey();
            $heap->deleteMin();
            if (!is_null($prev)) {
                $this->assertTrue($prev <= $cur);
            }
            $prev = $cur;
        }
    }

    public function testFindDeleteSame(): void
    {
        $heap = new FibonacciHeap();

        for ($i = 0; $i < self::SIZE; $i += 1) {
            $heap->insert(random_int(0, 100000));
        }

        while (!$heap->isEmpty()) {
            $this->assertEquals($heap->findMin(), $heap->deleteMin());
        }
    }

    public function testDeleteNodes(): void
    {
        $heap = new FibonacciHeap();

        $nodes = [];
        for ($i = 0; $i < 15; $i += 1) {
            $nodes[$i] = $heap->insert($i);
        }

        $nodes[5]->delete();
        $this->assertEquals(0, $heap->findMin()->getKey());
        $nodes[7]->delete();
        $this->assertEquals(0, $heap->findMin()->getKey());
        $nodes[0]->delete();
        $this->assertEquals(1, $heap->findMin()->getKey());
        $nodes[2]->delete();
        $this->assertEquals(1, $heap->findMin()->getKey());
        $nodes[1]->delete();
        $this->assertEquals(3, $heap->findMin()->getKey());
        $nodes[3]->delete();
        $this->assertEquals(4, $heap->findMin()->getKey());
        $nodes[9]->delete();
        $this->assertEquals(4, $heap->findMin()->getKey());
        $nodes[4]->delete();
        $this->assertEquals(6, $heap->findMin()->getKey());
        $nodes[8]->delete();
        $this->assertEquals(6, $heap->findMin()->getKey());
        $nodes[11]->delete();
        $this->assertEquals(6, $heap->findMin()->getKey());
        $nodes[6]->delete();
        $this->assertEquals(10, $heap->findMin()->getKey());
        $nodes[12]->delete();
        $this->assertEquals(10, $heap->findMin()->getKey());
        $nodes[10]->delete();
        $this->assertEquals(13, $heap->findMin()->getKey());
        $nodes[13]->delete();
        $this->assertEquals(14, $heap->findMin()->getKey());
        $nodes[14]->delete();
        $this->assertTrue($heap->isEmpty());
    }

    public function testNodesRandomDelete(): void
    {
        $heap = new FibonacciHeap();

        $nodes = [];
        for ($i = 0; $i < 8; $i += 1) {
            $nodes[$i] = $heap->insert($i);
        }

        $nodes[5]->delete();
        $this->assertEquals(0, $heap->findMin()->getKey());
        $nodes[7]->delete();
        $this->assertEquals(0, $heap->findMin()->getKey());
        $nodes[0]->delete();
        $this->assertEquals(1, $heap->findMin()->getKey());
        $nodes[2]->delete();
        $this->assertEquals(1, $heap->findMin()->getKey());
        $nodes[1]->delete();
        $this->assertEquals(3, $heap->findMin()->getKey());
    }

    public function testAddDelete(): void
    {
        $heap = new FibonacciHeap();

        $nodes = [];
        for ($i = 0; $i < self::SIZE; $i += 1) {
            $nodes[$i] = $heap->insert($i);
        }

        for ($i = self::SIZE - 1; $i >= 0; $i -= 1) {
            $nodes[$i]->delete();
            if ($i > 0) {
                $this->assertEquals(0, $heap->findMin()->getKey());
            }
        }
        $this->assertTrue($heap->isEmpty());
    }
}
