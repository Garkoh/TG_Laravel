<?php

/*
 * This file is part of the Predis package.
 *
 * (c) 2009-2020 Daniele Alessandri
 * (c) 2021-2025 Till Krüss
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Connection;

/**
 * Interface defining a container for connection parameters.
 *
 * The actual list of connection parameters depends on the features supported by
 * each connection backend class (please refer to their specific documentation),
 * but the most common parameters used through the library are:
 *
 * @property string $scheme             Connection scheme, such as 'tcp' or 'unix'.
 * @property string $host               IP address or hostname of Redis.
 * @property int    $port               TCP port on which Redis is listening to.
 * @property int    $protocol           Version of RESP protocol.
 * @property string $path               Path of a UNIX domain socket file.
 * @property string $alias              Alias for the connection.
 * @property float  $timeout            Timeout for the connect() operation.
 * @property float  $read_write_timeout Timeout for read() and write() operations.
 * @property bool   $persistent         Leaves the connection open after a GC collection.
 * @property string $conn_uid           Unique identifier of connection, needs to create a multiple persistent connections to the same resource.
 * @property string $username           Username to access Redis (see the AUTH command).
 * @property string $password           Password to access Redis (see the AUTH command).
 * @property string $database           Database index (see the SELECT command).
 * @property bool   $async_connect      Performs the connect() operation asynchronously.
 * @property bool   $tcp_nodelay        Toggles the Nagle's algorithm for coalescing.
 * @property bool   $client_info        Whether to set LIB-NAME and LIB-VER when connecting.
 * @property bool   $cache              (Relay only) Whether to use in-memory caching.
 * @property string $serializer         (Relay only) Serializer used for data serialization.
 * @property string $compression        (Relay only) Algorithm used for data compression.
 */
interface ParametersInterface
{
    /**
     * Checks if the specified parameters is set.
     *
     * @param string $parameter Name of the parameter.
     *
     * @return bool
     */
    public function __isset($parameter);

    /**
     * Returns the value of the specified parameter.
     *
     * @param string $parameter Name of the parameter.
     *
     * @return mixed|null
     */
    public function __get($parameter);

    /**
     * Returns basic connection parameters as a valid URI string.
     *
     * @return string
     */
    public function __toString();

    /**
     * Returns an array representation of the connection parameters.
     *
     * @return array
     */
    public function toArray();
}
