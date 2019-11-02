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

namespace Opis\Session\Handlers;

use RuntimeException;
use Opis\Session\ISessionHandler;
use Opis\Session\SessionData;

class File implements ISessionHandler
{
    /** @var string */
    private $path;

    /** @var resource */
    private $fp;

    /** @var string */
    private $filename;

    /**
     * DefaultHandler constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            if (!@mkdir($path, 0775, true)) {
                throw new RuntimeException('Could not create path');
            }
        }

        if (!is_dir($path)) {
            throw new RuntimeException('Path must be a directory');
        }

        if (!is_writable($path)) {
            throw new RuntimeException('Path must be writable');
        }

        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function open(string $name)
    {
        $this->filename = $this->getHeaderFilename($this->path, $name);

        if (!file_exists($this->filename)) {
            file_put_contents($this->filename, $this->serializeHeaderData([]));
        }
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        $this->releaseLock();
    }

    /**
     * @inheritDoc
     */
    public function create(string $session_id, int $expire, array $data = []): SessionData
    {
        $session = new SessionData($session_id, $expire, $data);
        $this->createOrUpdate($session);
        return $session;
    }

    /**
     * @inheritDoc
     */
    public function update(SessionData $data): bool
    {
        return $this->createOrUpdate($data);
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
        return (bool) $this->deleteMultipleById([$session_id]);
    }

    /**
     * @inheritDoc
     */
    public function deleteMultipleById(array $session_ids): int
    {
        $this->acquireLock();

        $content = '';

        while (!feof($this->fp)) {
            $content .= fread($this->fp, 1024);
        }

        $count = 0;
        $data = $this->unserializeHeaderData($content);

        foreach ($session_ids as $session_id) {
            if (!isset($data[$session_id])) {
                continue;
            }
            unset($data[$session_id]);
            unlink($this->getSessionDataFilename($this->path, $session_id));
            $count++;
        }

        $content = $this->serializeHeaderData($data);

        fseek($this->fp, 0);
        ftruncate($this->fp, strlen($content));
        fwrite($this->fp, $content);

        $this->releaseLock();

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function read(string $session_id): ?SessionData
    {
        $file = $this->getSessionDataFilename($this->path, $session_id);

        if (!file_exists($file)) {
            return null;
        }

        $this->acquireLock();
        $content = $this->unserializeSessionData(file_get_contents($file));
        $this->releaseLock();
        return $content;
    }

    /**
     * @inheritDoc
     */
    public function gc(): bool
    {
        $this->acquireLock();

        $content = '';

        while (!feof($this->fp)) {
            $content .= fread($this->fp, 1024);
        }

        $tmp = $this->unserializeHeaderData($content);
        $timestamp = time();

        $data = [];

        foreach ($tmp as $key => $expire) {
            if ($expire > $timestamp) {
                $data[$key] = $expire;
            } else {
                unlink($this->getSessionDataFilename($this->path, $key));
            }
        }

        unset($tmp);

        $content = $this->serializeHeaderData($data);

        fseek($this->fp, 0);
        ftruncate($this->fp, strlen($content));
        fwrite($this->fp, $content);

        return $this->releaseLock();
    }

    /**
     * @inheritDoc
     */
    public function generateSessionId(): string
    {
        return session_create_id();
    }

    /**
     * @param SessionData $session
     * @return string
     */
    protected function serializeSessionData(SessionData $session): string
    {
        return serialize($session);
    }

    /**
     * @param string $data
     * @return SessionData|null
     */
    protected function unserializeSessionData(string $data): ?SessionData
    {
        $data = @unserialize($data);
        return $data instanceof SessionData ? $data : null;
    }

    /**
     * @param array $data
     * @return string
     */
    protected function serializeHeaderData(array $data): string
    {
        return serialize($data);
    }

    /**
     * @param string $data
     * @return array
     */
    protected function unserializeHeaderData(string $data): array
    {
        return unserialize($data);
    }

    /**
     * @param string $path
     * @param string $name
     * @return string
     */
    protected function getHeaderFilename(string $path, string $name): string
    {
        return $path . DIRECTORY_SEPARATOR . $name . '.session';
    }

    /**
     * @param string $path
     * @param string $session_id
     * @return string
     */
    protected function getSessionDataFilename(string $path, string $session_id): string
    {
        return $path . DIRECTORY_SEPARATOR . $session_id;
    }

    /**
     * @param SessionData $session
     * @return bool
     */
    private function createOrUpdate(SessionData $session): bool
    {
        $this->acquireLock();

        $content = '';

        while (!feof($this->fp)) {
            $content .= fread($this->fp, 1024);
        }

        $data = $this->unserializeHeaderData($content);

        $session_id = $session->id();
        $file = $this->getSessionDataFilename($this->path, $session_id);
        file_put_contents($file, $this->serializeSessionData($session));

        if (!isset($data[$session_id]) || $data[$session_id] !== $session->expiresAt()) {
            $data[$session_id] = $session->expiresAt();
            $content = $this->serializeHeaderData($data);
            fseek($this->fp, 0);
            ftruncate($this->fp, strlen($content));
            fwrite($this->fp, $content);
        }

        return $this->releaseLock();
    }

    /**
     * @return bool
     */
    private function acquireLock(): bool
    {
        if ($this->fp === null) {
            $this->fp = fopen($this->filename, 'c+');
        }

        return flock($this->fp, LOCK_EX);
    }

    /**
     * @return bool
     */
    private function releaseLock(): bool
    {
        if ($this->fp !== null) {
            flock($this->fp, LOCK_UN);
            fclose($this->fp);
            $this->fp = null;
        }
        return true;
    }
}