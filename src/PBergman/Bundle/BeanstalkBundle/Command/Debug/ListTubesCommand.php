<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Command\Debug;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListTubesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('beanstalk:debug:tubes')
            ->setDescription('List all existing tubes.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \PBergman\Bundle\BeanstalkBundle\Service\Beanstalk $beanstalk*/
        $beanstalk = $this->getContainer()->get('beanstalk');
        $tubes = $beanstalk->getWorker()->listTubes();
        foreach($tubes as $tube) {
            $output->writeln($tube);
        }
    }
}