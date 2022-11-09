<?php


namespace ImapBankParser\Banks;

use ImapBankParser\Imap\Connection;

class Bank extends Connection
{
	const CREDITAS = 'Creditas';
	const CSOB = 'Csob';
	const EXPOBANK = 'Expobank';
	const FIOBANK = 'Fio';
	const KB = 'Kb';
	const MONETA = 'Moneta';
	const RAIFFEISEN = 'Raiffeisen';

	/**
	 * @var mixed
	 */
	private $bank;

	/**
	 * @param string $hostname
	 * @param string $username
	 * @param string $password
	 * @param string $port
	 * @param string $flags
	 * @param array $parameters
	 */
	public function __construct__construct(
		string $hostname,
		string $username,
		string $password,
		string $port,
		string $flags,
		array $parameters
	)
	{
		parent::__construct($hostname, $username, $password, $port, $flags, $parameters);
	}

	/**
	 * Create parser instance of defined Bank
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setBank(string $name): self
	{
		$bankClass = 'ImapBankParser\Bank\\' . $name;
		$this->bank = new $bankClass();

		return $this;
	}

	/**
	 * Parse emails by bank rules
	 *
	 * @return $this
	 */
	public function parseEmails(): self
	{
		$this->bank->setMessages($this->messages);
		$this->bank->parseEmails();

		foreach($this->bank->getParsed() as $key => $email) {
			if(!$email) {
				unset($this->messages[$key]);
			}
		}

		return $this;
	}

	/**
	 * Return parsed messages as array
	 *
	 * @return array
	 */
	public function getParsed(): array
	{
		return $this->bank->getParsed();
	}
}
