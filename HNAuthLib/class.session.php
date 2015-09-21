<?php

    // Example usage
    // $session = new Session();
    // $session->setItem('username', 'john');
    // $session->save();
    //
    // $session = new Session();
    // $session->getItem('username');
    //
    // $session = new Session();
    // $session->destroy();

    class Session {
        private $_sessionPath = '';
        private $_sessionName = '';
        private $_sessionId = '';
        private $_items = array();

        public function __construct($name = 'SESSIONID', $prefix = 'sess_'){
            $this->_sessionName = $name;

            if ( isset($_COOKIE[$this->_sessionName]) ){
                $this->_sessionId = $_COOKIE[$this->_sessionName];
            }else{
                $this->_sessionId = md5(uniqid());
                setcookie($this->_sessionName, $this->_sessionId);
            }

            $this->_sessionPath = session_save_path() . '/' . $prefix . $this->_sessionId;

            if ( file_exists($this->_sessionPath) ){
                $data = file_get_contents($this->_sessionPath);
                $this->_items = unserialize($data);
            }
        }

        public function setItem($key, $value){
            $this->_items[$key] = $value;
        }

        public function getItem($key){
            if ( isset($this->_items[$key]) ){
                return $this->_items[$key];
            }
            return false;
        }

        public function getSessionID(){
            return $this->_sessionId;
        }

        public function destroy(){
            if ( file_exists($this->_sessionPath) ){
                unlink($this->_sessionPath);
            }
        }

        public function save(){
            $handle = fopen($this->_sessionPath, 'w');
            fwrite($handle, serialize($this->_items));
            fclose($handle);
        }
    }

?>