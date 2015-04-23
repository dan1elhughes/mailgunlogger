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

	PRIVATE function generateFooter($debug) {
		$file = $debug['file'];
		$line = $debug['line'];
		$ip = $_SERVER['REMOTE_ADDR'];

		$f = "\n\n---\n";
		$f .= "File: $file\n";
		$f .= "Line: $line\n";
		$f .= "Remote IP: $ip\n";

		return $f;
	}

	public function add($type, $body) {
		$this->mails[$type] = $body;
	}

	public function send($type, $real = true) {
		if (array_key_exists($type, $this->mails)) {

			$mail = array(
				'from'    => 'auto@xes.io',
				'to'      => 'auto@xes.io',
				'subject' => "Alert : $type",
				'text'    => $this->mails[$type] . $this->generateFooter(debug_backtrace()[0])
			);

			if ($real) {
				$this->dispatch($mail);
			}

			return $mail;
		}
	}

	public function dispatch($settings) {
		$mailThread = new Email($settings, $this->domain, $this->mailgun);
		$mailThread->start();
	}
}

class Email extends \Thread {
	private $from;
	private $to;
	private $subject;
	private $text;
	private $mailgun;

	public function __construct($settings, $domain, $mailgun) {
		$this->from = $settings['from'];
		$this->to = $settings['to'];
		$this->subject = $settings['subject'];
		$this->text = $settings['text'];

		$this->domain = $domain;
		$this->mailgun = $mailgun;
	}

	public function run() {
		$this->mailgun->sendMessage($this->domain, array(
			'from' => $this->from,
			'to' => $this->to,
			'subject' => $this->subject,
			'text' => $this->text,
		));
	}
}

?>
