<?php

namespace Heap\Tree;

use InvalidArgumentException;
use Heap\AddressableHeapHandleInterface;

/**
 * Class FibonacciHeapNode
 *
 * @package Heap\Tree
 */
class FibonacciHeapNode implements AddressableHeapHandleInterface
{
    /**
     * The heap node unique hash
     *
     * @var string
     */
    private $hash;

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
     * @var mixed
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
     * @param mixed $key - the node key
     * @param mixed $value - value stored in the node
     */
    public function __construct(FibonacciHeap $heap, $key, $value)
    {
        $this->heap = $heap;
        $this->key = $key;
        $this->value = $value;
        $this->hash = uniqid('', true);
    }

    /**
     * Get the node key
     *
     * @return mixed
     */
    public function getKey()
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
     * @param mixed $newKey - new node key
     */
    public function decreaseKey($newKey): void
    {
        $heap = $this->getOwner();
        $heap->decreaseKey($this, $newKey);
    }

    /**
     * Delete the node
     *
     * @throws InvalidArgumentException
     */
    public function delete(): void
    {
        if (is_null($this->next)) {
            throw new InvalidArgumentException("Invalid handle!");
        }
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
        if ($this->heap->getOther() != $this->heap) {
            // find root
            $root = $this->heap;
            while ($root != $root->getOther()) {
                $root = $root->getOther();
            }
            // path-compression
            $cur = $this->heap;
            while ($cur->getOther() != $root) {
                $next = $cur->getOther();
                $cur->setOther($root);
                $cur = $next;
            }
            $this->heap = $root;
        }
        return $this->heap;
    }
}
