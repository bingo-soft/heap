<?php

namespace Heap\Tree;

use Exception;
use InvalidArgumentException;
use Heap\exception\NoSuchElementException;
use Heap\MergeableAddressableHeapInterface;
use Heap\AddressableHeapHandleInterface;

/**
 * Class PairingHeap
 *
 * @package Heap\Tree
 */
class PairingHeap implements MergeableAddressableHeapInterface
{
    /**
     * The heap unique hash
     *
     * @var string
     */
    private $hash;

    /**
     * The heap root node
     *
     * @var PairingHeapNode
     */
    private $root = null;

    /**
     * Size of the heap
     *
     * @var int
     */
    private $size = 0;

    /**
     * Reference to current or some other heap in case of melding
     *
     * @var PairingHeap
     */
    private $other;

    /**
     * Construct a new Pairing heap
     */
    public function __construct()
    {
        $this->root = null;
        $this->size = 0;
        $this->other = $this;
        $this->hash = uniqid('', true);
    }

    /**
     * Insert a new node to the heap
     *
     * @param mixed $key - node key
     * @param null|mixed $value - node value
     *
     * @return AddressableHeapHandleInterface
     *
     * @throws Exception
     */
    public function insert($key, $value = null): AddressableHeapHandleInterface
    {
        if ($this->other != $this) {
            throw new Exception("A heap cannot be used after a meld");
        }

        $node = new PairingHeapNode($this, $key, $value);
        $this->root = $this->link($this->root, $node);
        $this->size += 1;
        return $node;
    }

    /**
     * Find heap node with the minimal key
     *
     * @return AddressableHeapHandleInterface
     *
     * @throws NoSuchElementException
     */
    public function findMin(): AddressableHeapHandleInterface
    {
        if ($this->size == 0) {
            throw new NoSuchElementException("No such element!");
        }
        return $this->root;
    }

    /*
     * Delete a node
     *
     * @param PairingHeapNode $node - the node to delete
     */
    public function delete(PairingHeapNode $node): void
    {
        if ($this->root == $node) {
            $this->deleteMin();
            $node->o_c = null;
            $node->y_s = null;
            $node->o_s = null;
            return;
        }

        if (is_null($node->o_s)) {
            throw new InvalidArgumentException("Invalid handle!");
        }

        // unlink from parent
        if (!is_null($node->y_s)) {
            $node->y_s->o_s = $node->o_s;
        }
        if ($node->o_s->o_c == $node) {
            $node->o_s->o_c = $node->y_s;
        } else {
            $node->o_s->y_s = $node->y_s;
        }
        $node->y_s = null;
        $node->o_s = null;

        // perform delete-min at tree rooted at this
        $t = $this->combine($this->cutChildren($node));

        // and merge with other cut tree
        $this->root = $this->link($this->root, $t);

        $this->size -= 1;
    }

    /**
     * Delete an element with the minimal key.
     *
     * @return AddressableHeapHandleInterface
     *
     * @throws NoSuchElementException
     */
    public function deleteMin(): AddressableHeapHandleInterface
    {
        if ($this->size == 0) {
            throw new NoSuchElementException("No such element!");
        }
        $oldRoot = $this->root;

        $this->root = $this->combine($this->cutChildren($this->root));

        $this->size -= 1;

        return $oldRoot;
    }

    /**
     * Check if the heap is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->size == 0;
    }

    /**
     * Get the number of elements in the heap.
     *
     * @return int
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * Get the other heap referenced in the current heap
     *
     * @return MergeableAddressableHeapInterface
     */
    public function getOther(): MergeableAddressableHeapInterface
    {
        return $this->other;
    }

    /**
     * Reference the other heap in the current heap
     *
     * @param MergeableAddressableHeapInterface $other - the other heap
     */
    public function setOther(MergeableAddressableHeapInterface $other): void
    {
        if (!($other instanceof PairingHeap)) {
            throw new InvalidArgumentException("The heap must be of type Pairing.");
        }
        $this->other = $other;
    }

    /**
     * Clear all the elements in the heap
     */
    public function clear(): void
    {
        $this->root = null;
        $this->size = 0;
        $this->other = $this;
    }

    /**
     * Meld other heap to the current heap
     *
     * @param MergeableAddressableHeapInterface $other - the heap to be melded
     *
     * @throws Exception
     */
    public function meld(MergeableAddressableHeapInterface $other): void
    {
        if (!($other instanceof PairingHeap)) {
            throw new InvalidArgumentException("The heap to be melded must be of type Pairing.");
        }

        if ($other->other != $other) {
            throw new Exception("A heap cannot be used after a meld.");
        }

        $this->size += $other->size;
        $this->root = $this->link($this->root, $other->root);

        // clear other
        $other->size = 0;
        $other->root = null;

        // take ownership
        $other->other = $this;
    }

    /**
     * Decrease the node key
     *
     * @param PairingHeapNode $node - the node
     * @param mixed $newKey - the node new key
     *
     * @throws Exception
     */
    public function decreaseKey(PairingHeapNode $node, $newKey): void
    {
        if ($newKey > $node->key) {
            throw new InvalidArgumentException("Keys can only be decreased!");
        } elseif ($node->key != $newKey) {
            if (is_null($node->o_s)) {
                throw new InvalidArgumentException("Invalid handle!");
            }
            $node->key = $newKey;
            // unlink from parent
            if (!is_null($node->y_s)) {
                $node->y_s->o_s = $node->o_s;
            }
            if ($node->o_s->o_c == $node) {
                $node->o_s->o_c = $node->y_s;
            } else {
                $node->o_s->y_s = $node->y_s;
            }
            $node->y_s = null;
            $node->o_s = null;

            // merge with root
            $this->root = $this->link($this->root, $node);
        }
    }

    /*
     * Link two nodes
     *
     * @param PairingHeapNode $first - first node
     * @param PairingHeapNode $second - second node
     *
     * @return null|PairingHeapNode
     */
    private function link(?PairingHeapNode $first = null, ?PairingHeapNode $second = null): ?PairingHeapNode
    {
        if (is_null($second)) {
            return $first;
        } elseif (is_null($first)) {
            return $second;
        } elseif ($first->key < $second->key) {
            $second->y_s = $first->o_c;
            $second->o_s = $first;
            if ($first->o_c != null) {
                $first->o_c->o_s = $second;
            }
            $first->o_c = $second;
            return $first;
        } else {
            return $this->link($second, $first);
        }
    }

    /**
     * Cut the children of a node and return the list.
     *
     * @param PairingHeapNode $node - the root node
     *
     * @return PairingHeapNode
     */
    private function cutChildren(PairingHeapNode $node): ?PairingHeapNode
    {
        $child = $node->o_c;
        $node->o_c = null;
        if (!is_null($child)) {
            $child->o_s = null;
        }
        return $child;
    }

    /*
     * Two pass pair and compute root.
     *
     * @param PairingHeapNode $node - the node
     *
     * @return null|PairingHeapNode
     */
    private function combine(?PairingHeapNode $node = null): ?PairingHeapNode
    {
        if (is_null($node)) {
            return null;
        }
        if (!is_null($node->o_s)) {
            throw new InvalidArgumentException("Invalid handle!");
        }
        
        // left-right pass
        $pairs = null;
        $it = $node;
        while (!is_null($it)) {
            $p_it = $it;
            $it = $it->y_s;

            if (is_null($it)) {
                // append last node to pair list
                $p_it->y_s = $pairs;
                $p_it->o_s = null;
                $pairs = $p_it;
            } else {
                $n_it = $it->y_s;

                // disconnect both
                $p_it->y_s = null;
                $p_it->o_s = null;
                $it->y_s = null;
                $it->o_s = null;

                // link trees
                $p_it = $this->link($p_it, $it);

                // append to pair list
                $p_it->y_s = $pairs;
                $pairs = $p_it;

                // advance
                $it = $n_it;
            }
        }

        // second pass (reverse order - due to add first)
        $it = $pairs;
        $f = null;
        while (!is_null($it)) {
            $nextIt = $it->y_s;
            $it->y_s = null;
            $f = $this->link($f, $it);
            $it = $nextIt;
        }

        return $f;
    }
}
