<?php

namespace ImapBankParser\Imap;

use Ddeboer\Imap\ConnectionInterface;
use Ddeboer\Imap\Search\Text\Subject;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Server;

abstract class Connection
{
    const MAILBOX_INBOX = 'INBOX';

    private Server $imapServer;

    protected ConnectionInterface $connection;

    public array $messages = [];

    private string $box = self::MAILBOX_INBOX;

    private SearchExpression $searchExpression;

    /**
     * Connection constructor.
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $port
     * @param string $flags
     * @param array $parameters
     */
    public function __construct(
        string $hostname,
        string $username,
        string $password,
        string $port,
        string $flags,
        array $parameters
    )
    {
        $this->imapServer = new Server($hostname, $port, $flags, $parameters);
        $this->connection = $this->imapServer->authenticate($username, $password);
    }

    /**
     * Get all emails in defined box
     *
     * @return $this
     */
    public function getEmails(): self
    {
        foreach ($this->connection->getMailbox($this->box)->getMessages(isset($this->searchExpression) ? $this->searchExpression : null) as $message) {
            $this->messages[] = $message;
        }

        return $this;
    }

    /**
     * Delete emails from box
     *
     * @return $this
     */
    public function deleteEmails() {
        if(!empty($this->messages)) {
            foreach ($this->messages as $message) {
                $message->delete();
            }
            $this->connection->expunge();
        }

        return $this;
    }

    /**
     * Return all founded emails as array
     *
     * @return array
     */
    public function asArray(): array
    {
        return $this->messages;
    }

    /**
     * Set box for finding emails
     *
     * @param string $box
     */
    public function setBox(string $box): void
    {
        $this->box = $box;
    }

    /**
     * Set search expression
     *
     * @param SearchExpression $expression
     * @return $this
     */
    public function setSearchExpression(SearchExpression $expression): self
    {
        $this->searchExpression = $expression;

        return $this;
    }
}