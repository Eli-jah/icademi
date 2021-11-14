<?php

namespace App\Services\Websocket\Conversations;

use Swoole\Table;

class TableConversation implements ConversationContract
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Table
     */
    protected $conversations;

    /**
     * @var Table
     */
    protected $fds;

    /**
     * TableConversation constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Do some init stuffs before workers started.
     *
     * @return ConversationContract
     */
    public function prepare(): ConversationContract
    {
        $this->initConversationsTable();
        $this->initFdsTable();

        return $this;
    }

    /**
     * Add a socket fd to multiple conversations.
     *
     * @param int fd
     * @param array|string conversations
     */
    public function add(int $fd, $conversationNames)
    {
        $conversations = $this->getConversations($fd);
        $conversationNames = is_array($conversationNames) ? $conversationNames : [$conversationNames];

        foreach ($conversationNames as $conversation) {
            $fds = $this->getClients($conversation);

            if (in_array($fd, $fds)) {
                continue;
            }

            $fds[] = $fd;
            $conversations[] = $conversation;

            $this->setClients($conversation, $fds);
        }

        $this->setConversations($fd, $conversations);
    }

    /**
     * Delete a socket fd from multiple conversations.
     *
     * @param int fd
     * @param array|string conversations
     */
    public function delete(int $fd, $conversationNames = [])
    {
        $allConversations = $this->getConversations($fd);
        $conversationNames = is_array($conversationNames) ? $conversationNames : [$conversationNames];
        $conversations = count($conversationNames) ? $conversationNames : $allConversations;

        $removeConversations = [];
        foreach ($conversations as $conversation) {
            $fds = $this->getClients($conversation);

            if (! in_array($fd, $fds)) {
                continue;
            }

            $this->setClients($conversation, array_values(array_diff($fds, [$fd])));
            $removeConversations[] = $conversation;
        }

        $this->setConversations($fd, array_values(array_diff($allConversations, $removeConversations)));
    }

    /**
     * Get all sockets by a conversation key.
     *
     * @param string $conversation
     *
     * @return array
     */
    public function getClients(string $conversation)
    {
        return $this->getValue($conversation, ConversationContract::CONVERSATIONS_KEY) ?? [];
    }

    /**
     * Get all conversations by a fd.
     *
     * @param int $fd
     *
     * @return array
     */
    public function getConversations(int $fd)
    {
        return $this->getValue($fd, ConversationContract::DESCRIPTORS_KEY) ?? [];
    }

    /**
     * @param string $conversation
     * @param array $fds
     *
     * @return TableConversation
     */
    protected function setClients(string $conversation, array $fds): TableConversation
    {
        return $this->setValue($conversation, $fds, ConversationContract::CONVERSATIONS_KEY);
    }

    /**
     * @param int $fd
     * @param array $conversations
     *
     * @return TableConversation
     */
    protected function setConversations(int $fd, array $conversations): TableConversation
    {
        return $this->setValue($fd, $conversations, ConversationContract::DESCRIPTORS_KEY);
    }

    /**
     * Init conversations table
     */
    protected function initConversationsTable(): void
    {
        $this->conversations = new Table($this->config['conversation_rows']);
        $this->conversations->column('value', Table::TYPE_STRING, $this->config['conversation_size']);
        $this->conversations->create();
    }

    /**
     * Init descriptors table
     */
    protected function initFdsTable()
    {
        $this->fds = new Table($this->config['client_rows']);
        $this->fds->column('value', Table::TYPE_STRING, $this->config['client_size']);
        $this->fds->create();
    }

    /**
     * Set value to table
     *
     * @param $key
     * @param array $value
     * @param string $table
     *
     * @return $this
     */
    public function setValue($key, array $value, string $table)
    {
        $this->checkTable($table);

        $this->$table->set($key, ['value' => json_encode($value)]);

        return $this;
    }

    /**
     * Get value from table
     *
     * @param string $key
     * @param string $table
     *
     * @return array|mixed
     */
    public function getValue(string $key, string $table)
    {
        $this->checkTable($table);

        $value = $this->$table->get($key);

        return $value ? json_decode($value['value'], true) : [];
    }

    /**
     * Check table for exists
     *
     * @param string $table
     */
    protected function checkTable(string $table)
    {
        if (! property_exists($this, $table) || ! $this->$table instanceof Table) {
            throw new \InvalidArgumentException("Invalid table name: `{$table}`.");
        }
    }
}
