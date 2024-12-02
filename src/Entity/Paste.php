<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PasteRepository;

/**
 * Class Paste
 *
 * The paste entity database mapping class
 *
 * @package App\Entity
 */
#[ORM\Table(name: 'pastes')]
#[ORM\Index(name: 'pastes_token_idx', columns: ['token'])]
#[ORM\Index(name: 'pastes_ip_address_idx', columns: ['ip_address'])]
#[ORM\Entity(repositoryClass: PasteRepository::class)]
class Paste
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $token = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $views = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $time = null;

    #[ORM\Column(length: 255)]
    private ?string $browser = null;

    #[ORM\Column(length: 255)]
    private ?string $ip_address = null;

    /**
     * Get the paste id
     *
     * @return int The past id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the paste token
     *
     * @return string The paste token
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set the paste token
     *
     * @param string $token The paste token
     *
     * @return static The paste object
     */
    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the paste content
     *
     * @return string|null The paste content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set the paste content
     *
     * @param string $content The paste content
     *
     * @return static The paste object
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the paste views counter
     *
     * @return int|null The paste views
     */
    public function getViews(): ?int
    {
        return $this->views;
    }

    /**
     * Set the paste views count
     *
     * @param int $views The paste views
     *
     * @return static The paste object
     */
    public function setViews(int $views): static
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get the paste creation time
     *
     * @return DateTimeInterface|null The paste creation time
     */
    public function getTime(): ?DateTimeInterface
    {
        return $this->time;
    }

    /**
     * Set the paste creation time
     *
     * @param DateTimeInterface $time The paste creation time
     *
     * @return static The paste object
     */
    public function setTime(DateTimeInterface $time): static
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get the paste browser of paste saver
     *
     * @return string|null The paste browser
     */
    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    /**
     * Set the paste browser of the paste saver
     *
     * @param string $browser The paste browser
     *
     * @return static The paste object
     */
    public function setBrowser(string $browser): static
    {
        $this->browser = $browser;

        return $this;
    }

    /**
     * Get the paste IP address of the paste saver
     *
     * @return string|null The paste IP address
     */
    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    /**
     * Get the Ip address of the paste saver
     *
     * @return static The paste object
     */
    public function setIpAddress(string $ip_address): static
    {
        $this->ip_address = $ip_address;

        return $this;
    }
}
