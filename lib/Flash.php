<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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

use Closure;

class Flash
{
    
    protected $data = array();
    
    protected $session;
    
    public function __construct(array $session = array())
    {
        $this->session = $session;
    }
    
    /**
     * Stores a value
     *
     * @access  public
     *
     * @return  \Opis\Session\Flash
     */
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    /**
     * Retrive a value
     * 
     * @access  public
     *
     * @param   string  $key        Key
     * @param   mixed   $default    (optional)  Default value
     * 
     * @return  \Opis\Session\Flash
     */
    
    public function get($key, $default = null)
    {
        if(isset($this->data[$key]))
        {
            return $this->data[$key];
        }
        
        return isset($this->session[$key]) ? $this->session[$key] : $default;
    }
    
    /**
     * Retrive the value associated with the specified key or associate
     * the specified key with the value returned by invoking the callback.
     *
     * @access  public
     *
     * @param   string      $key        Key
     * @param   \Closure    $callback   Callback   
     *
     * @return  mixed
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
     * Removes specified key
     *
     * @access  public
     *
     * @return  \Opis\Session\Flash
     */
    
    public function delete($key)
    {
        unset($this->data[$key]);
        return $this;
    }
    
    /**
     * Check if a key exists
     *
     * @access  public
     *
     * @return  boolean
     */
    
    public function has($key)
    {
        return isset($this->data[$key]) || isset($this->session[$key]);
    }
    
    /**
     * Clear or replace data
     * 
     * @access  public
     *
     * @param   array   $data   (optional) Data
     * 
     * @return  \Opis\Session\Flash
     */
    
    public function clear(array $data = array())
    {
        $this->data = $data;
        $this->session = array();
        return $this;
    }
    
    /**
     * Reflash data
     * 
     * @access  public
     *
     * @param   array   $keys   (optional) Data
     * 
     * @return  \Opis\Session\Flash
     */
    
    public function reflash(array $keys = array())
    {
        $data = empty($keys) ? $this->session : array_intersect_key($this->session, array_flip($keys));
        return $this->clear($data);
    }
    
    /**
     * Return saved data
     * 
     * @access  public
     * 
     * @return  array
     */
    
    public function toArray()
    {
        return $this->data;
    }
}
