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

namespace Opis\Session\Storage;

use PDOException;
use SessionHandlerInterface;
use Opis\Session\SessionStorage;
use Opis\Database\Connection;
use Opis\Database\Database as OpisDatabase;

class Database extends SessionStorage implements SessionHandlerInterface
{
    protected $maxLifetime;
    
    protected $db;
    
    protected $table;
    
    protected $columns;
    
    /**
     * Constructor
     *
     * @access public
     * 
     */
    
    public function __construct(Connection $connection, $table, $maxLifetime = 0, $columns = null)
    {
        $this->db = new OpisDatabase($connection);
        $this->table = $table;
        $this->maxLifetime = $maxLifetime > 0 ? $maxLifetime : ini_get('session.gc_maxlifetime');
        
        if($columns === null || !is_array($columns))
        {
            $columns = array();
        }
        
        $columns += array(
            'id' => 'id',
            'data' => 'data',
            'expires' => 'expires',
        );
        
        $this->columns = $columns;
        
    }
    
    /**
     * Destructor.
     *
     * @access public
     */

    public function __destruct()
    {
        parent::__destruct();
        
        session_write_close();
        
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
        try
        {
            $result = $this->db->from($this->table)
                               ->where($this->columns['id'], $id)
                               ->column($this->columns['data']);
            
            return $result === false ? '' : $result;
        }
        catch(PDOException $e)
        {
            return '';
        }
    }

    /**
     * Writes data to the session.
     *
     * @access  public
     * @param   string  $id    Session id
     * @param   string  $data  Session data
     */

    public function write($id, $data)
    {
        try
        {
            $result = $this->db->from($this->table)->where($this->columns['id'], $id)->count();
            
            if($result != 0)
            {
                return (bool) $this->db->update($this->table)
                                        ->where($this->columns['id'], $id)
                                        ->set(array(
                                            $this->columns['data'] => $data,
                                            $this->columns['expires'] => time() + $this->maxLifetime,
                                        ))
                                        ->execute();
            }
            else
            {
                return $this->db->into($this->table)
                                ->insert(array(
                                    $this->columns['id'] => $id,
                                    $this->columns['data'] => $data,
                                    $this->columns['expires'] => time() + $this->maxLifetime
                                ));
            }
        }
        catch(PDOException $e)
        {
            return false;
        }
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
        try
        {
            return (bool) $this->db->from($this->table)
                                    ->where($this->columns['id'], $id)
                                    ->delete();
        }
        catch(PDOException $e)
        {
            return false;
        }
    }

    /**
     * Garbage collector.
     *
     * @access  public
     * @param   int      $maxLifetime  Lifetime in secods
     * @return  boolean
     */

    public function gc($maxLifetime)
    {
        try
        {
            return (bool) $this->db->from($this->table)
                                    ->where($this->columns['expires'], time(), '<')
                                    ->delete();
        }
        catch(PDOException $e)
        {
            return false;
        }
    }
}