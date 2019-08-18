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

use Opis\Session\ICookieContainer;

class CookieContainer implements ICookieContainer
{
    private $cookies;

    private $newCookies = [];

    public function __construct(array $cookies = [])
    {
        $this->cookies = $cookies;
    }

    /**
     * @inheritDoc
     */
    public function hasCookie(string $name): bool
    {
        return array_key_exists($name, $this->cookies);
    }

    /**
     * @inheritDoc
     */
    public function getCookie(string $name): ?string
    {
        return $this->cookies[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setCookie(
        string $name,
        string $value,
        int $expires = 0,
        string $path = "",
        string $domain = "",
        bool $secure = false,
        bool $http_only = false
    ): bool {
        $this->newCookies[$name] = [$value, $expires, $path, $domain, $secure, $http_only];
        return true;
    }
}