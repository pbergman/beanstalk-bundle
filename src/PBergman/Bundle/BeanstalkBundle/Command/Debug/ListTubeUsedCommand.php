<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Command\Debug;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListTubeUsedCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('beanstalk:debug:tube:used')
            ->setDescription('Returns the tube currently being used by the client.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \PBergman\Bundle\BeanstalkBundle\Service\Beanstalk $beanstalk*/
        $beanstalk = $this->getContainer()->get('beanstalk');
        $output->writeln($beanstalk->getWorker()->listTubeUsed()->getData());
    }
}