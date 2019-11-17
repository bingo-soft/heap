<?php

namespace heap;

/**
 * Interface AddressableHeapInterface
 *
 * @package heap
 */
interface AddressableHeapInterface
{
    /**
     * Insert a new node to the heap
     *
     * @param int $key - node key
     * @param null|mixed $value - node value
     *
     * @return AddressableHeapHandleInterface
     *
     * @throws Exception
     */
    public function insert(int $key, $value = null): AddressableHeapHandleInterface;

    /**
     * Find heap node with the minimal key
     *
     * @return AddressableHeapHandleInterface
     *
     * @throws Exception
     */
    public function findMin(): AddressableHeapHandleInterface;

    /**
     * Get an element with the minimum key.
     *
     * @return AddressableHeapHandleInterface
     */
    public function deleteMin(): AddressableHeapHandleInterface;

    /**
     * Check if the heap is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Get the number of elements in the heap.
     *
     * @return int
     */
    public function size(): int;

    /**
     * Clear all the elements in the heap
     */
    public function clear(): void;
}
