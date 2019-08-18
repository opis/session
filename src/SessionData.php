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

final class SessionData implements \Serializable
{
    /** @var string */
    private $id;

    /** @var int */
    private $expire;

    /** @var array */
    private $data;

    /** @var int */
    private $createdAt;

    /** @var int|null */
    private $updatedAt;

    /**
     * SessionData constructor.
     * @param string $id
     * @param int $expire
     * @param array $data
     * @param int|null $createdAt
     * @param int|null $updatedAt
     */
    public function __construct(string $id, int $expire, array $data = [], int $createdAt = null, int $updatedAt = null)
    {
        if ($createdAt === null) {
            $updatedAt = $createdAt = time();
        }

        if ($createdAt > $updatedAt) {
            $updatedAt = $createdAt;
        }

        $this->id = $id;
        $this->expire = $expire;
        $this->data = $data;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function expiresAt(): int
    {
        return $this->expire;
    }

    /**
     * @param int $timestamp
     */
    public function setExpirationDate(int $timestamp)
    {
        $this->expire = $timestamp;
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * @param array $array
     * @return SessionData
     */
    public function setData(array $array): self
    {
        $this->data = $array;
        return $this;
    }

    /**
     * @return int
     */
    public function createdAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function updatedAt(): int
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize([
            'id' => $this->id,
            'expire' => $this->expire,
            'createdAt' => $this->createdAt,
            'updatedAt' => time(),
            'data' => $this->data,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->id = $data['id'];
        $this->expire = $data['expire'];
        $this->createdAt = $data['createdAt'];
        $this->updatedAt = $data['updatedAt'];
        $this->data = $data['data'];
    }
}