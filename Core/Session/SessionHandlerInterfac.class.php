<?php declare(strict_types = 1);
/**
 * 不会使用,例子
 */
Interface SessionHandlerInterface  {

	public function close () : bool;

	public function destroy ( string $session_id ) : bool;

	public function gc ( int $maxlifetime ) : bool;

	public function open ( string $save_path , string $name ) : bool;

	public function read ( string $session_id ) : string;

	public function write ( string $session_id , string $session_data ) : bool;
}