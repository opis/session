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
use Closure;
use SessionHandlerInterface;

class Session
{
    /** @var    array   Session configuration. */
    protected $config;
    
    /** @var    \Opis\Session\Flash Flash object. */
    protected $flashdata;
    
    /** @var    string  Flash slot name */
    protected $flashslot;
    
    /** @var    \SessionHandlerInterface    Session storage. */
    protected $storage;
    
    /**
     * Constructor
     *
     * @access  public
     *
     * @param   \SessionHandlerInterface    $storage    (optional)  Session storage
     * @param   \Closure                    $callback   (optional)  Config callback
     */
    
    public function __construct(SessionHandlerInterface $storage = null, Closure $callback = null)
    {
        
        $this->storage = $storage;
        
        $config = new SessionConfig();
        
        if($callback !== null)
        {
            $callback($config);
        }
        
        $this->config = $config->toArray();
        
        if($storage !== null)
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
        
        session_name($this->config['name']);
        
        session_set_cookie_params(
            $this->config['lifetime'],
            $this->config['path'],
            $this->config['domain'],
            $this->config['secure'],
            $this->config['httponly']
        );
        
        $this->flashslot = $this->config['flashslot'];
        
        session_start();
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    
    public function __destruct()
    {
        unset($_SESSION[$this->flashslot]);
        $_SESSION[$this->flashslot] = $this->flash()->toArray();
    }
    
    /**
     * @deprecated  since 2.0.0
     */
    
    public function remember($key, $value)
    {
        return $this->set($key, $value);
    }
    
    /**
     * @deprecated  since 2.0.0
     */
    
    public function forget($key)
    {
        return $this->delete($key);
    }
    
    /**
     * Stores a value in the session.
     *
     * @access  public
     * 
     * @param   string  $key    Session key
     * @param   mixed   $value  Session data
     */
    
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Removes a value from the session.
     *
     * @access  public
     * 
     * @param   string  $key    Session key
     */
    
    public function delete($key)
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * Returns TRUE if key exists in the session and FALSE if not.
     *
     * @access  public
     * 
     * @param   string  $key    Session key
     * 
     * @return  boolean
     */
    
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Returns a value from the session.
     *
     * @access  public
     * 
     * @param   string  $key        Session key
     * @param   mixed   $default    (optional) Default value
     * 
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
            $this->flashdata = new Flash(isset($_SESSION[$this->flashslot]) ? $_SESSION[$this->flashslot] : array());
        }
        
        return $this->flashdata;
    }
    
    /**
     * Extends the lifetime of the flash data by one request.
     *
     * @access  public
     * 
     * @param   array   $keys   (optional) Keys to preserve
     */
    
    public function reflash(array $keys = array())
    {
        return $this->flash()->reflash($keys);
    }
    
    /**
     * Clears all session data.
     *
     * @access  public
     */
        
    public function clear()
    {
        $_SESSION = array();
    }
    
    /**
     * Returns the session id.
     *
     * @access  public
     * 
     * @return  string
     */
    
    public function id()
    {
        return session_id();
    }
    
    /**
     * Regenerates the session id.
     *
     * @access  public
     * 
     * @param   boolean $deleteOld  (optional) Delete the session data associated with the old id
     * 
     * @return  boolean
     */
        
    public function regenerate($deleteOld = true)
    {
        return session_regenerate_id($deleteOld);
    }
    
    /**
     * Destroys all data registered to the session.
     *
     * @access  public
     * 
     * @return  boolean
     */
    
    public function destroy()
    {
        return session_destroy();
    }
    
    /**
     * @deprecated  since 2.0.0
     */
    
    public function dispose()
    {
        return $this->destroy();
    }
    
}
