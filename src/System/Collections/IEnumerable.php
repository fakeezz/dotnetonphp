<?php

namespace System\Collections {

    /**
     * Exposes the enumerator, which supports a simple iteration over a non-generic collection.
     *
     * @access public
     * @name IEnumerable
     * @package System
     * @subpackage Collections
     */
    interface IEnumerable {

        /**
         * Returns an enumerator that iterates through a collection.
         *
         * @access public
         * @return \System\Collections\IEnumerator An \System\Collections\IEnumerator object that can be used to iterate through the collection.
         */
        function getEnumerator();
    }
}