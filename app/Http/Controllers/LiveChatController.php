<?php

namespace Fickrr\Http\Controllers;

use Fickrr\Models\LiveChat;
use Fickrr\Support\Visits\ClientAddress;
use Fickrr\Support\Visits\CountryResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Visitor side of the landing page live chat. The visitor is identified only
 * by the random conversation token kept in their browser.
 */
class LiveChatController extends Controller
{
    public function send(Request $request)
    {
        if (!LiveChat::ready() || !LiveChat::enabled()) {
            return $this->json(['ok' => false, 'error' => 'disabled'], 403);
        }

        $body = LiveChat::cleanBody($request->input('message'));
        if ($body === '') {
            return $this->json(['ok' => false, 'error' => 'empty'], 422);
        }

        $conversation = LiveChat::findByToken($request->input('token'));
        if (!$conversation) {
            $lpId = null;
            $slug = (string) $request->input('lp_slug', '');
            if ($slug !== '') {
                $lpId = DB::table('landing_pages')->where('lp_slug', $slug)->value('lp_id');
            }
            if (!$lpId) {
                return $this->json(['ok' => false, 'error' => 'unknown_page'], 422);
            }
            $email = trim((string) $request->input('email', ''));
            $conversation = LiveChat::createConversation([
                'lp_id' => $lpId,
                'page_path' => mb_substr('/landing/'.$slug, 0, 191),
                'visitor_name' => mb_substr(trim(strip_tags((string) $request->input('name', ''))), 0, 100) ?: null,
                'visitor_email' => filter_var($email, FILTER_VALIDATE_EMAIL) ? mb_substr($email, 0, 150) : null,
                'country_code' => $this->country($request),
            ]);
        }

        LiveChat::addMessage((int) $conversation->id, 'visitor', $body);

        return $this->json([
            'ok' => true,
            'token' => $conversation->token,
            'messages' => LiveChat::messages((int) $conversation->id, (int) $request->input('after', 0)),
        ]);
    }

    public function poll(Request $request)
    {
        $conversation = LiveChat::findByToken($request->input('token'));
        if (!$conversation) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        $messages = LiveChat::messages((int) $conversation->id, (int) $request->input('after', 0));
        if ($request->boolean('open')) {
            LiveChat::markRead((int) $conversation->id, 'visitor');
            $unread = 0;
        } else {
            $unread = (int) $conversation->unread_visitor;
        }

        return $this->json(['ok' => true, 'messages' => $messages, 'unread' => $unread]);
    }

    private function country(Request $request): ?string
    {
        try {
            $headers = [];
            foreach (array_change_key_case($request->headers->all(), CASE_LOWER) as $name => $values) {
                $headers[$name] = is_array($values) ? (string) ($values[0] ?? '') : (string) $values;
            }
            $ip = ClientAddress::ip($headers, (string) $request->ip());
            $code = CountryResolver::resolve($headers, $ip, (string) config('visits.geoip_path'));
            return $code ? strtoupper(substr($code, 0, 2)) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function json(array $data, int $status = 200)
    {
        return response()->json($data, $status)->header('Cache-Control', 'no-store, private');
    }
}
