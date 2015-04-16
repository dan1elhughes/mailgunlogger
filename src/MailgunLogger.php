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

	public function add($type, $body) {
		$this->mails[$type] = $body;
	}

	public function send($type, $real = true) {
		if (array_key_exists($type, $this->mails)) {

			$mail = array(
				'from'    => 'auto@xes.io',
				'to'      => 'auto@xes.io',
				'subject' => "Alert : $type",
				'text'    => $this->mails[$type]
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
