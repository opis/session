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

namespace Opis\Session\Handlers;

use SessionHandlerInterface;

class File implements SessionHandlerInterface
{
    /** @var string */
    protected $path;

    /** @var int */
    protected $maxLifeTime;

    /**
     * @param string $path
     */
    public function __construct(string $path, int $maxLifeTime = 0)
    {
        $this->path = $path;
        $this->maxLifeTime = $maxLifeTime > 0 ? $maxLifeTime : ini_get('session.gc_maxlifetime');

        if (!is_dir($path) && !@mkdir($path, 0777, true)) {
            throw new \RuntimeException('Session directory does not exist: "' . $path . '".');
        }

        if (!is_writable($path)) {
            throw new \RuntimeException('Session directory is not writable: "' . $path . '".');
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        // Fixes issue with Debian and Ubuntu session garbage collection
        if (mt_rand(1, 100) === 100) {
            $this->gc($this->maxLifeTime);
        }
    }

    /**
     * @inheritdoc
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function read($id)
    {
        $data = '';
        if (file_exists($this->path . '/' . $id) && is_readable($this->path . '/' . $id)) {
            $data = (string)file_get_contents($this->path . '/' . $id);
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function write($id, $data)
    {
        if (is_writable($this->path)) {
            return file_put_contents($this->path . '/' . $id, $data) === false ? false : true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function destroy($id)
    {
        if (file_exists($this->path . '/' . $id) && is_writable($this->path . '/' . $id)) {
            return unlink($this->path . '/' . $id);
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function gc($maxLifetime)
    {
        $files = glob($this->path . '/*');

        if (is_array($files)) {
            foreach ($files as $file) {
                if ((filemtime($file) + $maxLifetime) < time() && is_writable($file)) {
                    unlink($file);
                }
            }
        }

        return true;
    }

}
