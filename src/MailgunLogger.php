<?php namespace xes;

class MailgunLogger {
	private $mailgun;
	private $domain;
	private $to;
	private $from;

	private $mails = array(
		'error' => 'Invalid type specified (todo, get line and file)'
	);

	public function __construct(array $settings) {
		$this->mailgun = $settings['mailgun'];
		$this->domain = $settings['domain'];
		$this->to = $settings['to'];
		$this->from = $settings['from'];
	}

	private function generateFooter($debug) {
		$file = $debug['file'];
		$line = $debug['line'];
		$ip = $_SERVER['REMOTE_ADDR'];
		$host = gethostname();

		$f = "\n\n---\n";
		$f .= "File: $file\n";
		$f .= "Line: $line\n";
		$f .= "Remote IP: $ip\n";
		$f .= "Server: $host\n";

		return $f;
	}

	public function add($type, $body) {
		$this->mails[$type] = $body;
	}

	public function send($type, $real = true) {
		if (array_key_exists($type, $this->mails)) {

			$mail = array(
				'from'    => $this->from,
				'to'      => $this->to,
				'subject' => "Alert triggered on $this->domain: $type",
				'text'    => $this->mails[$type] . $this->generateFooter(debug_backtrace()[0])
			);

			if ($real) {
				$this->dispatch($mail);
			} else {
				return $mail;
			}
		}
	}

	public function dispatch($settings) {
		$this->mailgun->sendMessage($this->domain, array(
			'from' => $settings['from'],
			'to' => $settings['to'],
			'subject' => $settings['subject'],
			'text' => $settings['text'],
		));
	}
}
?>
