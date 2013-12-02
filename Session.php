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
use Opis\Session\Storage\Native;

class Session
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
                array($storage, 'sessionOpen'),
                array($storage, 'sessionClose'),
                array($storage, 'sessionRead'),
                array($storage, 'sessionWrite'),
                array($storage, 'sessionDestroy'),
                array($storage, 'sessionGarbageCollector')
            );
        }
        session_name($name);
        session_start();
    }
    
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->storage, $name), $arguments);
    }
    
}