<?php

namespace App\Support;

class MitraContext
{
    private ?string $id = null;

    public function activate(string $id): void
    {
        $this->id = $id;
        setPermissionsTeamId($id);
    }

    public function id(): ?string
    {
        return $this->id;
    }

    public function clear(): void
    {
        $this->id = null;
        setPermissionsTeamId(null);
    }
}
