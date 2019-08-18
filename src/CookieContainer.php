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

class CookieContainer implements ICookieContainer
{
    /**
     * @inheritDoc
     */
    public function hasCookie(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getCookie(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setCookie(
        string $name,
        string $value,
        int $expire = 0,
        string $path = "",
        string $domain = "",
        bool $secure = false,
        bool $http_only = false
    ): bool {
        return setcookie($name, $value, $expire, $path, $domain, $secure, $http_only);
    }
}