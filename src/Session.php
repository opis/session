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

namespace Opis\Session;

use SessionHandlerInterface;

class Session
{  
    /** @var array   Session configuration. */
    protected $config;
    
    /** @var Flash Flash object. */
    protected $flashdata;
    
    /** @var string  Flash slot name */
    protected $flashslot;
    
    /** @var SessionHandlerInterface    Session storage. */
    protected $storage;
    
    /**
     * Constructor
     *
     * @param   SessionHandlerInterface  $storage Session store object
     * @param   array $config   (optional)  Session configuration
     */
    
    public function __construct(SessionHandlerInterface $storage, array $config = [])
    {
        $config += [
            'name' => 'opis',
            'flashslot' => 'opis:flashdata',
            'lifetime' => ini_get('session.cookie_lifetime'),
            'domain' => ini_get('session.cookie_domain'),
            'path' => ini_get('session.cookie_path'),
            'secure' => ini_get('session.cookie_secure'),
            'httponly' => ini_get('session.cookie_httponly'),
        ];
        
        $this->storage = $storage;
        $this->config = $config;
        $this->flashslot = $config['flashslot'];
        
        session_set_save_handler($storage, false);
        session_name($config['name']);
        session_set_cookie_params(
            $config['lifetime'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            $config['httponly']
        );
        
        session_start();
    }
    
    /**
     * Destructor
     */
    
    public function __destruct()
    {
        unset($_SESSION[$this->flashslot]);
        $_SESSION[$this->flashslot] = $this->flash()->toArray();
        session_write_close();
    }
    
    /**
     * Stores a value in the session.
     *
     * @param   string  $key    Session key
     * @param   mixed   $value  Session data
     */
    
    public function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Removes a value from the session.
     *
     * @param   string  $key    Session key
     */
    
    public function delete(string $key)
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * Checks if the key was set.
     *
     * @param   string  $key    Session key
     * @return  boolean
     */
    
    public function has(string $key)
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Returns a value from the session.
     * 
     * @param   string  $key        Session key
     * @param   mixed   $default    (optional) Default value
     * 
     * @return mixed
     */
    
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Gets a value from session if the key exists, otherwise associate
     * the specified key with the value returned by invoking the callback.
     * 
     * @param   string      $key        Session key
     * @param   callable    $callback   Callback function
     * 
     * @return mixed
     */
    
    public function load(string $key, callable $callback)
    {
        if(!$this->has($key)) {
            $this->set($key, $callback($key));
        }
        
        return $this->get($key);
    }
    
    /**
     * Access flash object.
     *
     * @return  Flash
     */
        
    public function flash(): Flash
    {
        if($this->flashdata === null) {
            $this->flashdata = new Flash($_SESSION[$this->flashslot] ?? []);
        }
        
        return $this->flashdata;
    }

    /**
     * Extends the lifetime of the flash data by one request.
     *
     * @param array $keys (optional) Keys to preserve
     * @return Flash
     */
    
    public function reflash(array $keys = []): Flash
    {
        return $this->flash()->reflash($keys);
    }
    
    /**
     * Clears all session data.
     */
        
    public function clear() 
    {
        $_SESSION = array();
    }
    
    /**
     * Returns the session id.
     *
     * @return  string
     */
    
    public function id(): string 
    {
        return session_id();
    }
    
    /**
     * Regenerates the session id.
     *
     * @param   boolean $keep  (optional) Delete old data associated with the old ID
     * @return  boolean
     */
        
    public function regenerate(bool $keep = false): bool 
    {
        return session_regenerate_id(!$keep);
    }
    
    /**
     * Destroys all data registered to the session.
     * 
     * @return  boolean
     */
    
    public function destroy(): bool 
    {
        return session_destroy();
    }
    
}
