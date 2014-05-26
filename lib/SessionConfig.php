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

class SessionConfig
{
    
    protected $config = array();
    
    /**
     * Set session's name
     *
     * @access  public
     *
     * @param   string  $value  (optional) Name
     *
     * @return  \Opis\Session\SessionConfig Self reference
     */
    
    public function name($value = 'opis')
    {
        $this->config['name'] = (string) $value;
        return $this;
    }
    
    /**
     * Set session's lifetime
     *
     * @access  public
     *
     * @param   int $value  (optional) Lifetime in seconds
     *
     * @return  \Opis\Session\SessionConfig Self reference
     */
        
    public function lifetime($value = 0)
    {
        $this->config['lifetime'] = (int) $value;
        return $this;
    }
    
    /**
     * Set session's path
     *
     * @access  public
     *
     * @param   string  $value  (optional) Path
     *
     * @return  \Opis\Session\SessionConfig Self reference
     */
    
    public function path($value = '/')
    {
        $this->config['path'] = (string) $value;
        return $this;
    }
    
    
    /**
     * Set session's domain
     *
     * @access  public
     *
     * @param   string  $value  (optional) Domain
     *
     * @return  \Opis\Session\SessionConfig Self reference
     */
        
    public function domain($value = 'loclahost')
    {
        $this->config['domain'] = (string) $value;
        return $this;
    }
    
    /**
     * Set session's secure flag
     *
     * @access  public
     *
     * @param   bool    $value  (optional) TRUE or FALSE
     *
     * @return  \Opis\Session\SessionConfig Self reference
     */
    
    public function secure($value = true)
    {
        $this->config['secure'] = (bool) $value;
        return $this;
    }
    
    /**
     * Set session's http-only flag
     *
     * @access  public
     *
     * @param   bool    $value  (optional) TRUE or FALSE
     *
     * @return  \Opis\Session\SessionConfig Self reference
     */
    
    public function httpOnly($value = true)
    {
        $this->config['httponly'] = (bool) $value;
        return $this;
    }
    
}
