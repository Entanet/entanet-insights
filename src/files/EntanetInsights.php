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

        preg_match_all('#([^\s]+)%#', $command, $matches);

        $name = config('app.name');
        $code = $this->str_between($command, '[Code]', '[Architecture]');
        $architecture = $this->str_between($command, '[Architecture]', '[Style]');
        $style = $this->str_between($command, '[Style]', '[Security]');
        $security = $this->str_between($command, '[Security]', '✨');

        $matches['name'] = $name;
        $matches['code'] = $matches[1][0];
        $matches['complexity'] = $matches[1][1];
        $matches['architecture'] = $matches[1][2];
        $matches['style'] = $matches[1][3];
        $matches['code_issues'] = $code;
        $matches['architecture_issues'] = $architecture;
        $matches['style_issues'] = $style;
        $matches['security_issues'] = $security;
        unset($matches[0]);
        unset($matches[1]);

        $matches = json_encode($matches);

        $this->pubSub->publish('entaqa.listen', $matches);
        print("\n". 'Data Sent!');
        return true;
    }
}
