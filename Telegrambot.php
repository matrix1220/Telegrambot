<?
/**
* @author The Matrix (https://t.me/matrix_1220)
* @version 1.0
* Telegram Bot Client
*/
class Telegrambot {
	private $token;
	function __construct($token) {
		$this->token=$token;
	}
	function call($method,$data=[]) {
		$sock = fsockopen("ssl://api.telegram.org", 443, $errno, $errstr, 30);
		if (!$sock) throw new Exception("$errstr ($errno)");
		$data = json_encode($data);
		fwrite($sock, "POST /bot".$this->token."/".$method." HTTP/1.0\r\n");
		fwrite($sock, "Host: api.telegram.org\r\n");
		fwrite($sock, "Content-type: application/json\r\n");
		fwrite($sock, "Content-length: " . strlen($data) . "\r\n");
		fwrite($sock, "\r\n");
		fwrite($sock, $data);
		$headers = ""; while ($str = trim(fgets($sock))) $headers .= "$str\n";
		$body = ""; while (!feof($sock)) $body .= fgets($sock, 4096);
		fclose($sock);

		$body=json_decode($body);
		if(!$body->ok) throw new Exception($body->description);
		return $body->result;
	}
	static function reply($method,$data) {
		echo json_encode(["method"=>$method]+$data);
	}
	function sendMessage($id,$text,$options=[]) {
		//if(!isset($options['parse_mode'])) $options['parse_mode']='HTML';
		return $this->call('sendMessage',array('chat_id'=>$id,'text'=>$text)+$options);
	}
	// static function replySendMessage($text,$options=[]) {
	// 	self::reply('sendMessage',['chat_id'=>$id,'text'=>$text]+$options); // $id !!!
	// }
	static function HTML($t) {
		return str_replace(['&','<','>'],['&amp;','&lt;','&gt;'],$t);
	}
}
?>