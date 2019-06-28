<?php
/**
 * 2019-06-28.
 */

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 * @ORM\Table(name="movie", indexes={@Index(columns={"title"})})
 */
class Movie
{
    /**
     * @var int|null
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column()
     */
    private $title;

    /**
     * @var string|null
     * @ORM\Column()
     */
    private $link;

    /**
     * @var string|null
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", name="pub_date")
     */
    private $pubDate;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return Movie
     */
    public function setTitle(?string $title): Movie
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     *
     * @return Movie
     */
    public function setLink(?string $link): Movie
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return Movie
     */
    public function setDescription(?string $description): Movie
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getPubDate(): ?\DateTime
    {
        return $this->pubDate;
    }

    /**
     * @param \DateTime|null $pubDate
     *
     * @return Movie
     */
    public function setPubDate(?\DateTime $pubDate): Movie
    {
        $this->pubDate = $pubDate;

        return $this;
    }
}
