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

namespace Opis\Session\Test;

use Opis\Session\Handlers\File;
use Opis\Session\SessionData;

class JSONHandler extends File
{
    public function __construct()
    {
        parent::__construct(__DIR__ . '/sessions');
    }

    public function serializeHeaderData(array $data): string
    {
        return json_encode($data);
    }

    public function unserializeHeaderData(string $data): array
    {
        $arr = json_decode($data, true);
        if (!is_array($arr)) {
            print_r('yyy'. $data);;
        }
        return $arr;
        return json_decode($data, true);
    }

    public function serializeSessionData(SessionData $session): string
    {
        return json_encode([
            'id' => $session->id(),
            'expires_at' => $session->expiresAt(),
            'created_at' => $session->createdAt(),
            'updated_at' => $session->updatedAt(),
            'data' => $session->data(),
        ]);
    }

    public function unserializeSessionData(string $data): ?SessionData
    {
        $data = json_decode($data, true);

        return new SessionData(
            $data['id'],
            $data['expires_at'],
            $data['data'],
            $data['created_at'],
            $data['updated_at']
        );
    }

    public function getSessionDataFilename(string $path, string $session_id): string
    {
        return parent::getSessionDataFilename($path, $session_id) . '.json';
    }

    public function getHeaderFilename(string $path, string $name): string
    {
        return parent::getHeaderFilename($path, $name) . '.json';
    }
}