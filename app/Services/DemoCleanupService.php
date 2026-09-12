<?php

namespace App\Services;

use App\Models\DemoActivity;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DemoCleanupService
{
    /**
     * Clean up and rollback expired demo activities (older than 10 minutes).
     *
     * @return int Number of activities reverted/cleaned
     */
    public function cleanupExpired(): int
    {
        $activities = DemoActivity::expired()->orderByDesc('id')->get();

        if ($activities->isEmpty()) {
            return 0;
        }

        return $this->processActivities($activities);
    }

    /**
     * Immediately reset all demo data or demo data for a specific user.
     *
     * @return int Number of activities reverted/cleaned
     */
    public function resetAllDemoData(?int $userId = null): int
    {
        $query = DemoActivity::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $activities = $query->orderByDesc('id')->get();

        if ($activities->isEmpty()) {
            return 0;
        }

        return $this->processActivities($activities);
    }

    /**
     * Process list of demo activities and revert changes.
     */
    protected function processActivities($activities): int
    {
        $count = 0;
        $hasSettingChange = false;

        foreach ($activities as $activity) {
            try {
                $class = $activity->record_type;
                $recordId = $activity->record_id;
                $action = $activity->action;
                $originalData = $activity->original_data;
                $filePaths = $activity->file_paths;

                if (! class_exists($class)) {
                    $activity->delete();
                    $count++;

                    continue;
                }

                if ($class === SiteSetting::class) {
                    $hasSettingChange = true;
                }

                switch ($action) {
                    case 'created':
                        // 1. Delete newly created demo record
                        $class::withoutEvents(function () use ($class, $recordId) {
                            $instance = $class::find($recordId);
                            if ($instance) {
                                $instance->delete();
                            }
                        });

                        // 2. Delete any associated demo uploaded files
                        if (! empty($filePaths) && is_array($filePaths)) {
                            foreach ($filePaths as $path) {
                                if (is_string($path) && Storage::disk('public')->exists($path)) {
                                    Storage::disk('public')->delete($path);
                                }
                            }
                        }
                        break;

                    case 'updated':
                        // 3. Revert modified record back to its original baseline state
                        if (! empty($originalData) && is_array($originalData)) {
                            $class::withoutEvents(function () use ($class, $recordId, $originalData) {
                                $instance = $class::find($recordId);
                                if ($instance) {
                                    $instance->forceFill($originalData);
                                    $instance->saveQuietly();
                                }
                            });
                        }
                        break;

                    case 'deleted':
                        // 4. Restore or re-insert deleted baseline record
                        if (! empty($originalData) && is_array($originalData)) {
                            $class::withoutEvents(function () use ($class, $originalData) {
                                // Check if soft deleted
                                if (method_exists($class, 'withTrashed')) {
                                    $softDeleted = $class::withTrashed()->find($originalData['id'] ?? null);
                                    if ($softDeleted) {
                                        $softDeleted->restore();

                                        return;
                                    }
                                }

                                $class::create($originalData);
                            });
                        }
                        break;
                }

                $activity->delete();
                $count++;
            } catch (Throwable $e) {
                Log::error("DemoCleanupService process error on activity #{$activity->id}: {$e->getMessage()}");
                // Remove failed activity to avoid stuck loops
                $activity->delete();
            }
        }

        // Clear site settings cache if settings were reverted
        if ($hasSettingChange) {
            Cache::flush();
        }

        return $count;
    }
}
