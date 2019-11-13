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

interface ISessionHandler
{
    /**
     * @param string $name
     * @return mixed
     */
    public function open(string $name);

    /**
     * @return void
     */
    public function close();

    /**
     * @param string $session_id
     * @param int $expire
     * @param array $data
     * @return SessionData
     */
    public function create(string $session_id, int $expire, array $data = []): SessionData;

    /**
     * @param SessionData $data
     * @return bool
     */
    public function update(SessionData $data): bool;

    /**
     * @param SessionData $data
     * @return bool
     */
    public function delete(SessionData $data): bool;

    /**
     * @param string $session_id
     * @return bool
     */
    public function deleteById(string $session_id): bool;

    /**
     * @param string[] $session_ids
     * @return int
     */
    public function deleteMultipleById(array $session_ids): int;

    /**
     * @param string $session_id
     * @return SessionData|null
     */
    public function read(string $session_id): ?SessionData;

    /**
     * @param int $maxLifeTime
     * @return bool
     */
    public function gc(int $maxLifeTime): bool;

    /**
     * @return string
     */
    public function generateSessionId(): string;
}