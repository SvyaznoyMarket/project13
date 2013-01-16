<?php

namespace Http;

/**
 * Interface for the session.
 */
interface SessionInterface {
    /**
     * Starts the session storage.
     *
     * @return Boolean True if session started.
     *
     * @throws \RuntimeException If session fails to start.
     *
     * @api
     */
    public function start();

    /**
     * Returns the session ID.
     *
     * @return string The session ID.
     *
     * @api
     */
    public function getId();

    /**
     * Sets the session ID
     *
     * @param string $id
     *
     * @api
     */
    public function setId($id);

    /**
     * Returns the session name.
     *
     * @return mixed The session name.
     *
     * @api
     */
    public function getName();

    /**
     * Sets the session name.
     *
     * @param string $name
     *
     * @api
     */
    public function setName($name);

    /**
     * Checks if an attribute is defined.
     *
     * @param string $name The attribute name
     *
     * @return Boolean true if the attribute is defined, false otherwise
     *
     * @api
     */
    public function has($name);

    /**
     * Returns an attribute.
     *
     * @param string $name    The attribute name
     * @param mixed  $default The default value if not found.
     *
     * @return mixed
     *
     * @api
     */
    public function get($name, $default = null);

    /**
     * Sets an attribute.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @api
     */
    public function set($name, $value);

    /**
     * Returns attributes.
     *
     * @return array Attributes
     *
     * @api
     */
    public function all();

    /**
     * Removes an attribute.
     *
     * @param string $name
     *
     * @return mixed The removed value
     *
     * @api
     */
    public function remove($name);

    /**
     * Clears all attributes.
     *
     * @api
     */
    public function clear();

    /**
     * Checks if the session was started.
     *
     * @return Boolean
     */
    public function isStarted();
}
