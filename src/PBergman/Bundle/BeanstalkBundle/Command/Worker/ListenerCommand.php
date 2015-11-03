<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Command\Worker;

use PBergman\Bundle\BeanstalkBundle\Exception\InvalidArgumentException;
use PBergman\Bundle\BeanstalkBundle\Exception\ResponseReserveException;
use PBergman\Bundle\BeanstalkBundle\Transformer\DataContainer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListenerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('beanstalk:worker:listner')
            ->addArgument('tubes', InputArgument::IS_ARRAY|InputArgument::REQUIRED)
            ->addOption('connection-name', 'c', InputOption::VALUE_REQUIRED, 'use a (predifined) connection config')
            ->addOption('timeout', 't', InputOption::VALUE_REQUIRED, 'set time out for reserving tubes')
            ->setDescription('Statistical information about the system.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $worker = $this->getContainer()->get('beanstalk')->getWorker($input->getOption('connection-name'));
        $tubes = $input->getArgument('tubes');
        $maps = [];

        foreach ($tubes as $tube) {

            if (preg_match('/^(?P<name>[a-z\._]+)::(?P<method>[^$]+)$/', $tube, $match)) {

                if (!method_exists($this->get($match['name']), $match['method'])) {
                    throw new \Symfony\Component\DependencyInjection\Exception\BadMethodCallException(
                        sprintf(
                            'Method "%s" does not exists for service "%s" (%s)',
                            $match['method'],
                            $match['name'],
                            get_class($this->get($match['name']))
                        )
                    );
                } else {
                    $tubeName = sprintf('%s()', $match['name'], $match['method']);
                    $worker->watch($tubeName);
                    $maps[$tubeName] = [$match['name'], $match['method']];
                }

            } else {
                throw new InvalidArgumentException(
                    sprintf('Tube %s is not a valid tube name, should use for example foo::bar where foo is the servie and bar the method', $tube)
                );
            }
        }

        if (!in_array('default', $tubes)) {
            $worker->ignore('default');
        }

        while (true) {
            try {
                /** @var DataContainer $work */
                $work = $worker->reserve($input->getOption('timeout'));
                list($name, $method) = $maps[$work->getHeader()->getName()];
                $service = $this->get($name);
                $service->$method($worker, $work->getData(), $work->getHeader());
            } catch (ResponseReserveException $e) {
                switch($e->getCode()) {
                    case $e::TIMED_OUT:
                        $output->writeln(sprintf('<error>A timeout (%s seconds) was reached while waiting for work</error>'));
                        break;
                    default:
                        throw $e;
                        break;
                }
            }
        }

    }

    /**
     * @param   $name
     * @return  object
     */
    protected function get($name)
    {
        return $this->getContainer()->get($name);
    }
}