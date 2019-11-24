<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use Exception;
use InvalidArgumentException;
use TypeError;
use BingoSoft\Heap\AddressableHeapInterface;
use BingoSoft\Heap\Exception\NoSuchElementException;

abstract class AbstractAddressableHeapTest extends TestCase
{
    protected const SIZE = 100;

    abstract protected function createHeap(): AddressableHeapInterface;

    public function testInsert(): void
    {
        $heap = $this->createHeap();
       
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
        $heap = $this->createHeap();
        $heap->insert(1, "value1")->setValue("value2");
        $this->assertEquals("value2", $heap->findMin()->getValue());
    }

    public function testEmptyAfterDeleteAll(): void
    {
        $heap = $this->createHeap();
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
        $heap = $this->createHeap();

        for ($i = 0; $i < self::SIZE; $i += 1) {
            $heap->insert(random_int(0, 100000));
        }

        $prev = null;
        $i = 0;
        while (!$heap->isEmpty()) {
            $cur = $heap->findMin()->getKey();
            $heap->deleteMin();
            if (!is_null($prev)) {
                $this->assertTrue($prev <= $cur);
            }
            $prev = $cur;
            $i += 1;
        }
    }

    public function testFindDeleteSame(): void
    {
        $heap = $this->createHeap();

        for ($i = 0; $i < self::SIZE; $i += 1) {
            $heap->insert(random_int(0, 100000));
        }

        while (!$heap->isEmpty()) {
            $this->assertEquals($heap->findMin(), $heap->deleteMin());
        }
    }

    public function testDeleteNodes(): void
    {
        $heap = $this->createHeap();

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
        $heap = $this->createHeap();

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
        $heap = $this->createHeap();

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

    public function testDeleteTwice(): void
    {
        $heap = $this->createHeap();

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

        // again
        $this->expectException(InvalidArgumentException::class);
        $nodes[2]->delete();
    }

    public function testDeleteMindeleteTwice(): void
    {
        $heap = $this->createHeap();
        $e1 = $heap->insert(50);
        $heap->insert(100);
        $heap->deleteMin();
        $this->expectException(InvalidArgumentException::class);
        $e1->delete();
    }

    public function testDeleteMinDeleteTwiceChained(): void
    {
        $heap = $this->createHeap();
        for ($i = 0; $i < 15; $i += 1) {
            $heap->insert($i);
        }
        $this->expectException(InvalidArgumentException::class);
        $heap->deleteMin()->delete();
    }

    public function testDeleteMinDecreaseKey(): void
    {
        $heap = $this->createHeap();
        for ($i = 100; $i < 200; $i += 1) {
            $heap->insert($i);
        }
        $this->expectException(InvalidArgumentException::class);
        $heap->deleteMin()->decreaseKey(0);
    }

    public function testNoElementFindMin(): void
    {
        $heap = $this->createHeap();
        $this->expectException(NoSuchElementException::class);
        $heap->findMin();
    }

    public function testDeleteMinFindMin(): void
    {
        $heap = $this->createHeap();
        $this->expectException(NoSuchElementException::class);
        $heap->deleteMin();
    }

    public function testNullPointerException(): void
    {
        $heap = $this->createHeap();
        $this->expectException(TypeError::class);
        $heap->insert(null, null);
    }

    public function testDecreaseKey(): void
    {
        $heap = $this->createHeap();
        $nodes = [];
        for ($i = 0; $i < 15; $i += 1) {
            $nodes[$i] = $heap->insert($i + 100);
        }
        $this->assertEquals(100, $heap->findMin()->getKey());
        $nodes[5]->decreaseKey(5);
        $this->assertEquals(5, $heap->findMin()->getKey());
        $nodes[1]->decreaseKey(50);
        $this->assertEquals(5, $heap->findMin()->getKey());
        $nodes[1]->decreaseKey(20);
        $this->assertEquals(5, $heap->findMin()->getKey());
        $nodes[5]->delete();
        $this->assertEquals(20, $heap->findMin()->getKey());
        $nodes[10]->decreaseKey(3);
        $this->assertEquals(3, $heap->findMin()->getKey());
        $nodes[0]->decreaseKey(0);
        $this->assertEquals(0, $heap->findMin()->getKey());
    }

    public function testDecreaseKeyAndDelete(): void
    {
        $heap = $this->createHeap();
        $nodes = [];
        for ($i = 0; $i < 1000; $i += 1) {
            $nodes[$i] = $heap->insert($i + 2000);
        }
        for ($i = 999; $i >= 0; $i -= 1) {
            $nodes[$i]->decreaseKey($nodes[$i]->getKey() - 2000);
        }
        for ($i = 0; $i < 1000; $i += 1) {
            $this->assertEquals($i, $heap->deleteMin()->getKey());
        }
    }

    public function testIncreaseKeyException(): void
    {
        $heap = $this->createHeap();
        $nodes = [];
        for ($i = 0; $i < 15; $i += 1) {
            $nodes[$i] = $heap->insert($i + 100);
        }
        $this->assertEquals(100, $heap->findMin()->getKey());
        $nodes[5]->decreaseKey(5);
        $this->assertEquals(5, $heap->findMin()->getKey());
        $this->expectException(InvalidArgumentException::class);
        $nodes[1]->decreaseKey(102);
    }
}
