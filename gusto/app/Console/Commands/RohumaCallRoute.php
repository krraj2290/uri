<?php 
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\PubsubController;

class RohumaCallRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:rohumaCall {uri}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'php artsian route:call /route';

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
     * @return mixed
     */
    public function handle()
    {
        
//        $request = Request::create($this->argument('uri'), 'GET');
//        echo $request;die;
        $pubsub = new PubsubController();
        $pubsub->subscribe_rohuma();
//        $this->info(app()->make(\Illuminate\Contracts\Http\Kernel::class)->handle($request));
    }

}

?>