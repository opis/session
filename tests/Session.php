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

namespace Opis\Session\Test;

use Opis\Session\Flash;
use Opis\Session\ISession;

class Session implements ISession
{
    private $id = 0;

    /** @var array */
    private $session;

    /** @var Flash */
    private $flash;

    /**
     * @param array $session
     */
    public function __construct(array $session = [])
    {
        $this->session = $session;
        $this->id++;
    }

    /**
     * @inheritDoc
     */
    public function id(): string
    {
        return 'id_' . $this->id;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
        }

        return $default;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value)
    {
        $this->session[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->session);
    }

    /**
     * @inheritDoc
     */
    public function load(string $key, callable $callback)
    {
        if (!$this->has($key)) {
            $this->set($key, $callback($key, $this));
        }

        return $this->get($key);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key)
    {
        unset($this->session[$key]);
    }

    /**
     * @inheritDoc
     */
    public function flash(): Flash
    {
        if ($this->flash === null) {
            $this->flash = new Flash($this->session['flash'] ?? []);
        }

        return $this->flash;
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->session = [];
    }

    /**
     * @inheritDoc
     */
    public function regenerate(bool $keep = false): bool
    {
        $this->id++;

        if (!$keep) {
            $this->session = [];
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function destroy(): bool
    {
        $this->id = 0;
        $this->session = [];
        return true;
    }
}