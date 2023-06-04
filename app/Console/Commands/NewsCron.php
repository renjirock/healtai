<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;
use jcobhams\NewsApi\NewsApi;



class RecipeCron extends Command
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
        $top_headlines = $newsapi->getTopHeadlines('health');
        dd($top_headlines);
    }
    private function getNews()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://newsapi.org/v2/top-headlines?country=us&category=health&apiKey=e82a1aa1cd734cddab866855887772a5',
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
}
