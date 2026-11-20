<?php

namespace Fickrr\Http\Controllers\Admin;

use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Admin > Items > Import reviews & comments for one item.
 * Imported entries have no user account: the name (and optional source,
 * e.g. "Etsy") is stored with them and shown on the item page.
 */
class ItemReviewImportController extends Controller
{
    private const MAX_LINES = 500;

    public function show($token)
    {
        $item = $this->item($token);
        if (!$item) {
            return redirect('/admin/items')->with('error', 'Item not found.');
        }
        $ready = $this->ready();
        return view('admin.item-reviews-import', [
            'setting' => ['setting' => Settings::editGeneral(1)],
            'item' => $item,
            'ready' => $ready,
            'reviews' => $ready ? DB::table('item_ratings')->where('or_item_id', $item->item_id)->whereNotNull('import_name')->orderByDesc('rating_id')->get() : collect(),
            'comments' => $ready ? DB::table('item_comments')->where('comm_item_id', $item->item_id)->whereNotNull('import_name')->orderByDesc('comm_id')->get() : collect(),
        ]);
    }

    public function import(Request $request, $token)
    {
        $item = $this->item($token);
        if (!$item || !$this->ready()) {
            return redirect()->back()->with('error', 'Item not found or the database update has not run yet.');
        }
        $request->validate([
            'type' => 'required|in:reviews,comments',
            'lines' => 'nullable|string|max:300000',
            'csv' => 'nullable|file|mimes:csv,txt|max:4096',
            'source' => 'nullable|string|max:60',
        ]);
        $type = $request->input('type');
        $source = trim(strip_tags((string) $request->input('source'))) ?: null;

        $raw = (string) $request->input('lines', '');
        if ($request->hasFile('csv') && $request->file('csv')->isValid()) {
            $raw .= "\n" . file_get_contents($request->file('csv')->getRealPath());
        }
        [$rows, $errors] = $this->parse($raw, $type);
        if (!$rows) {
            return redirect()->back()->withInput()->with('error', $errors ? implode(' ', array_slice($errors, 0, 5)) : 'Nothing to import. Add at least one line.');
        }

        $now = date('Y-m-d H:i:s');
        foreach (array_chunk($rows, 100) as $chunk) {
            $insert = [];
            foreach ($chunk as $r) {
                if ($type === 'reviews') {
                    $insert[] = [
                        'or_item_id' => $item->item_id,
                        'order_id' => 0,
                        'or_item_token' => $item->item_token,
                        'or_user_id' => 0,
                        'or_item_user_id' => $item->user_id,
                        'rating' => $r['rating'],
                        'rating_reason' => '',
                        'rating_comment' => $r['text'],
                        'rating_date' => $r['date'] ?: $now,
                        'import_name' => $r['name'],
                        'import_source' => $source,
                    ];
                } else {
                    $insert[] = [
                        'comm_user_id' => 0,
                        'comm_item_user_id' => $item->user_id,
                        'comm_item_id' => $item->item_id,
                        'comm_text' => $r['text'],
                        'comm_date' => $r['date'] ?: $now,
                        'import_name' => $r['name'],
                        'import_source' => $source,
                    ];
                }
            }
            DB::table($type === 'reviews' ? 'item_ratings' : 'item_comments')->insert($insert);
        }

        $msg = count($rows) . ' ' . ($type === 'reviews' ? 'review(s)' : 'comment(s)') . ' imported.';
        if ($errors) {
            $msg .= ' Skipped: ' . implode(' ', array_slice($errors, 0, 5)) . (count($errors) > 5 ? ' (+' . (count($errors) - 5) . ' more)' : '');
        }
        return redirect()->back()->with('success', $msg);
    }

    public function delete($token, $type, $id)
    {
        $item = $this->item($token);
        if ($item && $this->ready()) {
            if ($type === 'review') {
                DB::table('item_ratings')->where('rating_id', (int) $id)->where('or_item_id', $item->item_id)->whereNotNull('import_name')->delete();
            } elseif ($type === 'comment') {
                DB::table('item_comments')->where('comm_id', (int) $id)->where('comm_item_id', $item->item_id)->whereNotNull('import_name')->delete();
            }
        }
        return redirect()->back()->with('success', 'Deleted.');
    }

    public function deleteAll(Request $request, $token)
    {
        $item = $this->item($token);
        $type = $request->input('type');
        if ($item && $this->ready()) {
            if ($type === 'reviews') {
                DB::table('item_ratings')->where('or_item_id', $item->item_id)->whereNotNull('import_name')->delete();
            } elseif ($type === 'comments') {
                DB::table('item_comments')->where('comm_item_id', $item->item_id)->whereNotNull('import_name')->delete();
            }
        }
        return redirect()->back()->with('success', 'All imported ' . ($type === 'comments' ? 'comments' : 'reviews') . ' of this item were deleted.');
    }

    /**
     * One entry per line, fields separated by | or tab or comma (CSV):
     *   reviews:  Name | Rating 1-5 | Review text | Date (optional)
     *   comments: Name | Comment text | Date (optional)
     *
     * @return array{0: array, 1: string[]}
     */
    private function parse(string $raw, string $type): array
    {
        $rows = [];
        $errors = [];
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $n = 0;
        foreach ($lines as $line) {
            $n++;
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (count($rows) >= self::MAX_LINES) {
                $errors[] = 'Only the first ' . self::MAX_LINES . ' lines are imported at once.';
                break;
            }
            if (str_contains($line, '|')) {
                $f = array_map('trim', explode('|', $line));
            } elseif (str_contains($line, "\t")) {
                $f = array_map('trim', explode("\t", $line));
            } else {
                $f = array_map('trim', str_getcsv($line));
            }
            // skip a CSV header row
            if ($n === 1 && preg_match('/^(name|author|reviewer)$/i', $f[0] ?? '')) {
                continue;
            }
            $name = mb_substr(trim(strip_tags($f[0] ?? '')), 0, 100);
            if ($type === 'reviews') {
                $rating = (int) round((float) str_replace(',', '.', (string) ($f[1] ?? '')));
                $text = trim(strip_tags($f[2] ?? ''));
                $dateRaw = $f[3] ?? '';
                if ($rating < 1 || $rating > 5) {
                    $errors[] = "Line $n: rating must be 1-5.";
                    continue;
                }
            } else {
                $rating = 0;
                $text = trim(strip_tags($f[1] ?? ''));
                $dateRaw = $f[2] ?? '';
            }
            if ($text === '') {
                $errors[] = "Line $n: the text is empty.";
                continue;
            }
            $date = null;
            if (trim((string) $dateRaw) !== '') {
                $ts = strtotime((string) $dateRaw);
                if ($ts === false || $ts > time()) {
                    $errors[] = "Line $n: date not understood (imported with today's date).";
                } else {
                    $date = date('Y-m-d H:i:s', $ts);
                }
            }
            $rows[] = ['name' => $name !== '' ? $name : 'Customer', 'rating' => $rating, 'text' => mb_substr($text, 0, 2000), 'date' => $date];
        }
        return [$rows, $errors];
    }

    private function item($token)
    {
        return DB::table('items')->where('item_token', (string) $token)->first();
    }

    private function ready(): bool
    {
        try {
            return Schema::hasColumn('item_ratings', 'import_name') && Schema::hasColumn('item_comments', 'import_name');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
