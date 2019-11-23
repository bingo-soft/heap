<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use Exception;
use InvalidArgumentException;
use TypeError;
use heap\MergeableAddressableHeapInterface;
use heap\exception\NoSuchElementException;

abstract class AbstractMergeableAddressableHeapTest extends TestCase
{
    protected const SIZE = 100;

    abstract protected function createHeap(): MergeableAddressableHeapInterface;

    public function testMeld1(): void
    {
        $a = $this->createHeap();
        $a->insert(10);
        $a->insert(11);
        $a->insert(12);
        $a->insert(13);

        $b = $this->createHeap();
        $b->insert(14);
        $b->insert(15);
        $b->insert(16);
        $b4 = $b->insert(17);

        $a->meld($b);

        $this->assertEquals(8, $a->size());
        $this->assertTrue($b->isEmpty());
        $this->assertEquals(0, $b->size());

        $b4->decreaseKey(9);
        $this->assertEquals(9, $a->findMin()->getKey());
    }

    public function testMeld2(): void
    {
        $a = $this->createHeap();
        $a->insert(10);
        $a->insert(11);
        $a->insert(12);
        $a->insert(13);

        $b = $this->createHeap();
        $b->insert(14);
        $b->insert(15);
        $b->insert(16);
        $b4 = $b->insert(17);

        $c = $this->createHeap();
        $c->insert(18);
        $c->insert(19);
        $c->insert(20);
        $c4 = $c->insert(21);

        $a->meld($b);
        $a->meld($c);

        $this->assertEquals(12, $a->size());
        $this->assertTrue($b->isEmpty());
        $this->assertEquals(0, $b->size());

        $this->assertTrue($c->isEmpty());
        $this->assertEquals(0, $c->size());

        $this->assertEquals(10, $a->findMin()->getKey());
        $b4->decreaseKey(9);
        $this->assertEquals(9, $a->findMin()->getKey());
        $c4->decreaseKey(8);
        $this->assertEquals(8, $a->findMin()->getKey());
    }

    public function testMultipleMelds(): void
    {
        $a = $this->createHeap();
        $a->insert(10);
        $a->insert(11);
        $a->insert(12);
        $a->insert(13);

        $b = $this->createHeap();
        $b->insert(14);
        $b->insert(15);
        $b->insert(16);
        $b->insert(17);

        $c = $this->createHeap();
        $c->insert(18);
        $c->insert(19);
        $c->insert(20);
        $c->insert(21);

        $a->meld($b);
        // Invalid: A heap cannot be used after a meld.
        $this->expectException(Exception::class);
        $a->meld($b);
    }

    public function testInsertAfterAMeld(): void
    {
        $a = $this->createHeap();
        $a->insert(10);
        $a->insert(11);
        $a->insert(12);
        $a->insert(13);

        $b = $this->createHeap();
        $b->insert(14);
        $b->insert(15);
        $b->insert(16);
        $b->insert(17);

        $a->meld($b);
        // Invalid: A heap cannot be used after a meld.
        $this->expectException(Exception::class);
        $b->insert(30);
    }

    public function testMeldGeneric(): void
    {
        $h1 = $this->createHeap();

        for ($i = 0; $i < self::SIZE; $i += 1) {
            $h1->insert(2 * $i);
        }

        $h2 = $this->createHeap();
        for ($i = 0; $i < self::SIZE; $i += 1) {
            $h2->insert(2 * $i + 1);
        }

        $h1->meld($h2);

        $this->assertEquals($h1->size(), self::SIZE * 2);
        $this->assertEquals($h2->size(), 0);

        $prev = null;
        while (!$h1->isEmpty()) {
            $cur = $h1->findMin()->getKey();
            $h1->deleteMin();
            if ($prev != null) {
                $this->assertTrue($prev <= $cur);
            }
            $prev = $cur;
        }
    }

    public function testMeldGeneric1(): void
    {
        $h1 = $this->createHeap();

        $h2 = $this->createHeap();
        for ($i = 0; $i < self::SIZE; $i += 1) {
            $h2->insert($i);
        }

        $h1->meld($h2);

        $this->assertEquals($h1->size(), self::SIZE);
        $this->assertEquals($h2->size(), 0);

        $prev = null;
        while (!$h1->isEmpty()) {
            $cur = $h1->findMin()->getKey();
            $h1->deleteMin();
            if ($prev != null) {
                $this->assertTrue($prev <= $cur);
            }
            $prev = $cur;
        }
    }

    public function testMeldGeneric2(): void
    {
        $h1 = $this->createHeap();

        $h2 = $this->createHeap();
        for ($i = 0; $i < self::SIZE; $i += 1) {
            $h1->insert($i);
        }

        $h1->meld($h2);

        $this->assertEquals($h1->size(), self::SIZE);
        $this->assertEquals($h2->size(), 0);

        $prev = null;
        while (!$h1->isEmpty()) {
            $cur = $h1->findMin()->getKey();
            $h1->deleteMin();
            if ($prev != null) {
                $this->assertTrue($prev <= $cur);
            }
            $prev = $cur;
        }
    }

    public function testMeld(): void
    {
        $h1 = $this->createHeap();
        $h2 = $this->createHeap();

        for ($i = 0; $i < self::SIZE; $i += 1) {
            if ($i % 2 == 0) {
                $h1->insert($i);
            } else {
                $h2->insert($i);
            }
        }

        $h1->meld($h2);

        $this->assertTrue($h2->isEmpty());
        $this->assertEquals(0, $h2->size());

        for ($i = 0; $i < self::SIZE; $i += 1) {
            $this->assertEquals($i, $h1->findMin()->getKey());
            $h1->deleteMin();
        }
        $this->assertTrue($h1->isEmpty());
    }
}
