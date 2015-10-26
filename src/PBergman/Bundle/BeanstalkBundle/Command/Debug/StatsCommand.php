<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Command\Debug;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('beanstalk:debug:stats')
            ->setDescription('Statistical information about the system.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \PBergman\Bundle\BeanstalkBundle\Service\Beanstalk $beanstalk*/
        $beanstalk = $this->getContainer()->get('beanstalk');
        $stats = $beanstalk->getWorker()->stats()->getArrayCopy();
        $max = max(array_map('strlen', array_keys($stats))) + 1;
        foreach ($stats as $name => $value) {
            $output->writeln(sprintf(
                "  <options=bold>%-${max}s</> %s", str_replace('-', ' ', $name) . ":", $value
            ));
        }
    }
}