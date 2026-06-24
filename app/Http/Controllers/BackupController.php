<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class BackupController extends Controller
{
    private function getDbPath(): string
    {
        return config('database.connections.sqlite.database');
    }

    public function index()
    {
        $backups = collect(Storage::disk('local')->files('backups'))
            ->filter(fn ($f) => str_ends_with($f, '.sqlite'))
            ->map(fn ($f) => [
                'name'    => basename($f),
                'size'    => Storage::disk('local')->size($f),
                'date'    => Carbon::createFromTimestamp(Storage::disk('local')->lastModified($f)),
                'path'    => $f,
            ])
            ->sortByDesc('date')
            ->values();

        return view('backup.index', compact('backups'));
    }

    public function backup()
    {
        $dbPath  = $this->getDbPath();
        $name    = 'backup-' . Carbon::now()->format('Ymd-His') . '.sqlite';
        $dest    = 'backups/' . $name;

        if (! file_exists($dbPath)) {
            return back()->with('error', 'File database tidak ditemukan.');
        }

        Storage::disk('local')->put($dest, file_get_contents($dbPath));

        ActivityLog::log('backup', "Database di-backup: {$name}");

        return back()->with('success', "Backup berhasil dibuat: {$name}");
    }

    public function download(string $filename)
    {
        $path = 'backups/' . $filename;

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        ActivityLog::log('backup_download', "Backup diunduh: {$filename}");

        return Storage::disk('local')->download($path, $filename);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => ['required', 'file'],
        ]);

        $file = $request->file('backup_file');

        // Basic validation: try to open as SQLite
        $tmpPath = $file->getPathname();
        try {
            $pdo = new \PDO('sqlite:' . $tmpPath);
            $pdo->query('SELECT name FROM sqlite_master LIMIT 1');
        } catch (\PDOException $e) {
            return back()->with('error', 'File bukan database SQLite yang valid.');
        }

        $dbPath = $this->getDbPath();

        // Backup current before restoring
        $safeguard = 'backups/before-restore-' . Carbon::now()->format('Ymd-His') . '.sqlite';
        Storage::disk('local')->put($safeguard, file_get_contents($dbPath));

        // Copy uploaded file over database
        copy($tmpPath, $dbPath);

        ActivityLog::log('restore', 'Database di-restore dari upload');

        return back()->with('success', 'Database berhasil di-restore. Backup sebelum restore: ' . basename($safeguard));
    }

    public function delete(string $filename)
    {
        $path = 'backups/' . $filename;
        Storage::disk('local')->delete($path);
        return back()->with('success', "Backup '{$filename}' dihapus.");
    }
}
