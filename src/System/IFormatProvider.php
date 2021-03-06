<?php

namespace System 
{
    /**
     * Provides a mechanism for retrieving an object to control formatting.
     *
     * @access public
     * @name IFormatProvider
     * @package System
     */
    interface IFormatProvider 
    {
        /**
         * Returns an object that provides formatting services for the specified type.
         *
         * @access public
         * @param \System\Type $formatType An object that specifies the type of format object to return.
         * @return object An instance of the object specified by formatType, if the System.IFormatProvider implementation can supply that type of object; otherwise, null.
         */
        function getFormat(Type $formatType);
    }
}
