<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'text-magic:import:question')]
class ImportQuestionsCommand extends Command
{

    const array QUESTIONS = [
        [
            "question" => "1 + 1",
            "answers"  => ["3", "2", "0"]
        ],
        [
            "question" => "2 + 2",
            "answers"  => ["4", "3 + 1", "10"]
        ],
        [
            "question" => "3 + 3",
            "answers"  => ["1 + 5", "1", "6", "2 + 4"]
        ],
        [
            "question" => "4 + 4",
            "answers"  => ["8", "4", "0", "0 + 8"]
        ],
        [
            "question" => "5 + 5",
            "answers"  => ["6", "18", "10", "9", "0"]
        ],
        [
            "question" => "6 + 6",
            "answers"  => ["3", "9", "0", "12", "5 + 7"]
        ],
        [
            "question" => "7 + 7",
            "answers"  => ["5", "14"]
        ],
        [
            "question" => "8 + 8",
            "answers"  => ["16", "12", "9", "5"]
        ],
        [
            "question" => "9 + 9",
            "answers"  => ["18", "9", "17 + 1", "2 + 16"]
        ],
        [
            "question" => "10 + 10",
            "answers"  => ["0", "2", "8", "20"]
        ],
    ];

    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (self::QUESTIONS as $questionEntry) {
            $question = new Question();
            $question->setQuestion($questionEntry["question"]);
            foreach ($questionEntry["answers"] as $answerEntry) {
                $answer = new Answer();
                $answer->setAnswer($answerEntry);
                $question->addAnswer($answer);
            }

            $this->em->persist($question);
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}