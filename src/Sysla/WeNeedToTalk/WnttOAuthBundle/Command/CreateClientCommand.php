<?php

namespace Sysla\WeNeedToTalk\WnttOAuthBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateClientCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sysla:oauth:create-client')
            ->setDescription('Creates a new OAuth Client')
            ->addOption(
                'redirect-uri',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Sets redirect uri for client. Use this option multiple times to set multiple redirect URIs.',
                null
            )
            ->addOption(
                'grant-type',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Sets allowed grant type for client. Use this option multiple times to set multiple grant types..',
                ["client_credentials"]
            )
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'client name',
                null
            )
            ->setHelp(
                <<<EOT
                    The <info>%command.name%</info> command creates a new client.

<info>php %command.full_name% [--grant-type=...] [--name=...] [--redirect-uri=["..."]] name</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $name = $input->getOption('name');
        $client = $clientManager->findClientBy(array('name' => $name));
        if (!empty($client)) {
            $output->writeln(sprintf('Client with name <info>%s</info> already exists', $name));
        } else {
            $client = $clientManager->createClient();
            $client->setName($input->getOption('name'));
            $client->setRedirectUris($input->getOption('redirect-uri'));
            $client->setAllowedGrantTypes($input->getOption('grant-type'));
            $clientManager->updateClient($client);
            $output->writeln(
                sprintf(
                    'Added a new client with public id <info>%s</info>, secret <info>%s</info>',
                    $client->getPublicId(),
                    $client->getSecret()
                )
            );
        }
    }
}
