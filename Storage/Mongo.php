<?php

namespace Opis\Session\Storage;

use Opis\Session\SessionHandlerInterface;

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
        $result = $this->mongo->findOne(array('_id' => $id), array('data'));
        
        return $result === null ? '' : $result['data'];
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
        $this->mongo->save(array('_id' => $id, 'expries' => time() + $this->maxLifetime, 'data' => $data));
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
     * @param   int      $maxLifetime  Lifetime in secods
     * @return  boolean
     */

    public function gc($maxLifetime)
    {
        $this->mongo->remove(array('expires' => array('$lt' => time())));
        return true;
    }
}