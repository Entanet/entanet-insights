<?php

namespace Entanet\Insights;

use Illuminate\Console\Command;
use Superbalist\PubSub\PubSubAdapterInterface;


class EntanetInsights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entanet:insights';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lints the project and sends data to entaqa';

    protected $pubSub;

    protected $message;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PubSubAdapterInterface $pubSub)
    {
        parent::__construct();

        $this->pubSub = $pubSub;
    }

    function str_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $command = shell_exec('php artisan insights --no-interaction');


        $command = $command . ' ' . 'End of Report.';

        preg_match_all('#([^\s]+)%#', $command, $matches);

        $codeTag = '[Code]';
        $architectureTag = '[Architecture]';
        $styleTag = '[Style]';
        $complexityTag = '[Complexity]';


        if(strpos($command, $codeTag)) {
            $code = $this->str_between($command, '[Code]', '[Complexity]');
        } else
        {
            $code = 'No Code Issues to Report';
        }

        if(strpos($command, $complexityTag)) {
            $complexity = $this->str_between($command, '[Complexity]', '[Architecture]');
        } else
        {
            $complexity ='No Complexity Issues to Report';
        }


        if(strpos($command, $architectureTag)) {
            $architecture = $this->str_between($command, '[Architecture]', '[Style]');
        } else
        {
            $architecture = 'No Architecture Issues to Report';
        }


        if(strpos($command, $styleTag)) {
            $style = $this->str_between($command, '[Style]', 'End of Report.');
        } else
        {
            $style = 'No Style Issues to Report';
        }


        $name = config('app.name');

        $matches['name'] = $name;
        $matches['code'] = $matches[1][0];
        $matches['complexity'] = $matches[1][1];
        $matches['architecture'] = $matches[1][2];
        $matches['style'] = $matches[1][3];
        $matches['code_issues'] = $code;
        $matches['architecture_issues'] = $architecture;
        $matches['style_issues'] = $style;
        $matches['complexity_issues'] = $complexity;
        unset($matches[0]);
        unset($matches[1]);

        $matches = json_encode($matches);

        $this->pubSub->publish('entaqa.listen', $matches);
        print("\n". 'Data Sent!');
        return true;
    }
}