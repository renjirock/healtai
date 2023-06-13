<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;
use jcobhams\NewsApi\NewsApi;
use App\Models\Meta;
use Goutte\Client;



class NewsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule for to get news';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $newsapi = new NewsApi(ENV('NEWSAPI_KEY'));
        $topHeadlines = $newsapi->getTopHeadlines('health');
        $totalResults = $topHeadlines->totalResults;
        $news = rand(1, $totalResults);
        $new = $topHeadlines->articles[$news - 1];
        $response = $this->getConclusionsCronChathGPT($new->description, $new->url);
        $content = str_replace("\n", "<p>", $response->choices[0]->message->content);
        $blog  = new Blog;
        $blog->title = $new->title;
        $blog->url = rand(1, 100000000);
        $blog->img = $this->saveImage($new->urlToImage);
        $blog->content = $content;
        $blog->author = 'MedicalAI';
        $meta = new Meta;
        $meta->title = $new->title;
        $keys = $this->getMeta($content, 'generates at least 100 meta keys based on the following content: ');
        $meta->keys = $keys->choices[0]->text;
        $description = $this->getMeta($content, 'generates a description for the meta description of a blog based on the following content: ');
        $meta->description = $description->choices[0]->text;
        $blog->meta_id = $meta->save();
        $blog->content = $content;
        if ($blog->save()) {
            echo 'succes';
        } else {
            echo 'error';
        }
    }
    private function saveImage($img)
    {
        $url = $img;
        $contents = file_get_contents($url);
        $name = rand(0, 900000000);
        Storage::disk('public')->put('images/' . $name . '.jpg', $contents);
        return $name . '.jpg';
    }
    private function getConclusionsCronChathGPT($content, $url)
    {
        $prompt = 'I want you to generate a blog post based on the content of the following news ' . $content . ' in this url' . $url;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => ENV('CHATGPT_API') . '/v1/chat/completions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "model": "gpt-3.5-turbo",
            "messages": [
                {"role": "system", "content": "You are a us medical doctor with more than 10 years of experience in general medicine trying to write a post for good health and nutrition."},
                {"role": "user", "content": "' . $prompt . '"}
            ],
            "temperature": 0
        }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . ENV('CHATGPT_KEY'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }
    private function getMeta($content, $prompt)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => ENV('CHATGPT_API') . '/v1/completions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "model": "text-davinci-003",
                "prompt": "' . $prompt . $content . '",
                "temperature": 0,
                "max_tokens": 500
            }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . ENV('CHATGPT_KEY'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
}
