<?php

namespace Heap\Tree;

use InvalidArgumentException;
use Heap\AddressableHeapHandleInterface;

/**
 * Class PairingHeapNode
 *
 * @package Heap\Tree
 */
class PairingHeapNode implements AddressableHeapHandleInterface
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
     * @var PairingHeap
     */
    public $heap;

    /**
     * Node value
     *
     * @var mixed
     */
    public $value;

    /**
     * Older child
     *
     * @var PairingHeapNode
     */
    public $o_c;

    /**
     * Younger sibling
     *
     * @var PairingHeapNode
     */
    public $y_s;

    /**
     * Older sibling or parent
     *
     * @var PairingHeapNode
     */
    public $o_s;

    /**
     * Node key
     *
     * @var mixed
     */
    public $key;

    /**
     * Construct a new Pairing heap node
     *
     * @param PairingHeap $heap - heap to which the node belongs
     * @param mixed $key - the node key
     * @param mixed $value - value stored in the node
     */
    public function __construct(PairingHeap $heap, $key, $value)
    {
        $this->heap = $heap;
        $this->key = $key;
        $this->value = $value;
        $this->hash = uniqid('', true);
        $this->o_c = null;
        $this->y_s = null;
        $this->o_s = null;
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
     */
    public function delete(): void
    {
        $heap = $this->getOwner();
        $heap->delete($this);
    }

    /**
     * Get the owner heap of the node
     *
     * @return PairingHeap
     */
    public function getOwner(): PairingHeap
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
