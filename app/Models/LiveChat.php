<?php

namespace Fickrr\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Live chat between landing page visitors and the admin.
 */
class LiveChat
{
    public const MAX_MESSAGE = 2000;

    public static function ready(): bool
    {
        try {
            return Schema::hasTable('live_chat_conversations');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function settings()
    {
        try {
            $row = DB::table('live_chat_settings')->where('id', 1)->first();
        } catch (\Throwable $e) {
            return null;
        }
        return $row;
    }

    public static function enabled(): bool
    {
        $s = self::settings();
        return $s !== null && (int) $s->enabled === 1;
    }

    public static function updateSettings(array $data): void
    {
        $data['updated_at'] = now();
        if (DB::table('live_chat_settings')->where('id', 1)->exists()) {
            DB::table('live_chat_settings')->where('id', 1)->update($data);
        } else {
            DB::table('live_chat_settings')->insert($data + ['id' => 1, 'created_at' => now()]);
        }
    }

    public static function findByToken(?string $token)
    {
        $token = (string) $token;
        if (!preg_match('/^[A-Za-z0-9]{48}$/', $token)) {
            return null;
        }
        return DB::table('live_chat_conversations')->where('token', $token)->first();
    }

    public static function createConversation(array $data)
    {
        $data['token'] = bin2hex(random_bytes(24));
        $data['status'] = 'open';
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $id = DB::table('live_chat_conversations')->insertGetId($data);
        return DB::table('live_chat_conversations')->where('id', $id)->first();
    }

    public static function addMessage(int $conversationId, string $sender, string $body): int
    {
        $now = date('Y-m-d H:i:s');
        $id = DB::table('live_chat_messages')->insertGetId([
            'conversation_id' => $conversationId,
            'sender' => $sender,
            'body' => $body,
            'created_at' => $now,
        ]);
        $counter = $sender === 'visitor' ? 'unread_admin' : 'unread_visitor';
        DB::table('live_chat_conversations')->where('id', $conversationId)->update([
            'last_message_at' => $now,
            'updated_at' => $now,
            'status' => 'open',
            $counter => DB::raw($counter.' + 1'),
        ]);
        return $id;
    }

    public static function messages(int $conversationId, int $afterId = 0, int $limit = 200)
    {
        return DB::table('live_chat_messages')
            ->where('conversation_id', $conversationId)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit($limit)
            ->get(['id', 'sender', 'body', 'created_at'])
            ->map(function ($m) {
                return [
                    'id' => (int) $m->id,
                    'sender' => $m->sender,
                    'body' => $m->body,
                    'at' => \Carbon\Carbon::parse($m->created_at, config('app.timezone'))->toIso8601String(),
                ];
            })
            ->values();
    }

    public static function markRead(int $conversationId, string $reader): void
    {
        $data = $reader === 'admin' ? ['unread_admin' => 0] : ['unread_visitor' => 0, 'visitor_seen_at' => date('Y-m-d H:i:s')];
        DB::table('live_chat_conversations')->where('id', $conversationId)->update($data);
    }

    public static function totalUnreadForAdmin(): int
    {
        try {
            return (int) DB::table('live_chat_conversations')->where('status', 'open')->sum('unread_admin');
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /** Clean a chat message: plain text, trimmed, limited length. */
    public static function cleanBody($body): string
    {
        $body = strip_tags((string) $body);
        $body = preg_replace("/\r\n?/", "\n", $body);
        $body = preg_replace("/\n{3,}/", "\n\n", $body);
        return mb_substr(trim($body), 0, self::MAX_MESSAGE);
    }
}
