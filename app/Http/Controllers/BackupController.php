<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Symfony\Component\Process\Process as SymfonyProcess;

class BackupController extends Controller
{
    /**
     * Path ke executable mysqldump.
     * Kalau di .env ada MYSQLDUMP_PATH, pakai itu.
     * Kalau tidak, coba pakai 'mysqldump' saja (asumsi sudah ada di PATH sistem).
     */
    private function mysqldumpBinary(): string
    {
        return env('MYSQLDUMP_PATH', 'mysqldump');
    }

    private function mysqlBinary(): string
    {
        return env('MYSQL_PATH', 'mysql');
    }

    private function dbConfig(): array
    {
        $conn = config('database.default');
        return config("database.connections.{$conn}");
    }

    public function index()
    {
        $backups = collect(Storage::disk('local')->files('backups'))
            ->filter(fn ($f) => str_ends_with($f, '.sql'))
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
        $config = $this->dbConfig();
        $name   = 'backup-' . Carbon::now()->format('Ymd-His') . '.sql';
        $dest   = 'backups/' . $name;

        // Pastikan folder backups ada
        Storage::disk('local')->makeDirectory('backups');
        $destAbsolutePath = Storage::disk('local')->path($dest);

        $command = [
            $this->mysqldumpBinary(),
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
            '--result-file=' . $destAbsolutePath,
            $config['database'],
        ];

        // Password dikirim lewat env var, bukan argumen (lebih aman, tidak muncul di process list)
        $process = new Process($command);
        $process->setTimeout(120);
        if (! empty($config['password'])) {
            $process->setEnv(['MYSQL_PWD' => $config['password']]);
        }

        $process->run();

        if (! $process->isSuccessful() || ! Storage::disk('local')->exists($dest) || Storage::disk('local')->size($dest) === 0) {
            Storage::disk('local')->delete($dest);
            return back()->with('error', 'Backup gagal. Pastikan mysqldump terpasang dan path-nya benar di .env (MYSQLDUMP_PATH). Detail: ' . $process->getErrorOutput());
        }

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

        // Validasi dasar: pastikan ini file .sql, cek beberapa baris pertama
        $tmpPath = $file->getPathname();
        $firstBytes = file_get_contents($tmpPath, false, null, 0, 500);
        if (stripos($firstBytes, 'CREATE TABLE') === false && stripos($firstBytes, 'INSERT INTO') === false && stripos($firstBytes, '-- MySQL') === false) {
            return back()->with('error', 'File tidak terlihat seperti dump SQL MySQL yang valid.');
        }

        $config = $this->dbConfig();

        // Backup current database dulu sebelum restore (safeguard)
        $safeguardName = 'before-restore-' . Carbon::now()->format('Ymd-His') . '.sql';
        $safeguardDest = 'backups/' . $safeguardName;
        Storage::disk('local')->makeDirectory('backups');
        $safeguardAbsolutePath = Storage::disk('local')->path($safeguardDest);

        $dumpCommand = [
            $this->mysqldumpBinary(),
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
            '--result-file=' . $safeguardAbsolutePath,
            $config['database'],
        ];
        $dumpProcess = new Process($dumpCommand);
        $dumpProcess->setTimeout(120);
        if (! empty($config['password'])) {
            $dumpProcess->setEnv(['MYSQL_PWD' => $config['password']]);
        }
        $dumpProcess->run();

        // Import file yang di-upload
        $importCommand = [
            $this->mysqlBinary(),
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
            $config['database'],
        ];
        $importProcess = new Process($importCommand);
        $importProcess->setTimeout(120);
        if (! empty($config['password'])) {
            $importProcess->setEnv(['MYSQL_PWD' => $config['password']]);
        }
        $importProcess->setInput(fopen($tmpPath, 'r'));
        $importProcess->run();

        if (! $importProcess->isSuccessful()) {
            return back()->with('error', 'Restore gagal: ' . $importProcess->getErrorOutput());
        }

        ActivityLog::log('restore', 'Database di-restore dari upload');

        return back()->with('success', 'Database berhasil di-restore. Backup sebelum restore: ' . $safeguardName);
    }

    public function delete(string $filename)
    {
        $path = 'backups/' . $filename;
        Storage::disk('local')->delete($path);
        return back()->with('success', "Backup '{$filename}' dihapus.");
    }
}