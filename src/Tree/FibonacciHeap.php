<?php

namespace Heap\Tree;

use Exception;
use InvalidArgumentException;
use Heap\Exception\NoSuchElementException;
use Heap\MergeableAddressableHeapInterface;
use Heap\AddressableHeapHandleInterface;

/**
 * Class FibonacciHeap
 *
 * @package Heap\Tree
 */
class FibonacciHeap implements MergeableAddressableHeapInterface
{
    /**
     * The heap unique hash
     *
     * @var string
     */
    private $hash;

    /**
     * Heap node with the minimal key
     *
     * @var FibonacciHeapNode
     */
    private $minRoot = null;
    
    /**
     * Number of roots in the heap
     *
     * @var int
     */
    private $roots = 0;

    /**
     * Size of the heap
     *
     * @var int
     */
    private $size = 0;

    /**
     * Auxiliary array for consolidation
     */
    private $aux = [];

    /**
     * Reference to current or some other heap in case of melding
     *
     * @var FibonacciHeap
     */
    private $other;

    /**
     * Construct a new Fibonacci heap
     */
    public function __construct()
    {
        $this->minRoot = null;
        $this->roots = 0;
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

        $node = new FibonacciHeapNode($this, $key, $value);
        $this->addToRootList($node);
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
        return $this->minRoot;
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
        $z = $this->minRoot;

        // move z children into root list
        $x = $z->child;
        $i = 0;
        while (!is_null($x)) {
            $nextX = ($x->next == $x) ? null : $x->next;

            // clear parent
            $x->parent = null;

            // remove from child list
            $x->prev->next = $x->next;
            $x->next->prev = $x->prev;

            // add to root list
            $x->next = $this->minRoot->next;
            // probably need to reset
            $x->prev = $this->minRoot;
            $this->minRoot->next = $x;
            $x->next->prev = $x;
            $this->roots += 1;

            // advance
            $x = $nextX;
            $i += 1;
        }
        $z->degree = 0;
        $z->child = null;

        // remove z from root list
        $z->prev->next = $z->next;
        $z->next->prev = $z->prev;
        $this->roots -= 1;

        // decrease size
        $this->size -= 1;

        // update minimum root
        
        if ($z == $z->next) {
            $this->minRoot = null;
        } else {
            $this->minRoot = $z->next;
            $this->consolidate();
        }

        // clear other fields
        $z->next = null;
        $z->prev = null;

        return $z;
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
        if (!($other instanceof FibonacciHeap)) {
            throw new InvalidArgumentException("The heap must be of type Fibonacci.");
        }
        $this->other = $other;
    }

    /**
     * Clear all the elements in the heap
     */
    public function clear(): void
    {
        $this->minRoot = null;
        $this->roots = 0;
        $this->other = $this;
        $this->size = 0;
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
        if (!($other instanceof FibonacciHeap)) {
            throw new InvalidArgumentException("The heap to be melded must be of type Fibonacci.");
        }

        if ($other->other != $other) {
            throw new Exception("A heap cannot be used after a meld.");
        }

        if ($this->size == 0) {
            // copy the other
            $this->minRoot = $other->minRoot;
        } elseif ($other->size != 0) {
            // concatenate root lists
            $h11 = $this->minRoot;
            $h12 = $h11->next;
            $h21 = $other->minRoot;
            $h22 = $h21->next;
            $h11->next = $h22;
            $h22->prev = $h11;
            $h21->next = $h12;
            $h12->prev = $h21;

            // find new minimum
            if ($other->minRoot->key < $this->minRoot->key) {
                $this->minRoot = $other->minRoot;
            }
        }
        $this->roots += $other->roots;
        $this->size += $other->size;

        // clear other
        $other->size = 0;
        $other->minRoot = null;
        $other->roots = 0;

        // take ownership
        $other->other = $this;
    }

    /**
     * Decrease the node key
     *
     * @param FibonacciHeapNode $node - the node
     * @param mixed $newKey - the node new key
     *
     * @throws Exception
     */
    public function decreaseKey(FibonacciHeapNode $node, $newKey): void
    {
        if ($newKey > $node->key) {
            throw new InvalidArgumentException("Keys can only be decreased!");
        } elseif ($node->key != $newKey) {
            if (is_null($node->next)) {
                throw new InvalidArgumentException("Invalid handle!");
            }

            $node->key = $newKey;

            // if not root and heap order violation
            $parent = $node->parent;
            if (!is_null($parent) && $node->key < $parent->key) {
                $this->cut($node, $parent);
                $this->cascadingCut($parent);
            }

            // update minimum root
            if ($node->key < $this->minRoot->key) {
                $this->minRoot = $node;
            }
        }
    }

    /*
     * Decrease the key of a node to the minimum
     *
     * @param FibonacciHeapNode $node - the node
     */
    public function forceDecreaseKeyToMinimum(FibonacciHeapNode $node): void
    {
        // if not root
        $parent = $node->parent;
        if (!is_null($parent)) {
            $this->cut($node, $parent);
            $this->cascadingCut($parent);
        }
        $this->minRoot = $node;
    }

    /*
     * Consolidate: Make sure each root tree has a distinct degree.
     */
    private function consolidate(): void
    {
        $maxDegree = -1;

        // for each node in root list
        $numRoots = $this->roots;
        $x = $this->minRoot;
        while ($numRoots > 0) {
            $nextX = $x->next;
            $deg = $x->degree;

            while (true) {
                if (!isset($this->aux[$deg])) {
                    break;
                }
                $y = $this->aux[$deg];

                // make sure x's key is smaller
                if ($y->key < $x->key) {
                    $tmp = $x;
                    $x = $y;
                    $y = $tmp;
                }

                // make y a child of x
                $this->link($y, $x);

                $this->aux[$deg] = null;
                $deg += 1;
            }

            // store result
            $this->aux[$deg] = $x;

            // keep track of max degree
            if ($deg > $maxDegree) {
                $maxDegree = $deg;
            }

            // advance
            $x = $nextX;
            $numRoots -= 1;
        }

        // recreate root list and find minimum root
        $this->minRoot = null;
        $this->roots = 0;
        for ($i = 0; $i <= $maxDegree; $i += 1) {
            if (!is_null($this->aux[$i])) {
                $this->addToRootList($this->aux[$i]);
                $this->aux[$i] = null;
            }
        }
    }

    /*
     * Remove node "first" from the root list and make it a child of "second". Degree of "second"
     * increases by 1 and "first" is unmarked if marked.
     *
     * @param FibonacciHeapNode $first - first node
     * @param FibonacciHeapNode $second - second node
     */
    private function link(FibonacciHeapNode $first, FibonacciHeapNode $second): void
    {
        // remove from root list
        $first->prev->next = $first->next;
        $first->next->prev = $first->prev;

        // one less root
        $this->roots -= 1;

        // clear if marked
        $first->mark = false;

        // hang as second's child
        $second->degree += 1;
        $first->parent = $second;

        $child = $second->child;
        if (is_null($child)) {
            $second->child = $first;
            $first->next = $first;
            $first->prev = $first;
        } else {
            $first->prev = $child;
            $first->next = $child->next;
            $child->next = $first;
            $first->next->prev = $first;
        }
    }

    /*
     * Cut the link between node and its parent making node a root.
     *
     * @param FibonacciHeapNode $node - the node
     * @param FibonacciHeapNode $parent - the root node
     */
    private function cut(FibonacciHeapNode $node, FibonacciHeapNode $parent): void
    {
        // remove x from child list of y
        $node->prev->next = $node->next;
        $node->next->prev = $node->prev;
        $parent->degree -= 1;
        if ($parent->degree == 0) {
            $parent->child = null;
        } elseif ($parent->child == $node) {
            $parent->child = $node->next;
        }

        // add x to the root list
        $node->parent = null;
        $this->addToRootList($node);

        // clear if marked
        $node->mark = false;
    }

    /*
     * Cascading cut until a root or an unmarked node is found.
     *
     * @param FibonacciHeapNode $node - the starting node
     */
    private function cascadingCut(FibonacciHeapNode $node): void
    {
        while (!is_null(($parent = $node->parent))) {
            if (!$parent->mark) {
                $parent->mark = true;
                break;
            }
            $this->cut($node, $parent);
            $node = $parent;
        }
    }

    /**
     * Add the specified node to heap root list
     *
     * @param FibonacciHeapNode $node - the node to be added
     */
    private function addToRootList(FibonacciHeapNode $node): void
    {
        if (is_null($this->minRoot)) {
            $node->next = $node;
            $node->prev = $node;
            $this->minRoot = $node;
            $this->roots = 1;
        } else {
            $node->next = $this->minRoot->next;
            $node->prev = $this->minRoot;
            $this->minRoot->next->prev = $node;
            $this->minRoot->next = $node;

            if ($node->key < $this->minRoot->key) {
                $this->minRoot = $node;
            }
            $this->roots += 1;
        }
    }
}
