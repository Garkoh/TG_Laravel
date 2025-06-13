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

use InvalidArgumentException;
use Predis\Client;
use Predis\Command\RawCommand;
use ReflectionClass;
use UnexpectedValueException;

/**
 * Standard connection factory for creating connections to Redis nodes.
 */
class Factory implements FactoryInterface
{
    private $defaults = [];

    protected $schemes = [
        'tcp' => 'Predis\Connection\StreamConnection',
        'unix' => 'Predis\Connection\StreamConnection',
        'tls' => 'Predis\Connection\StreamConnection',
        'redis' => 'Predis\Connection\StreamConnection',
        'rediss' => 'Predis\Connection\StreamConnection',
    ];

    /**
     * Checks if the provided argument represents a valid connection class
     * implementing Predis\Connection\NodeConnectionInterface. Optionally,
     * callable objects are used for lazy initialization of connection objects.
     *
     * @param mixed $initializer FQN of a connection class or a callable for lazy initialization.
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function checkInitializer($initializer)
    {
        if (is_callable($initializer)) {
            return $initializer;
        }

        $class = new ReflectionClass($initializer);

        if (!$class->isSubclassOf('Predis\Connection\NodeConnectionInterface')) {
            throw new InvalidArgumentException(
                'A connection initializer must be a valid connection class or a callable object.'
            );
        }

        return $initializer;
    }

    /**
     * {@inheritdoc}
     */
    public function define($scheme, $initializer)
    {
        $this->schemes[$scheme] = $this->checkInitializer($initializer);
    }

    /**
     * {@inheritdoc}
     */
    public function undefine($scheme)
    {
        unset($this->schemes[$scheme]);
    }

    /**
     * {@inheritdoc}
     */
    public function create($parameters)
    {
        if (!$parameters instanceof ParametersInterface) {
            $parameters = $this->createParameters($parameters);
        }

        $scheme = $parameters->scheme;

        if (!isset($this->schemes[$scheme])) {
            throw new InvalidArgumentException("Unknown connection scheme: '$scheme'.");
        }

        $initializer = $this->schemes[$scheme];

        if (is_callable($initializer)) {
            $connection = call_user_func($initializer, $parameters, $this);
        } else {
            $connection = new $initializer($parameters);
            $this->prepareConnection($connection);
        }

        if (!$connection instanceof NodeConnectionInterface) {
            throw new UnexpectedValueException(
                'Objects returned by connection initializers must implement ' .
                "'Predis\Connection\NodeConnectionInterface'."
            );
        }

        return $connection;
    }

    /**
     * Assigns a default set of parameters applied to new connections.
     *
     * The set of parameters passed to create a new connection have precedence
     * over the default values set for the connection factory.
     *
     * @param array $parameters Set of connection parameters.
     */
    public function setDefaultParameters(array $parameters)
    {
        $this->defaults = $parameters;
    }

    /**
     * Returns the default set of parameters applied to new connections.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return $this->defaults;
    }

    /**
     * Creates a connection parameters instance from the supplied argument.
     *
     * @param mixed $parameters Original connection parameters.
     *
     * @return ParametersInterface
     */
    protected function createParameters($parameters)
    {
        if (is_string($parameters)) {
            $parameters = Parameters::parse($parameters);
        } else {
            $parameters = $parameters ?: [];
        }

        if ($this->defaults) {
            $parameters += $this->defaults;
        }

        return new Parameters($parameters);
    }

    /**
     * Prepares a connection instance after its initialization.
     *
     * @param NodeConnectionInterface $connection Connection instance.
     */
    protected function prepareConnection(NodeConnectionInterface $connection)
    {
        $parameters = $connection->getParameters();

        if (!empty($parameters->password)) {
            $cmdAuthArgs = [$parameters->protocol, 'AUTH'];

            if (empty($parameters->username)) {
                $parameters->username = 'default';
            }

            array_push($cmdAuthArgs, $parameters->username, $parameters->password);
            array_push($cmdAuthArgs, 'SETNAME', 'predis');

            $connection->addConnectCommand(
                new RawCommand('HELLO', $cmdAuthArgs)
            );
        } else {
            $connection->addConnectCommand(
                new RawCommand('HELLO', [$parameters->protocol ?? 2, 'SETNAME', 'predis'])
            );
        }

        $connection->addConnectCommand(
            new RawCommand('CLIENT', ['SETINFO', 'LIB-NAME', 'predis'])
        );

        $connection->addConnectCommand(
            new RawCommand('CLIENT', ['SETINFO', 'LIB-VER', Client::VERSION])
        );

        if (isset($parameters->database) && strlen($parameters->database)) {
            $connection->addConnectCommand(
                new RawCommand('SELECT', [$parameters->database])
            );
        }
    }
}
