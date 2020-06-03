<?php
/* ===========================================================================
 * Copyright 2019-2020 Zindex Software
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

namespace Opis\Session\Handlers;

use Opis\Session\{
    SessionData,SessionHandler
};

class Memory implements SessionHandler
{

    /** @var SessionData[] */
    private array $sessions = [];

    /**
     * @inheritDoc
     */
    public function open(string $name): void
    {
        // Do nothing
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        // Do nothing
    }

    /**
     * @inheritDoc
     */
    public function create(string $session_id, int $expire, array $data = []): SessionData
    {
        return $this->sessions[$session_id] = new SessionData($session_id, $expire, $data);
    }

    /**
     * @inheritDoc
     */
    public function update(SessionData $data): bool
    {
        $this->sessions[$data->id()] = $data;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete(SessionData $data): bool
    {
        return $this->deleteById($data->id());
    }

    /**
     * @inheritDoc
     */
    public function deleteById(string $session_id): bool
    {
        unset($this->sessions[$session_id]);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultipleById(array $session_ids): int
    {
        $count = 0;

        foreach ($session_ids as $session_id) {
            if ($this->deleteById($session_id)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function read(string $session_id): ?SessionData
    {
        return $this->sessions[$session_id] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function gc(int $maxLifeTime): bool
    {
        $timestamp = time() - $maxLifeTime;

        foreach ($this->sessions as $key => $session) {
            if ($session->isExpiredAt($timestamp)) {
                unset($this->sessions[$key]);
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function generateSessionId(): string
    {
        return session_create_id();
    }
}