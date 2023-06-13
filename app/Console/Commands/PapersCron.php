<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;
use App\Models\Meta;



class PapersCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:papers';

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
        $papers = $this->getPapers();
        $totalPapers = count($papers->results);
        $number = rand(0, $totalPapers-1);
        $paper = $papers->results[$number];
        $content = str_replace("\n", ' ', $paper->abstract);
        $post = $this->getChatgpt($content, $paper->url_pdf);
        $blogContent = str_replace("\n", "<p>", $post->choices[0]->message->content);
        $img = $this->getImage();
        $saveImage = $this->saveImage($img->data[0]->url);
        $blog  = new Blog;
        $blog->title = $paper->title;
        $blog->url = str_replace(" ", "-", $paper->title);
        $blog->img = $saveImage;
        $blog->content = $blogContent;
        $blog->author = 'MedicalAI';
        $meta = new Meta;
        $meta->title = $paper->title;
        $keys = $this->getMeta($blogContent, 'generates at least 100 meta keys based on the following content: ');
        $meta->keys = $keys->choices[0]->text;
        $description = $this->getMeta($blogContent, 'generates a description for the meta description of a blog based on the following content: ');
        $meta->description = $description->choices[0]->text;
        $blog->meta_id = $meta->save();
        if ($blog->save()) {
            echo 'succes';
        }
        else {
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
    private function getChatgpt($content, $url)
    {
        $prompt = 'I want you to generate a blog post based on the content of the following abstrap ' . $content . ' in this paper ' . $url;
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
    private function getImage()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.openai.com/v1/images/generations',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "prompt": "generates an image easily applicable to a post about health sciences, medicine, health, research, laboratories, ",
            "n":1,
            "size":"1024x1024"
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
    private function getPapers()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://paperswithcode.com/api/v1/papers/?q=health',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
}
