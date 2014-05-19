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

class Flash
{
    
    protected $data = array();
    
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
        return isset($this->data[$key]) ? $this->data[$key] : $default;
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
        return isset($this->data[$key]);
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
        return $this;
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
