<?php

namespace App\Console\Commands;

use App\Models\ChatbotDatas;
use Illuminate\Console\Command;
use Phpml\Classification\NaiveBayes;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Dataset\ArrayDataset;
use Phpml\Exception\FileException;
use Phpml\Exception\SerializeException;
use Phpml\FeatureExtraction\StopWords\English;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Math\Kernel;
use Phpml\Metric\Accuracy;
use Phpml\ModelManager;
use Phpml\Pipeline;
use Phpml\Tokenization\NGramTokenizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\Tokenization\WordTokenizer;
use PhpParser\Node\Expr\AssignOp\Mod;

class ChatbotCommand extends Command
{
    protected $signature = 'chatbot';

    protected $description = 'Command description';

public function handle(): bool
{
    $this->info('Training chatbot model...');
    try {
        $adminData = ChatbotDatas::admin()->get();
        $adminQuestions = array_map('strtolower', $adminData->pluck('question')->toArray());
        $adminAnswers = array_map('strtolower', $adminData->pluck('answer')->toArray());
        $this->trainAndSaveModel($adminQuestions, $adminAnswers, 'adminBotModel');

        $dosenData = ChatbotDatas::where('admin', false)->get();
        $dosenQuestions = array_map('strtolower', $dosenData->pluck('question')->toArray());
        $dosenAnswers = array_map('strtolower', $dosenData->pluck('answer')->toArray());
        $this->trainAndSaveModel($dosenQuestions, $dosenAnswers, 'dosenBotModel');

        $this->info('Chatbot model trained successfully!');
        return true;
    } catch (\Exception $e) {
        $this->error('An error occurred: ' . $e->getMessage());
        return false;
    }
}

private function trainAndSaveModel(array $questions, array $answers, string $modelName): void
{
    $pipeline = new Pipeline([
        new TokenCountVectorizer(new WordTokenizer()),
        new TfIdfTransformer()
    ], new NaiveBayes());

    $pipeline->train($questions, $answers);
    (new ModelManager())->saveToFile($pipeline, storage_path('app/' . $modelName));
}
}
