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

use Opis\Session\SessionStorage;
use Opis\Session\SessionHandlerInterface;
use Opis\Database\Database as SQLDatabase;
use PDOException;

class Database extends SessionStorage implements SessionHandlerInterface
{
    protected $maxLifetime;
    
    protected $database;
    
    protected $table;
    
    /**
     * Constructor
     *
     * @access public
     * 
     */
    
    public function __construct(SQLDatabase $database, $table, $maxLifetime = 0)
    {
        $this->database = $database;
        $this->table = $table;
        $this->maxLifetime = $maxLifetime > 0 ? $maxLifetime : ini_get('session.gc_maxlifetime');
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
            $result = $this->database
                            ->select($this->table)
                            ->columns(array('data'))
                            ->where('id', $id)
                            ->execute('data');
                            
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
            $result = $this->database
                            ->select($this->table)
                            ->count()
                            ->where('id', $id)
                            ->execute(0);
            if($result != 0)
            {
                return (bool) $this->database->update($this->table)
                                    ->columns(array('data' => $data, 'expires' => time() + $this->maxLifetime))
                                    ->where('id', $id)
                                    ->execute();
            }
            else
            {
                return $this->database->insert($this->table, array('id', 'data', 'expires'))
                                    ->values(array($id, $data, time() + $this->maxLifetime))
                                    ->execute();
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
            return (bool) $this->database->delete($this->table)->where('id', $id)->execute();
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
            return (bool) $this->database->delete($this->table)->where('expires', time(), '<')->execute();
        }
        catch(PDOException $e)
        {
            return false;
        }
    }
}