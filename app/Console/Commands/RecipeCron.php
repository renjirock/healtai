<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;
use App\Models\ingredient;
use Illuminate\Support\Facades\Storage;
use App\Models\Meta;


class RecipeCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:recipe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule for to get recipes';

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
        $recipe = json_decode($this->getRecipe());
        $number = rand(0, count($recipe) - 1);
        $missedIngredients = $recipe[$number]->missedIngredients;
        $usedIngredients = $recipe[$number]->usedIngredients;
        $ingredients = $this->organiceIngredients($missedIngredients, $usedIngredients);
        $response = $this->getRecipeChathGPT($ingredients,  $recipe[$number]->title);
        $title = $recipe[$number]->title;
        $post = $response->choices[0]->message->content;
        $image = $this->getImage($ingredients,  $recipe[$number]->title);
        $saveImage = $this->saveImage($image->data[0]->url);
        $blog  = new Blog;
        $blog->title = $title;
        $blog->url = str_replace(" ", "-", $title);
        $blog->img = $saveImage;
        $blog->content = str_replace("\n", "<p>", $post);
        $blog->author = 'MedicalAI';
        $meta = new Meta;
        $meta->title = $title;
        $keys = $this->getMeta(str_replace("\n", "<p>", $post), 'generates at least 100 meta keys based on the following content: ');
        $meta->keys = $keys->choices[0]->text;
        $description = $this->getMeta(str_replace("\n", "<p>", $post), 'generates a description for the meta description of a blog based on the following content: ');
        $meta->description = $description->choices[0]->text;
        $blog->meta_id = $meta->save();
        $ingredient = new ingredient;
        $ingredient->ingredients = json_encode($missedIngredients);
        $ingredient->save();
        $blog->ingredients_id = $ingredient->id;
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
    private function organiceIngredients($missedIngredients, $usedIngredients)
    {
        $ingredientText = '';
        foreach ($missedIngredients as $ingredient) {
            $ingredientText .= ' , ' . $ingredient->original . ' with a quantity of ' . $ingredient->amount . ' ' . $ingredient->unit ;
        }
        foreach ($usedIngredients as $ingredient) {
            $ingredientText .=  ' , ' . $ingredient->original . ' with a quantity of ' . $ingredient->amount . ' ' . $ingredient->unit;
        }
        return $ingredientText;
    }
    private function getRecipe(){
        $ingredient = ['chicken', 'meal', 'fish', 'tomatoes', 'carrots', 'rice', 'pork', 'lamb', 'crab', 'octopus'];
        $number = rand(0, 9);
        $host = ENV('SPOONACULAR_HOST');
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $host.'/recipes/findByIngredients?ingredients=' . $ingredient[$number] . '&number=5&limitLicense=true&ranking=1&ignorePantry=false&apiKey=' . ENV('SPOONACULAR_KEY'),
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

        return $response;
    }
    private function getImage($ingredients, $title)
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
            "prompt": "'. $title . ' with ' . $ingredients .'",
            "n":1,
            "size":"1024x1024"
        }',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer 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',
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
    private function getRecipeChathGPT($ingredients, $title){
        $prompt = 'I want you to write a post for a blog taking into account that the post is for a health food blog, where the title will be ' . $title .', with the ingredients ' . $ingredients;
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
        CURLOPT_POSTFIELDS =>'{
            "model": "gpt-3.5-turbo",
            "messages": [
                {"role": "system", "content": "You are a medical doctor with more than 10 years of experience in general medicine trying to write a post for good health and nutrition."},
                {"role": "user", "content": "' . $prompt .'"}
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
