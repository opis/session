<?php
/* ===========================================================================
 * Copyright 2019 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Session;

class Session
{
    /** @var array */
    private $config;

    /** @var ISessionHandler */
    private $handler;

    /** @var ICookieContainer */
    private $container;

    /** @var array */
    private $data;

    /** @var Flash|null */
    private $flash;

    /** @var SessionData */
    private $session;

    /**
     * Session constructor.
     *
     * @param ISessionHandler $handler
     * @param array $config
     * @param ICookieContainer|null $container
     */
    public function __construct(ISessionHandler $handler, array $config = [], ICookieContainer $container = null)
    {
        if ($container === null) {
            $container = new CookieContainer();
        }

        $config += [
            'flash_slot' => '__flash__',
            'gc_probability' => 1,
            'gc_divisor' => 100,
            'cookie_name' => 'PHPSESSIONID',
            'cookie_lifetime' => 3600,
            'cookie_path' => '/',
            'cookie_domain' => '',
            'cookie_secure' => false,
            'cookie_httponly' => false,
        ];

        $session = null;
        $handler->open($config['cookie_name']);

        if ($container->hasCookie($config['cookie_name'])) {
            $session = $handler->read($container->getCookie($config['cookie_name']));
        }

        if ($session === null) {
            $session_id = $handler->generateSessionId();
            $expire = time() + $config['cookie_lifetime'];
            $container->setCookie(
                $config['cookie_name'],
                $session_id,
                $expire,
                $config['cookie_path'],
                $config['cookie_domain'],
                $config['cookie_secure'],
                $config['cookie_httponly']
            );
            $session = $handler->create($session_id, $expire);
        }

        $this->data = $session->data();
        $this->session = $session;
        $this->config = $config;
        $this->handler = $handler;
        $this->container = $container;

        // GC
        try {
            $r = random_int(1, $config['gc_divisor']);
        } catch (\Exception $e) {
            $r = rand(1, $config['gc_divisor']);
        }
        if ($r <= $config['gc_probability']) {
            $handler->gc();
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if ($this->session !== null) {
            $this->data[$this->config['flash_slot']] = $this->flash()->toArray();
            $this->session->setData($this->data);
            $this->handler->update($this->session);
        }

        $this->handler->close();
    }

    /**
     * Returns the session id.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->session->id();
    }

    /**
     * Returns a timestamp representing the session's creation date.
     *
     * @return int
     */
    public function createdAt(): int
    {
        return $this->session->createdAt();
    }

    /**
     * Returns a timestamp representing the last time this session was accessed.
     *
     * @return int
     */
    public function updatedAt(): int
    {
        return $this->session->updatedAt();
    }

    /**
     * Returns a timestamp representing the expiration date of the current session.
     *
     * @return int
     */
    public function expiresAt(): int
    {
        return $this->session->expiresAt();
    }

    /**
     * Extends the lifetime of the session.
     *
     * @param int $seconds
     * @return bool
     */
    public function extendLifetime(int $seconds): bool
    {
        if ($this->session === null || $seconds < 0) {
            return false;
        }

        $config = $this->config;
        $expire = $this->session->expiresAt() + $seconds;

        $this->container->setCookie(
            $config['cookie_name'],
            $this->session->id(),
            $expire,
            $config['cookie_path'],
            $config['cookie_domain'],
            $config['cookie_secure'],
            $config['cookie_httponly']
        );

        $this->session->setExpirationDate($expire);

        return true;
    }

    /**
     * Returns a value from the session.
     *
     * @param string $key Session key
     * @param mixed|null $default (optional) Default value
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * Stores a value in the session.
     *
     * @param string $key Session key
     * @param mixed $value Session data
     */
    public function set(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Checks if the key was set.
     *
     * @param string $key Session key
     * @return boolean
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Gets a value from session if the key exists, otherwise associate
     * the specified key with the value returned by invoking the callback.
     *
     * @param string $key Session key
     * @param callable $callback Callback function
     *
     * @return mixed|null
     */
    public function load(string $key, callable $callback)
    {
        if (!$this->has($key)) {
            $this->set($key, $callback($key));
        }

        return $this->get($key);
    }

    /**
     * Removes a value from the session.
     *
     * @param string $key Session key
     */
    public function delete(string $key)
    {
        unset($this->data[$key]);
    }

    /**
     * Access flash object.
     *
     * @return Flash
     */
    public function flash(): Flash
    {
        if ($this->flash === null) {
            $this->flash = new Flash($this->data[$this->config['flash_slot']] ?? []);
        }

        return $this->flash;
    }

    /**
     * Clears all session data.
     *
     * @param bool $flash
     */
    public function clear(bool $flash = true)
    {
        $f = $this->flash();
        if ($flash) {
            $f->clear();
        }
        $this->data = [];
    }

    /**
     * Regenerates the session id.
     *
     * @param boolean $keep (optional) Keep old data associated with the old ID
     * @return boolean
     */
    public function regenerate(bool $keep = false): bool
    {
        if ($this->session === null) {
            return false;
        }

        $session_id = $this->handler->generateSessionId();

        $session = $this->handler->create($session_id, $this->session->expiresAt(), $this->session->data());

        if ($session === null) {
            return false;
        }

        if (!$keep) {
            $this->handler->delete($this->session);
        }

        $this->session = $session;

        return true;
    }

    /**
     * Destroys all data registered to the session and the session itself.
     *
     * @return boolean
     */
    public function destroy(): bool
    {
        if ($this->session === null) {
            return false;
        }

        $this->clear();
        $config = $this->config;

        if (!$this->handler->delete($this->session)) {
            return false;
        }

        $this->session = null;

        $this->container->setCookie(
            $config['cookie_name'],
            '',
            1,
            $config['cookie_path'],
            $config['cookie_domain'],
            $config['cookie_secure'],
            $config['cookie_httponly']
        );

        return true;
    }
}