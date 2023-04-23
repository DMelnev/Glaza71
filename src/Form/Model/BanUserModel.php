<?php


namespace App\Form\Model;


class BanUserModel
{
private $banned;

private $bannedReason;

    /**
     * @return bool|null
     */
    public function isBanned(): ?bool
    {
        return $this->banned;
    }

    /**
     * @param bool|null $banned
     */
    public function setBanned(?bool $banned): self
    {
        $this->banned = $banned;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBannedReason(): ?string
    {
        return $this->bannedReason;
    }

    /**
     * @param string|null $bannedReason
     */
    public function setBannedReason(?string $bannedReason): self
    {
        $this->bannedReason = $bannedReason;
        return $this;
    }
}