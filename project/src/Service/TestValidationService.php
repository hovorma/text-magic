<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\TestAnswer;
use App\Exception\SystemException;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Throwable;

readonly class TestValidationService
{
    public function __construct(private EntityManagerInterface $em, private QuestionRepository $questionRepository)
    {
    }

    public function validate(array $testAnswers): array
    {
        $validationResults = [];

        if (empty($testAnswers)) {
            return [];
        } else {
            try {
                $this->em->beginTransaction();

                $this->validateAnswers($testAnswers, $validationResults);

                $this->em->flush();
                $this->em->commit();
            } catch (Throwable $e) {
                $this->em->rollback();

                throw $e;
            }
        }

        return $validationResults;
    }

    private function validateAnswers(array $testAnswers, array &$validationResults): void
    {
        $questions        = $this->questionRepository->findQuestions(["ids" => array_keys($testAnswers)]);
        $answersById      = $this->hydrateAnswers($questions);
        $questionsAnswers = $this->hydrateQuestionsAnswers($questions);

        foreach ($testAnswers as $questionId => $answers) {
            if (!isset($questions[$questionId])) {
                throw new SystemException("Unknown question with id $questionId");
            }

            $answerIsCorrect = false;

            foreach ($answers as $answer) {
                if (!isset($questionsAnswers[$questionId . "_" . $answer])) {
                    throw new SystemException("Unknown answer with id $answer");
                }

                $testAnswer = new TestAnswer();
                $testAnswer->setQuestion($questions[$questionId]);

                $testAnswer->setAnswer($answersById[$answer]);

                $this->em->persist($testAnswer);

                if ($questionsAnswers[$questionId . "_" . $answer]) {
                    $answerIsCorrect = true;
                } else {
                    $answerIsCorrect = false;
                    break;
                }
            }

            $validationResults[($answerIsCorrect) ? "correctAnswers" : "wrongAnswers"][] = $questions[$questionId]->getQuestion();
        }
    }

    private function hydrateQuestionsAnswers(array $questions): array
    {
        $questionAnswers = [];

        $expressionLanguage = new ExpressionLanguage();

        /**
         * @var int $questionId
         * @var Question $question
         */
        foreach ($questions as $questionId => $question) {
            $answers = $question->getAnswers()->toArray();

            $questionResult = $expressionLanguage->evaluate($question->getQuestion());
            /** @var Answer $answer */
            foreach ($answers as $answer) {
                $answerResult = $expressionLanguage->evaluate($answer->getAnswer());
                $questionAnswers[$questionId . "_" . $answer->getId()] = ($questionResult === $answerResult);
            }
        }

        return $questionAnswers;
    }

    private function hydrateAnswers(array $questions): array
    {
        $answersById = [];

        foreach ($questions as $question) {
            $answers = $question->getAnswers()->toArray();
            foreach ($answers as $answer) {
                $answersById[$answer->getId()] = $answer;
            }
        }

        return $answersById;
    }
}