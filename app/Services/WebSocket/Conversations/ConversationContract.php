<?php

namespace App\Services\Websocket\Conversations;

interface ConversationContract
{
    /**
     * Conversations key
     *
     * @const string
     */
    public const CONVERSATIONS_KEY = 'conversations';

    /**
     * Descriptors key
     *
     * @const string
     */
    public const DESCRIPTORS_KEY = 'fds';

    /**
     * Do some init stuffs before workers started.
     *
     * @return ConversationContract
     */
    public function prepare(): ConversationContract;

    /**
     * Add multiple socket fds to a conversation.
     *
     * @param int fd
     * @param array|string conversations
     */
    public function add(int $fd, $conversations);

    /**
     * Delete multiple socket fds from a conversation.
     *
     * @param int fd
     * @param array|string conversations
     */
    public function delete(int $fd, $conversations);

    /**
     * Get all sockets by a conversation key.
     *
     * @param string conversation
     *
     * @return array
     */
    public function getClients(string $conversation);

    /**
     * Get teacher socket by a conversation key.
     *
     * @param string conversation
     *
     * @return array
     */
    public function getTeacherSocket(string $conversation);

    /**
     * Get student socket by a conversation key.
     *
     * @param string conversation
     *
     * @return array
     */
    public function getStudentSocket(string $conversation);

    /**
     * Get all conversations by a fd.
     *
     * @param int fd
     *
     * @return array
     */
    public function getConversations(int $fd);
}
