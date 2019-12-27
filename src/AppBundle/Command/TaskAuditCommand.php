<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TaskAuditCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'task:audit';

    protected function configure()
    {
        $this
            ->setDescription('Audit task to replace null author by anonymous user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $taskManager = $this->getContainer()->get('service.task_manager');
        $userManager = $this->getContainer()->get('service.user_manager');
        $io = new SymfonyStyle($input, $output);

        $numberAnonymousTask = count($taskManager->getTaskNoAuthor());

        if ($numberAnonymousTask > 0) {
            $userManager->checkAnonymousUserExist();

            $taskManager->linkTaskAnonymousUser();

            $io->success($numberAnonymousTask . ' task(s) has been successfully updated');
            return true;
        }

        $io->note('No task without an author');
        return true;
    }
}
