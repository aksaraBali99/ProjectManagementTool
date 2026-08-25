<?php

namespace App\Observers\Concerns;

/**
 * Task/Subtask/Comment already get audit_log rows automatically from
 * their own Observers on every plain ::create()/update() call — including
 * ones made by the Import commit service (Task/Subtask/Comment are the
 * only three entity types Import doesn't write audit_log for directly,
 * see ImportCommitService). This trait is how those auto-produced rows
 * still end up tagged with source=import + the batch id, and how their
 * usual live notification gets suppressed during a bulk import (hundreds
 * of individually-created tasks would otherwise fire hundreds of
 * notifications for one import).
 *
 * ImportCommitService binds 'import.current_batch_id' into the container
 * for the duration of commit() — nothing else in the app ever binds it,
 * so its mere presence means "this mutation happened as part of an
 * import commit."
 */
trait TagsImportBatch
{
    protected function currentImportBatchId(): ?string
    {
        return app()->bound('import.current_batch_id') ? app('import.current_batch_id') : null;
    }

    /**
     * @param  array<string, mixed>  $changes
     * @return array<string, mixed>
     */
    protected function taggedChanges(array $changes): array
    {
        if ($this->currentImportBatchId() === null) {
            return $changes;
        }

        return array_merge($changes, ['source' => 'import']);
    }

    protected function shouldSuppressNotification(): bool
    {
        return $this->currentImportBatchId() !== null;
    }
}
