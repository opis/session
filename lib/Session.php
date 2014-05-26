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
     * @param   array                       $callback   (optional)  Session configuration
     */
    
    public function __construct(SessionHandlerInterface $storage = null, array $config = array())
    {
        
        $this->storage = $storage;
        
        $config += array(
            'name' => 'opis',
            'lifetime' => ini_get('session.cookie_lifetime'),
            'domain' => ini_get('session.cookie_domain'),
            'path' => ini_get('session.cookie_path'),
            'secure' => ini_get('session.cookie_secure'),
            'httponly' => ini_get('session.cookie_httponly'),
            'gc_maxlifetime' => ini_get('session.gc_maxlifetime'),
            'flashslot' => 'opis:flashdata',
        );
        
        $this->config = $config;
        
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
            
            if($storage instanceof SessionAwareInterface)
            {
                $storage->setSessionContainer($this);
            }
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
     * Checks if the key was set.
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
     * Gets a value from session if the key exists, otherwise associate
     * the specified key with the value returned by invoking the callback.
     *
     * @access  public
     * 
     * @param   string      $key        Session key
     * @param   \Closure    $callback   Callback function
     * 
     * @return mixed
     */
    
    public function load($key, Closure $callback)
    {
        if(!$this->has($key))
        {
            $this->set($key, $callback($key));
        }
        
        return $this->get($key);
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
    
    /**
     * Session's path
     *
     * @access  public
     *
     * @return  string
     */
    
    public function path()
    {
        return $this->config['path'];
    }
    
    /**
     * Session's domain
     *
     * @access  public
     *
     * @return  string
     */
    
    public function domain()
    {
        return $this->config['domain'];
    }
    
    /**
     * Session's life time
     *
     * @access  public
     *
     * @return  int
     */
    
    public function lifetime()
    {
        return (int) $this->config['lifetime'];
    }
    
    
    /**
     * Garbage collector's max life time
     *
     * @access  public
     *
     * @return  int
     */
    
    public function gcmaxLifetime()
    {
        return (int) $this->config['gc_maxlifetime'];
    }
    
    /**
     * Check if session is http-only
     *
     * @access  public
     *
     * @return  boolean
     */
    
    public function isHttpOnly()
    {
        return (bool) $this->config['httponly'];
    }
    
    /**
     * Check if session is sescure
     *
     * @access  public
     *
     * @return  boolean
     */
    
    public function isSecure()
    {
        return (bool) $this->config['secure'];
    }
    
}
