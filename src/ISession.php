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

interface ISession
{
    /**
     * Returns the session id.
     *
     * @return string
     */
    public function id(): string;

    /**
     * Returns a value from the session.
     *
     * @param string $key Session key
     * @param mixed $default (optional) Default value
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null);

    /**
     * Stores a value in the session.
     *
     * @param string $key Session key
     * @param mixed $value Session data
     */
    public function set(string $key, $value);

    /**
     * Checks if the key was set.
     *
     * @param string $key Session key
     * @return boolean
     */
    public function has(string $key): bool;

    /**
     * Gets a value from session if the key exists, otherwise associate
     * the specified key with the value returned by invoking the callback.
     *
     * @param string $key Session key
     * @param callable $callback Callback function
     *
     * @return mixed|null
     */
    public function load(string $key, callable $callback);

    /**
     * Removes a value from the session.
     *
     * @param string $key Session key
     */
    public function delete(string $key);

    /**
     * Access flash object.
     *
     * @return Flash
     */
    public function flash(): Flash;

    /**
     * Clears all session data.
     */
    public function clear();

    /**
     * Regenerates the session id.
     *
     * @param boolean $keep (optional) Delete old data associated with the old ID
     * @return boolean
     */
    public function regenerate(bool $keep = false): bool;

    /**
     * Destroys all data registered to the session.
     *
     * @return boolean
     */
    public function destroy(): bool;
}