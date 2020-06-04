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

namespace Opis\Session\Containers;

use Opis\Http\Request;
use Opis\Session\CookieContainer;

class RequestContainer implements CookieContainer
{

    private ?Request $request = null;

    private array $cookies = [];

    /**
     * CookieContainer constructor.
     * @param Request|null $request
     */
    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function hasCookie(string $name): bool
    {
        if ($this->request === null) {
            return false;
        }

        return $this->request->hasCookie($name);
    }

    /**
     * @inheritDoc
     */
    public function getCookie(string $name): ?string
    {
        if ($this->request === null) {
            return null;
        }

        return $this->request->getCookie($name);
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
        bool $httponly = false,
        ?string $samesite = null
    ): bool {
        $this->cookies[$name] = [
            'name' => $name,
            'value' => $value,
            'expires' => $expires,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
        ];

        return true;
    }

    /**
     * Get a list with the set cookies
     *
     * @return array
     */
    public function getAddedCookies(): array
    {
        return $this->cookies;
    }
}