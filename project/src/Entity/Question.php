<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $question = null;

    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'question', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $answers;
    #[ORM\OneToMany(targetEntity: TestAnswer::class, mappedBy: 'question', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $testAnswers;

    public function __construct()
    {
        $this->answers     = new ArrayCollection();
        $this->testAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    public function setAnswers(array $answers): self
    {
        $this->answers = new ArrayCollection($answers);

        foreach ($answers as $answer) {
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function getTestAnswers(): Collection
    {
        return $this->testAnswers;
    }

    public function addTestAnswer(TestAnswer $testAnswer): self
    {
        if (!$this->testAnswers->contains($testAnswer)) {
            $this->testAnswers->add($testAnswer);
            $testAnswer->setQuestion($this);
        }

        return $this;
    }

    public function removeTestAnswer(Answer $testAnswer): self
    {
        if ($this->testAnswers->removeElement($testAnswer)) {
            if ($testAnswer->getQuestion() === $this) {
                $testAnswer->setQuestion(null);
            }
        }

        return $this;
    }

    public function setTestAnswers(array $testAnswers): self
    {
        $this->testAnswers = new ArrayCollection($testAnswers);

        foreach ($testAnswers as $testAnswer) {
            $testAnswer->setQuestion($this);
        }

        return $this;
    }
}
