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

use RuntimeException;
use Opis\Session\{
    SessionData, SessionHandler
};

class File implements SessionHandler
{

    private string $path;

    /** @var resource */
    private $fp;

    private ?string $filename = null;

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
    public function open(string $name): void
    {
        $this->filename = $this->getHeaderFilename($name);

        if (!is_file($this->filename)) {
            file_put_contents($this->filename, $this->serializeHeaderData([]));
        }
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->releaseLock();
        $this->filename = null;
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
        if (!$this->acquireLock()) {
            return 0;
        }

        $count = 0;
        $data = $this->unserializeHeaderData($this->getHeaderContent());

        foreach ($session_ids as $session_id) {
            if (!isset($data[$session_id])) {
                continue;
            }
            unset($data[$session_id]);
            @unlink($this->getSessionDataFilename($session_id));
            $count++;
        }

        $this->setHeaderContent($this->serializeHeaderData($data));

        $this->releaseLock();

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function read(string $session_id): ?SessionData
    {
        $file = $this->getSessionDataFilename($session_id);

        if (!is_file($file)) {
            return null;
        }

        if (!$this->acquireLock()) {
            return null;
        }

        $content = $this->unserializeSessionData(file_get_contents($file));
        $this->releaseLock();

        return $content;
    }

    /**
     * @inheritDoc
     */
    public function gc(int $maxLifeTime): bool
    {
        if (!$this->acquireLock()) {
            return false;
        }

        $timestamp = time() - $maxLifeTime;

        $data = $this->unserializeHeaderData($this->getHeaderContent());

        $zeroed = [];
        $changed = false;

        foreach ($data as $key => $expire) {
            if ($expire === 0) {
                $zeroed[] = $key;
            } elseif ($expire < $timestamp) {
                unset($data[$key]);
                @unlink($this->getSessionDataFilename($key));
                $changed = true;
            }
        }

        foreach ($zeroed as $key) {
            $file = $this->getSessionDataFilename($key);
            if (!is_file($file)) {
                unset($data[$key]);
                $changed = true;
                continue;
            }

            $session = $this->unserializeSessionData(file_get_contents($file));
            if ($session === null) {
                unset($data[$key]);
                $changed = true;
                continue;
            }

            $expire = $session->expiresAt();
            if ($expire !== 0) {
                // Somehow the expires is not 0, check to see if expired
                if ($expire < $timestamp) {
                    unset($data[$key]);
                    @unlink($file);
                    $changed = true;
                }
            } elseif ($session->updatedAt() < $timestamp) { // Check last update
                unset($data[$key]);
                @unlink($file);
                $changed = true;
            }

            unset($session);
        }

        unset($zeroed);

        if ($changed) {
            $this->setHeaderContent($this->serializeHeaderData($data));
        }

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
        if ($data === '') {
            return [];
        }
        return unserialize($data);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getHeaderFilename(string $name): string
    {
        return $this->path . DIRECTORY_SEPARATOR . $name . '.session';
    }

    /**
     * @param string $session_id
     * @return string
     */
    protected function getSessionDataFilename(string $session_id): string
    {
        return $this->path . DIRECTORY_SEPARATOR . $session_id;
    }

    /**
     * @param SessionData $session
     * @return bool
     */
    private function createOrUpdate(SessionData $session): bool
    {
        if (!$this->acquireLock()) {
            return false;
        }

        $data = $this->unserializeHeaderData($this->getHeaderContent());

        $session_id = $session->id();
        file_put_contents($this->getSessionDataFilename($session_id), $this->serializeSessionData($session));

        if (!isset($data[$session_id]) || $data[$session_id] !== $session->expiresAt()) {
            $data[$session_id] = $session->expiresAt();
            $this->setHeaderContent($this->serializeHeaderData($data));
        }

        return $this->releaseLock();
    }

    /**
     * @return bool
     */
    private function acquireLock(): bool
    {
        if (!$this->filename) {
            return false;
        }

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

    /**
     * @return string
     */
    private function getHeaderContent(): string
    {
        $fp = $this->fp;
        fseek($fp, 0, SEEK_SET);
        $content = '';
        while (!feof($fp)) {
            $content .= fread($fp, 8192);
        }
        return $content;
    }

    /**
     * @param string $content
     */
    private function setHeaderContent(string $content)
    {
        $fp = $this->fp;
        fseek($fp, 0, SEEK_SET);
        ftruncate($fp, strlen($content));
        fwrite($fp, $content);
    }
}