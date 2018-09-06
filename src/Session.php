<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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

use SessionHandlerInterface;

class Session implements ISession
{
    /** @var array */
    protected $config;

    /** @var Flash */
    protected $flashdata;

    /** @var string */
    protected $flashslot;

    /** @var SessionHandlerInterface */
    protected $handler;

    /**
     * @param SessionHandlerInterface $handler
     * @param array $config
     */
    public function __construct(SessionHandlerInterface $handler, array $config = [])
    {
        $config += [
            'name' => 'opis',
            'flashslot' => 'opis:flashdata',
            'lifetime' => ini_get('session.cookie_lifetime'),
            'domain' => ini_get('session.cookie_domain'),
            'path' => ini_get('session.cookie_path'),
            'secure' => ini_get('session.cookie_secure'),
            'httponly' => ini_get('session.cookie_httponly'),
        ];

        $this->handler = $handler;
        $this->config = $config;
        $this->flashslot = $config['flashslot'];

        session_set_save_handler($handler, false);
        session_name($config['name']);
        session_set_cookie_params(
            $config['lifetime'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            $config['httponly']
        );

        session_start();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($_SESSION[$this->flashslot]);
        $_SESSION[$this->flashslot] = $this->flash()->toArray();
        session_write_close();
    }

    /**
     * @inheritdoc
     */
    public function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function delete(string $key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * @inheritdoc
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * @inheritdoc
     */
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * @inheritdoc
     */
    public function load(string $key, callable $callback)
    {
        if (!$this->has($key)) {
            $this->set($key, $callback($key));
        }

        return $this->get($key);
    }

    /**
     * @inheritdoc
     */
    public function flash(): Flash
    {
        if ($this->flashdata === null) {
            $this->flashdata = new Flash($_SESSION[$this->flashslot] ?? []);
        }

        return $this->flashdata;
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $_SESSION = [];
    }

    /**
     * @inheritdoc
     */
    public function id(): string
    {
        return session_id();
    }

    /**
     * @inheritdoc
     */
    public function regenerate(bool $keep = false): bool
    {
        return session_regenerate_id(!$keep);
    }

    /**
     * @inheritdoc
     */
    public function destroy(): bool
    {
        return session_destroy();
    }
}
