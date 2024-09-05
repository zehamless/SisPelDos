<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotManFactory;
use Phpml\Classification\NaiveBayes;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;

class testBotController extends Controller
{
    public function index()
    {

    }

    public function listen()
    {
        # Dataset dalam bentuk array
        $datasheet = [
            [
                "question" => "Bagaimana cara mendaftar?",
                "answer" => "Untuk mendaftar, kamu bisa mengikuti langkah-langkah berikut: [tutorial pendaftaran]."
            ],
            [
                "question" => "Bagaimana cara reset password?",
                "answer" => "Untuk mereset password, ikuti langkah-langkah ini: [tutorial reset password]."
            ],
            // Tambahkan lebih banyak data sesuai kebutuhan...
        ];

// Ekstraksi pertanyaan dan jawaban dari datasheet
        $questions = [];
        $answers = [];
        foreach ($datasheet as $data) {
            $questions[] = $data['question'];
            $answers[] = $data['answer'];
        }

// Preprocessing: Tokenizing, Vectorizing, and Tf-Idf
        $vectorizer = new TokenCountVectorizer(new WhitespaceTokenizer());
        $vectorizer->fit($questions);
        $vectorizer->transform($questions);

        $tfIdfTransformer = new TfIdfTransformer();
        $tfIdfTransformer->fit($questions);
        $tfIdfTransformer->transform($questions);

// Melatih model Naive Bayes
        $classifier = new NaiveBayes();
        $classifier->train($questions, $answers);
//
//        $botman = BotManFactory::create();
//        $botman->listen();
    }

    function chatbotResponse($inputQuestion, $vectorizer, $tfIdfTransformer, $classifier)
    {
        $this->listen();
        $input = [$inputQuestion];
        $vectorizer->transform($input);
        $tfIdfTransformer->transform($input);
        return $classifier->predict($input);
    }
}
