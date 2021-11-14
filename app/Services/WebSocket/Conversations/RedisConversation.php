<?php

namespace App\Services\Websocket\Conversations;

use Illuminate\Support\Arr;
use Predis\Client as RedisClient;
use Predis\Pipeline\Pipeline;

/**
 * Class RedisConversation
 */
class RedisConversation implements ConversationContract
{
    /**
     * @var RedisClient
     */
    protected $redis;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $prefix = 'swoole:';

    /**
     * RedisConversation constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param RedisClient | null $redis
     *
     * @return ConversationContract
     */
    public function prepare(RedisClient $redis = null): ConversationContract
    {
        $this->setRedis($redis);
        $this->setPrefix();
        $this->cleanConversations();

        return $this;
    }

    /**
     * Set redis client.
     *
     * @param RedisClient | null $redis
     */
    public function setRedis(?RedisClient $redis = null)
    {
        if (!$redis) {
            $server = Arr::get($this->config, 'server', []);
            $options = Arr::get($this->config, 'options', []);

            // forbid setting prefix from options
            if (Arr::has($options, 'prefix')) {
                $options = Arr::except($options, 'prefix');
            }

            $redis = new RedisClient($server, $options);
        }

        $this->redis = $redis;
    }

    /**
     * Set key prefix from config.
     */
    protected function setPrefix()
    {
        if ($prefix = Arr::get($this->config, 'prefix')) {
            $this->prefix = $prefix;
        }
    }

    /**
     * Get redis client.
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * Add multiple socket fds to a conversation.
     *
     * @param int fd
     * @param array|string $conversations
     */
    public function add(int $fd, $conversations)
    {
        $conversations = is_array($conversations) ? $conversations : [$conversations];

        $this->addValue($fd, $conversations, ConversationContract::DESCRIPTORS_KEY);

        foreach ($conversations as $conversation) {
            $this->addValue($conversation, [$fd], ConversationContract::CONVERSATIONS_KEY);
        }
    }

    /**
     * Delete multiple socket fds from a conversation.
     *
     * @param int fd
     * @param array|string conversations
     */
    public function delete(int $fd, $conversations)
    {
        $conversations = is_array($conversations) ? $conversations : [$conversations];
        $conversations = count($conversations) ? $conversations : $this->getConversations($fd);

        $this->removeValue($fd, $conversations, ConversationContract::DESCRIPTORS_KEY);

        foreach ($conversations as $conversation) {
            $this->removeValue($conversation, [$fd], ConversationContract::CONVERSATIONS_KEY);
        }
    }

    /**
     * Add value to redis.
     *
     * @param        $key
     * @param array  $values
     * @param string $table
     *
     * @return $this
     */
    public function addValue($key, array $values, string $table)
    {
        $this->checkTable($table);
        $redisKey = $this->getKey($key, $table);

        $this->redis->pipeline(function (Pipeline $pipe) use ($redisKey, $values) {
            foreach ($values as $value) {
                $pipe->sadd($redisKey, $value);
            }
        });

        return $this;
    }

    /**
     * Remove value from redis.
     *
     * @param        $key
     * @param array  $values
     * @param string $table
     *
     * @return $this
     */
    public function removeValue($key, array $values, string $table)
    {
        $this->checkTable($table);
        $redisKey = $this->getKey($key, $table);

        $this->redis->pipeline(function (Pipeline $pipe) use ($redisKey, $values) {
            foreach ($values as $value) {
                $pipe->srem($redisKey, $value);
            }
        });

        return $this;
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
     * Get teacher socket by a conversation key.
     *
     * @param string $conversation
     *
     * @return array
     */
    public function getTeacherSocket(string $conversation)
    {
        return $this->getValue($conversation, ConversationContract::CONVERSATIONS_KEY) ?? [];
    }

    /**
     * Get student socket by a conversation key.
     *
     * @param string $conversation
     *
     * @return array
     */
    public function getStudentSocket(string $conversation)
    {
        return $this->getValue($conversation, ConversationContract::CONVERSATIONS_KEY) ?? [];
    }

    /**
     * Get all conversations by a fd.
     *
     * @param int fd
     *
     * @return array
     */
    public function getConversations(int $fd)
    {
        return $this->getValue($fd, ConversationContract::DESCRIPTORS_KEY) ?? [];
    }

    /**
     * Check table for conversations and descriptors.
     *
     * @param string $table
     */
    protected function checkTable(string $table)
    {
        if (!in_array($table, [ConversationContract::CONVERSATIONS_KEY, ConversationContract::DESCRIPTORS_KEY])) {
            throw new \InvalidArgumentException("Invalid table name: `{$table}`.");
        }
    }

    /**
     * Get value.
     *
     * @param string $key
     * @param string $table
     *
     * @return array
     */
    public function getValue(string $key, string $table)
    {
        $this->checkTable($table);

        $result = $this->redis->smembers($this->getKey($key, $table));

        // Try to fix occasional non-array returned result
        return is_array($result) ? $result : [];
    }

    /**
     * Get key.
     *
     * @param string $key
     * @param string $table
     *
     * @return string
     */
    public function getKey(string $key, string $table)
    {
        return "{$this->prefix}{$table}:{$key}";
    }

    /**
     * Clean all conversations.
     */
    protected function cleanConversations(): void
    {
        if (count($keys = $this->redis->keys("{$this->prefix}*"))) {
            $this->redis->del($keys);
        }
    }
}
