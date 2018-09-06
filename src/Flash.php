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

class Flash
{
    /** @var array */
    protected $data = [];

    /** @var array */
    protected $session;

    /**
     * @param array $session
     */
    public function __construct(array $session = [])
    {
        $this->session = $session;
    }

    /**
     * Stores a value
     *
     * @param string $key
     * @param mixed $value
     * @return Flash
     */
    public function set(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Read a value
     *
     * @param string $key Key
     * @param mixed|null $default Default value
     *
     * @return  mixed
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return array_key_exists($key, $this->session) ? $this->session[$key] : $default;
    }

    /**
     * Check if a key exists
     *
     * @param string $key Key
     * @return boolean
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data) || array_key_exists($key, $this->session);
    }

    /**
     * Remove specified key
     *
     * @param string $key Key
     * @return  Flash
     */
    public function delete(string $key): self
    {
        unset($this->data[$key]);
        unset($this->session[$key]);
        return $this;
    }

    /**
     * Read the value associated with the specified key or associate
     * the specified key with the value returned by invoking the callback.
     *
     * @param string $key Key
     * @param callable $callback Callback
     *
     * @return mixed
     */
    public function load(string $key, callable $callback)
    {
        if (!$this->has($key)) {
            $this->set($key, $callback($key));
        }

        return $this->get($key);
    }

    /**
     * Clear or replace data
     *
     * @param array $data Data
     *
     * @return Flash
     */
    public function clear(array $data = []): self
    {
        $this->data = $data;
        $this->session = [];
        return $this;
    }

    /**
     * Re-flash data
     *
     * @param array $keys Data
     *
     * @return Flash
     */
    public function reflash(array $keys = []): self
    {
        if (empty($keys)) {
            return $this->clear();
        }

        $data = $this->data + $this->session;
        return $this->clear(array_intersect_key($data, array_flip($keys)));
    }

    /**
     * Return saved data
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
