<?php

namespace heap;

/**
 * Class FibonacciHeapNode
 *
 * @package heap\tree
 */
class FibonacciHeapNode
{
    /**
     * Node heap
     *
     * @var FibonacciHeap
     */
    public $heap;
    
    /**
     * Node value
     *
     * @var mixed
     */
    public $value;
    
    /**
     * Parent node
     *
     * @var FibonacciHeapNode
     */
    public $parent = null;
    
    /**
     * Child node
     *
     * @var FibonacciHeapNode
     */
    public $child = null;
    
    /**
     * Previous node
     *
     * @var FibonacciHeapNode
     */
    public $prev = null;
    
    /**
     * Next node
     *
     * @var FibonacciHeapNode
     */
    public $next = null;
    
    /**
     * True if the node lost child nodes
     *
     * @var bool
     */
    public $mark = false;
    
    /**
     * Node key
     *
     * @var int
     */
    public $key;
    
    /**
     * Node degree
     *
     * @var int
     */
    public $degree = 0;
    
    /**
     * Construct a new Fibonacci heap node
     *
     * @param FibonacciHeap $heap - heap to which the node belongs
     * @param int $key - the node key
     * @param mixed $value - value stored in the node
     */
    public function __construct(FibonacciHeap $heap, int $key, $value)
    {
        $this->heap = $heap;
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Get the node key
     *
     * @return int
     */
    public function getKey(): int
    {
        return $this->key;
    }
    
    /**
     * Get the node value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Set the node value
     *
     * @param mixed $value - node value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * Decrease the node key
     *
     * @param int $newKey - new node key
     */
    public function decreaseKey(int $newKey): void
    {
        $heap = $this->getOwner();
        $heap->decreaseKey($this, $newKey);
    }

    /**
     * Delete the node
     */
    public function delete(): void
    {
        $heap = $this->getOwner();
        $heap->forceDecreaseKeyToMinimum($this);
        $heap->deleteMin();
    }

    /**
     * Get the owner heap of the node
     *
     * @return FibonacciHeap
     */
    public function getOwner(): FibonacciHeap
    {
        if ($this->heap->other != $this->heap) {
            // find root
            $root = $this->heap;
            while ($root != $root->other) {
                $root = $root->other;
            }
            // path-compression
            $cur = $this->heap;
            while ($cur->other != $root) {
                $next = $cur->other;
                $cur->other = $root;
                $cur = $next;
            }
            $this->heap = $root;
        }
        return $this->heap;
    }
}
