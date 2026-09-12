<?php

namespace App\Observers;

use App\Models\DemoActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class DemoActivityObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        if (! $this->shouldTrack($model)) {
            return;
        }

        try {
            $user = Auth::user();

            // Extract any uploaded file paths from the model attributes
            $filePaths = $this->extractFilePaths($model);

            DemoActivity::create([
                'user_id' => $user?->id,
                'record_type' => get_class($model),
                'record_id' => (string) $model->getKey(),
                'action' => 'created',
                'original_data' => null,
                'file_paths' => $filePaths ?: null,
                'expires_at' => now()->addMinutes(10),
            ]);
        } catch (Throwable $e) {
            Log::warning("DemoActivityObserver created error: {$e->getMessage()}");
        }
    }

    /**
     * Handle the Model "updating" event.
     */
    public function updating(Model $model): void
    {
        if (! $this->shouldTrack($model)) {
            return;
        }

        try {
            $user = Auth::user();
            $class = get_class($model);
            $key = (string) $model->getKey();

            // If this record was already newly created in this demo session,
            // we don't need an 'updated' rollback snapshot as the entire record will be deleted anyway.
            $alreadyCreated = DemoActivity::where('record_type', $class)
                ->where('record_id', $key)
                ->where('action', 'created')
                ->exists();

            if ($alreadyCreated) {
                return;
            }

            // Check if baseline 'updated' snapshot already exists.
            // We preserve the INITIAL baseline state before any demo edits were made.
            $existingUpdate = DemoActivity::where('record_type', $class)
                ->where('record_id', $key)
                ->where('action', 'updated')
                ->first();

            if (! $existingUpdate) {
                DemoActivity::create([
                    'user_id' => $user?->id,
                    'record_type' => $class,
                    'record_id' => $key,
                    'action' => 'updated',
                    'original_data' => $model->getRawOriginal(),
                    'file_paths' => null,
                    'expires_at' => now()->addMinutes(10),
                ]);
            }
        } catch (Throwable $e) {
            Log::warning("DemoActivityObserver updating error: {$e->getMessage()}");
        }
    }

    /**
     * Handle the Model "deleting" event.
     */
    public function deleting(Model $model): void
    {
        if (! $this->shouldTrack($model)) {
            return;
        }

        try {
            $user = Auth::user();
            $class = get_class($model);
            $key = (string) $model->getKey();

            // If it was created in demo, just clean the demo creation log
            $createdLog = DemoActivity::where('record_type', $class)
                ->where('record_id', $key)
                ->where('action', 'created')
                ->first();

            if ($createdLog) {
                $createdLog->delete();

                return;
            }

            // If real existing record was deleted by demo user, save snapshot to restore it later
            DemoActivity::create([
                'user_id' => $user?->id,
                'record_type' => $class,
                'record_id' => $key,
                'action' => 'deleted',
                'original_data' => $model->getAttributes(),
                'file_paths' => null,
                'expires_at' => now()->addMinutes(10),
            ]);
        } catch (Throwable $e) {
            Log::warning("DemoActivityObserver deleting error: {$e->getMessage()}");
        }
    }

    /**
     * Determine if model should be tracked for demo rollback.
     */
    protected function shouldTrack(Model $model): bool
    {
        // Don't track DemoActivity or User authentication models
        if ($model instanceof DemoActivity || $model instanceof User) {
            return false;
        }

        // Only track when authenticated as a Demo User
        if (! Auth::check() || ! Auth::user()?->isDemo()) {
            return false;
        }

        return true;
    }

    /**
     * Extract uploaded files from model attributes (e.g. image, icon, banner).
     */
    protected function extractFilePaths(Model $model): array
    {
        $paths = [];
        $attributes = $model->getAttributes();

        $fileKeys = ['image_path', 'featured_image', 'image_url', 'icon_url', 'banner_image', 'image', 'path'];

        foreach ($fileKeys as $key) {
            if (! empty($attributes[$key]) && is_string($attributes[$key])) {
                $val = $attributes[$key];
                if (str_starts_with($val, 'products/') || str_starts_with($val, 'blog/') || str_starts_with($val, 'slides/') || str_starts_with($val, 'categories/') || str_starts_with($val, 'collections/')) {
                    $paths[] = $val;
                }
            }
        }

        return $paths;
    }
}
