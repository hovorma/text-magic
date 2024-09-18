<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UserException;
use App\Repository\QuestionRepository;
use App\Service\TestValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly TestValidationService $testValidationService,
        private readonly QuestionRepository    $questionRepository
    )
    {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $validationResults = [];
        $errorMessage = "";
        if (!empty($request->request->get("testSubmitted"))) {
            try {
                $validationResults = $this->testValidationService->validate($request->request->all()["testAnswers"] ?? []);
            } catch (Throwable $e) {
                $errorMessage = "Something went wrong";
                if ($e instanceof UserException) {
                    $errorMessage = $e->getMessage();
                }
            }
        }

        $questions = $this->questionRepository->findQuestions(["needRandomSorting" => true]);
        foreach ($questions as $question) {
            $answers = $question->getAnswers()->toArray();
            shuffle($answers);
            $question->setAnswers($answers);
        }

        return $this->render('index.html.twig', [
            "questions"         => $questions,
            "validationResults" => $validationResults,
            "errorMessage"      => $errorMessage
        ]);
    }
}