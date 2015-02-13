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

namespace Opis\Session\Storage;

use SessionHandlerInterface;
use MongoCollection;

class Mongo implements SessionHandlerInterface
{
    protected $maxLifetime;
    
    protected $mongo;
    
    /**
     * Constructor
     *
     * @access public
     * 
     */
    
    public function __construct(MongoCollection $mongo, $maxLifetime = 0)
    {
        $this->mongo = $mongo;
        $this->maxLifetime = $maxLifetime > 0 ? $maxLifetime : ini_get('session.gc_maxlifetime');
    }
    
    /**
     * Destructor.
     *
     * @access public
     */

    public function __destruct()
    {
        // Fixes issue with Debian and Ubuntu session garbage collection
        
        if(mt_rand(1, 100) === 100)
        {
            $this->gc(0);
        }
    }


    /**
     * Open session.
     *
     * @access  public
     * @param   string   $savePath     Save path
     * @param   string   $sessionName  Session name
     * @return  boolean
     */

    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * Close session.
     *
     * @access  public
     * @return  boolean
     */

    public function close()
    {
        return true;
    }

    /**
     * Returns session data.
     *
     * @access  public
     * @param   string  $id  Session id
     * @return  string
     */

    public function read($id)
    {
        $result = $this->mongo->findOne(array('_id' => $id), array('data'));
        
        return $result === null ? '' : $result['data'];
    }

    /**
     * Writes data to the session.
     *
     * @access  public
     * 
     * @param   string  $id    Session id
     * @param   string  $data  Session data
     *
     * @return  boolean
     */

    public function write($id, $data)
    {
        $this->mongo->save(array('_id' => $id, 'expires' => time() + $this->maxLifetime, 'data' => $data));
        return true;
    }

    /**
     * Destroys the session.
     *
     * @access  public
     * @param   string   $id  Session id
     * @return  boolean
     */

    public function destroy($id)
    {
        $this->mongo->remove(array('_id' => $id));
    }

    /**
     * Garbage collector.
     *
     * @access  public
     *
     * @param   int      $maxLifetime  Lifetime in secods
     * 
     * @return  boolean
     */

    public function gc($maxLifetime)
    {
        $this->mongo->remove(array('expires' => array('$lt' => time())));
        return true;
    }
}
