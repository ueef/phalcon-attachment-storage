<?php

namespace Phalcon\Extended\Attachment\Interfaces {

    interface File
    {
        function __construct(array $parameters = []);


        /**
         * @param array $parameters
         * @return null
         */
        function move(array $parameters);


        /**
         * @param array $parameters
         * @return static
         */
        function copy(array $parameters);


        /**
         * @param string $path
         * @return null
         */
        function store($path);


        /**
         * @param string $alias
         * @return bool
         */
        function exists($alias = null);


        /**
         * @param array $parameters
         * @return null
         */
        function assign(array $parameters);


        /**
         * @param bool $deleteEmptyDir
         * @return null
         */
        function delete($deleteEmptyDir = true);


        /**
         * @return null
         */
        function deleteAlias($alias);


        /**
         * @return array
         */
        function toArray();


        /**
         * @return string
         */
        function getDirUrl();


        /**
         * @return string
         */
        function getDirPath();


        /**
         * @param string $alias
         * @return string
         */
        function getUrl($alias = null);


        /**
         * @param string $alias
         * @return string
         */
        function getPath($alias = null);


        /**
         * @return integer
         */
        function getKey();


        /**
         * @return string
         */
        function getName();


        /**
         * @return string
         */
        function getTime();


        /**
         * @return array
         */
        function getAliases();


        /**
         * @return array
         */
        function getAllAliases();


        /**
         * @return string
         */
        function getMimeType();


        /**
         * @return string
         */
        function getExtension();


        /**
         * @param string $alias
         * @return string
         */
        function getFilename($alias = null);
    }
}