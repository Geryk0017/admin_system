<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AddTask extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'description',
        'developer_id',
        'client_id',
        'priority_level',
        'start_date',
        'due_date',
        'notes',
        'progress'
    ];

    // Relationship
    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Auto-generate task_id before creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->task_id)) {
                $task->task_id = self::generateTaskId();
            }
        });
    }

    // Generate unique task ID
    private static function generateTaskId()
    {
        // Get the last task ID
        $lastTask = self::orderBy('id', 'desc')->first();
        
        if (!$lastTask) {
            // First task
            return 'TSK-001';
        }

        // Extract number from last task_id (e.g., TSK-001 -> 001)
        $lastNumber = (int) substr($lastTask->task_id, 4);
        
        // Increment and format with leading zeros
        $newNumber = $lastNumber + 1;
        
        return 'TSK-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}