<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
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

use RuntimeException;
use SessionHandlerInterface;
use Opis\Session\Storage\Native;

class Session implements SessionInterface
{
    
    protected $storage;
    
    public function __construct(SessionStorage $storage = null, $name = 'opis')
    {
        if($storage == null)
        {
            $storage = new Native();
        }
        
        $this->storage = $storage;
        
        if($storage instanceof SessionHandlerInterface)
        {
            session_set_save_handler
            (
                array($storage, 'open'),
                array($storage, 'close'),
                array($storage, 'read'),
                array($storage, 'write'),
                array($storage, 'destroy'),
                array($storage, 'gc')
            );
        }
        session_name($name);
        session_start();
    }
    
    
    public function remember($key, $value)
    {
        return $this->storage->remember($key, $value);
    }
    
    public function forget($key)
    {
        return $this->storage->forget($key);
    }
    
    public function has($key)
    {
        return $this->storage->has($key);
    }
    
    public function get($key, $default = null)
    {
        return $this->storage->get($key, $default);
    }
    
    public function flash()
    {
        return $this->storage->flash();
    }
    
    public function reflash(array $keys = array())
    {
        return $this->storage->reflash($keys);
    }
    
    public function clear()
    {
        return $this->storage->clear();
    }
    
    public function id()
    {
        return $this->storage->id();
    }
    
    public function regenerate($deleteOld = true)
    {
        return $this->storage->regenerate($deleteOld);
    }
    
    public function dispose()
    {
        return $this->storage->dispose();
    }
    
}
