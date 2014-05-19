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

abstract class SessionStorage implements SessionInterface
{
    /** @var    \Opis\Session\Flash Flash object. */
    protected $flashdata;
    
    /** @var    string              Flash slot. */
    protected $flashSlot = 'opis:flashdata';
    

    /**
     * Destructor.
     *
     * @access public
     */

    public function __destruct()
    {
        unset($_SESSION[$this->flashSlot]);
        $_SESSION[$this->flashSlot] = $this->flash()->toArray();
    }
    
    
    /**
     * Store a value in the session.
     *
     * @access public
     * @param string $key Session key
     * @param mixed $value Session data
     */

    public function remember($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Removes a value from the session.
     *
     * @access public
     * @param string $key Session key
     */

    public function forget($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Returns TRUE if key exists in the session and FALSE if not.
     *
     * @access public
     * @param string $key Session key
     * @return boolean
     */

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Returns a value from the session.
     *
     * @access public
     * @param string $key Session key
     * @param mixed $default (optional) Default value
     * @return mixed
     */

    public function get($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Access flash object.
     *
     * @access  public
     * 
     * @return  \Opis\Session\Flash
     */

    public function flash()
    {
        if($this->flashdata === null)
        {
            $this->flashdata = new Flash();
        }
        
        return $this->flashdata;
    }
    
    /**
     * Extends the lifetime of the flash data by one request.
     *
     * @access public
     * 
     * @param   array   $keys   (optional) Keys to preserve
     */
    
    public function reflash(array $keys = array())
    {
        $flashdata = empty($keys) ? $_SESSION[$this->flashSlot] : array_intersect_key($_SESSION[$this->flashSlot], array_flip($keys));
        $this->flash()->clear(array_merge($this->flashdata, $flashdata));
    }
    
    /**
     * Clears all session data.
     *
     * @access public
     */
    
    public function clear()
    {
        $_SESSION = array();
    }
    
    /**
     * Returns the session id.
     *
     * @access public
     * @return string
     */
    
    public function id()
    {
        return session_id();
    }
    
    /**
     * Regenerates the session id.
     *
     * @access public
     * @param boolean $deleteOld (optional) Delete the session data associated with the old id?
     * @return boolean
     */
    
    public function regenerate($deleteOld = true)
    {
        return session_regenerate_id($deleteOld);
    }

    /**
     * Destroys all data registered to the session.
     *
     * @access public
     * @return boolean
     */
    
    public function dispose()
    {
        return session_destroy();
    }
    
}
