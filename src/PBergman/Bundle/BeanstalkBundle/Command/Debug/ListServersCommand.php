<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Command\Debug;

use PBergman\Bundle\BeanstalkBundle\Exception\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PBergman\Bundle\BeanstalkBundle\Server\Configuration;

class ListServersCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('beanstalk:debug:servers')
            ->setDescription('Print and check all defined servers')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this
            ->getContainer()
            ->get('beanstalk')->getWorker()->peek(1);

        $manager = $this
            ->getContainer()
            ->get('beanstalk')
            ->getConnectionManager()
        ;

        foreach($manager as $config) {

            $output->writeln('');
            $output->writeln(sprintf('name:       %s', $manager->key()));
            $output->writeln(sprintf('host:       %s', $config->getHost()));
            $output->writeln(sprintf('port:       %s', $config->getPort()));
            $output->writeln(sprintf('timeout:    %s', $config->getTimeout()));
            $output->writeln(sprintf('persistent: %s', $config->isPersistent() ? 'yes' : 'no'));

            try {
                $config->getConnection()->close();
                $output->writeln('reachable:  <info>yes</info>');
            } catch (ConnectionException $e) {
                $output->writeln('reachable:  <fg=red;options=bold>no</>');
            }

            $output->writeln('');
        }

    }
}