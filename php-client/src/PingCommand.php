<?php

namespace Monitor;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Description of PingCommand
 *
 * @author tibo
 */
class PingCommand extends Command {
    protected function configure()
    {
        $this
            ->setName('ping')
            ->setDescription('Send ping to monitor server')
            ->addOption('server', 's', InputOption::VALUE_OPTIONAL, "Monitor address")
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, "ID of this server")
            ->addOption('token', 't', InputOption::VALUE_OPTIONAL, "Token of this server");
    }

    /**
     * Runs the command analyze:directory
     * {@inheritDoc}
     *
     * @param InputInterface  $input  stdin reader
     * @param OutputInterface $output stdout writer
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = $input->getOption('server');
        if ($server === null) {
            $server = "https://monitor.web-d.be";
        }

        $myid = $input->getOption('id');
        $mytoken = $input->getOption('token');

        $version = file_get_contents(__DIR__ . "/../version");

        $url = $server ."/api/record/$myid";

        $data = array(
            'token' => $mytoken,
            'version' => $version);

        $sensors = [
            "loadavg" => LoadAvg::class,
            "reboot" => Reboot::class,
            "updates" => Updates::class
        ];

        foreach ($sensors as $key => $sensor_class) {
            $sensor = new $sensor_class();
            $data[$key] = $sensor->run();
        }

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        echo $result . "\n";
        // var_dump($result);
    }
}
