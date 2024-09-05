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
            $datas = ChatbotDatas::all();
            $questions = array_map('strtolower', $datas->pluck('question')->toArray());
            $answers = array_map('strtolower', $datas->pluck('answer')->toArray());
            $dataset = new ArrayDataset($questions, $answers);
            $split = new RandomSplit($dataset);
            $pipeline = new Pipeline([
                new TokenCountVectorizer(new WordTokenizer()),
                new TfIdfTransformer()
            ], new NaiveBayes());

            $pipeline->train($questions, $answers);
            $predictions = $pipeline->predict($split->getTestSamples());
            dump('Accuracy: ' . Accuracy::score($split->getTestLabels(), $predictions));
            (new ModelManager())->saveToFile($pipeline, storage_path('app/botModel'));
            $this->info('Chatbot model trained successfully!');
            return true;
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return false;
        }
    }
}
