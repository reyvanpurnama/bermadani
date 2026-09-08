<?php

namespace App\Livewire\Member\Concerns;

trait EnsuresMemberCanMutate
{
    protected function ensureMemberCanMutate(): bool
    {
        if ($this->member?->isReadOnly()) {
            $this->addError('member', 'Akun anggota non-aktif hanya dapat melihat data dan tidak dapat melakukan perubahan.');

            return false;
        }

        return true;
    }
}
