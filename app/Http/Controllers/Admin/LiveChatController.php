<?php

namespace Fickrr\Http\Controllers\Admin;

use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\LiveChat;
use Fickrr\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin > Live Chat: answer questions from landing page visitors.
 */
class LiveChatController extends Controller
{
    public function index()
    {
        $setting['setting'] = Settings::editGeneral(1);
        return view('admin.live-chat', [
            'setting' => $setting,
            'ready' => LiveChat::ready(),
            'chatSettings' => LiveChat::settings(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        if (!LiveChat::ready()) {
            return redirect()->back()->with('error', 'Please run the database migration first (php artisan migrate).');
        }
        $request->validate([
            'enabled' => 'required|in:0,1',
            'welcome_message' => 'nullable|string|max:500',
        ]);
        LiveChat::updateSettings([
            'enabled' => (int) $request->input('enabled'),
            'welcome_message' => $request->input('welcome_message'),
        ]);
        return redirect()->back()->with('success', 'Live chat settings saved.');
    }

    public function conversations(Request $request)
    {
        if (!LiveChat::ready()) {
            return $this->json(['ok' => true, 'conversations' => [], 'unread' => 0]);
        }
        $status = $request->input('status') === 'closed' ? 'closed' : 'open';

        $rows = DB::table('live_chat_conversations as c')
            ->leftJoin('landing_pages as lp', 'lp.lp_id', '=', 'c.lp_id')
            ->where('c.status', $status)
            ->orderByDesc('c.last_message_at')
            ->limit(100)
            ->get(['c.id', 'c.visitor_name', 'c.visitor_email', 'c.country_code', 'c.page_path',
                   'c.unread_admin', 'c.last_message_at', 'c.status', 'lp.lp_title']);

        $ids = $rows->pluck('id')->all();
        $last = [];
        if ($ids !== []) {
            $lastIds = DB::table('live_chat_messages')->whereIn('conversation_id', $ids)
                ->groupBy('conversation_id')->selectRaw('MAX(id) as id')->pluck('id');
            foreach (DB::table('live_chat_messages')->whereIn('id', $lastIds)->get(['conversation_id', 'sender', 'body']) as $m) {
                $last[$m->conversation_id] = $m;
            }
        }

        $list = $rows->map(function ($c) use ($last) {
            $m = $last[$c->id] ?? null;
            return [
                'id' => (int) $c->id,
                'name' => $c->visitor_name ?: ('Visitor #'.$c->id),
                'email' => $c->visitor_email,
                'country' => $c->country_code,
                'page' => $c->lp_title ?: $c->page_path,
                'path' => $c->page_path,
                'unread' => (int) $c->unread_admin,
                'status' => $c->status,
                'last' => $m ? mb_substr(($m->sender === 'admin' ? 'You: ' : '').$m->body, 0, 90) : '',
                'at' => $c->last_message_at ? \Carbon\Carbon::parse($c->last_message_at, config('app.timezone'))->toIso8601String() : null,
            ];
        })->values();

        return $this->json(['ok' => true, 'conversations' => $list, 'unread' => LiveChat::totalUnreadForAdmin()]);
    }

    public function messages(Request $request, $id)
    {
        $conversation = $this->find($id);
        if (!$conversation) {
            return $this->json(['ok' => false], 404);
        }
        LiveChat::markRead((int) $conversation->id, 'admin');
        return $this->json([
            'ok' => true,
            'messages' => LiveChat::messages((int) $conversation->id, (int) $request->input('after', 0), 500),
            'visitor_seen_at' => $conversation->visitor_seen_at
                ? \Carbon\Carbon::parse($conversation->visitor_seen_at, config('app.timezone'))->toIso8601String() : null,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $conversation = $this->find($id);
        if (!$conversation) {
            return $this->json(['ok' => false], 404);
        }
        $body = LiveChat::cleanBody($request->input('message'));
        if ($body === '') {
            return $this->json(['ok' => false, 'error' => 'empty'], 422);
        }
        LiveChat::addMessage((int) $conversation->id, 'admin', $body);
        LiveChat::markRead((int) $conversation->id, 'admin');
        return $this->json([
            'ok' => true,
            'messages' => LiveChat::messages((int) $conversation->id, (int) $request->input('after', 0)),
        ]);
    }

    public function close($id)
    {
        $conversation = $this->find($id);
        if ($conversation) {
            DB::table('live_chat_conversations')->where('id', $conversation->id)
                ->update(['status' => 'closed', 'unread_admin' => 0, 'updated_at' => now()]);
        }
        return $this->json(['ok' => (bool) $conversation]);
    }

    public function destroy($id)
    {
        $conversation = $this->find($id);
        if ($conversation) {
            DB::table('live_chat_messages')->where('conversation_id', $conversation->id)->delete();
            DB::table('live_chat_conversations')->where('id', $conversation->id)->delete();
        }
        return $this->json(['ok' => (bool) $conversation]);
    }

    public function unread()
    {
        return $this->json(['ok' => true, 'unread' => LiveChat::totalUnreadForAdmin()]);
    }

    private function find($id)
    {
        if (!LiveChat::ready()) {
            return null;
        }
        return DB::table('live_chat_conversations')->where('id', (int) $id)->first();
    }

    private function json(array $data, int $status = 200)
    {
        return response()->json($data, $status)->header('Cache-Control', 'no-store, private');
    }
}
