<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * A console command for retrieving information about event dispatcher.
 *
 * @author Matthieu Auger <mail@matthieuauger.com>
 *
 * @final since version 3.4
 */
class EventDispatcherDebugCommand extends ContainerAwareCommand
{
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct($dispatcher = null)
    {
        parent::__construct();

        if (!$dispatcher instanceof EventDispatcherInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version 3.4 and will be removed in 4.0. If the command was registered by convention, make it a service instead.', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $dispatcher ? 'debug:event-dispatcher' : $dispatcher);

            return;
        }

        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('debug:event-dispatcher')
            ->setDefinition(array(
                new InputArgument('event', InputArgument::OPTIONAL, 'An event name'),
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'The output format  (txt, xml, json, or md)', 'txt'),
                new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw description'),
            ))
            ->setDescription('Displays configured listeners for an application')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command displays all configured listeners:

  <info>php %command.full_name%</info>

To get specific listeners for an event, specify its name:

  <info>php %command.full_name% kernel.request</info>
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // BC to be removed in 4.0
        if (null === $this->dispatcher) {
            $this->dispatcher = $this->getEventDispatcher();
        }

        $io = new SymfonyStyle($input, $output);

        $options = array();
        if ($event = $input->getArgument('event')) {
            if (!$this->dispatcher->hasListeners($event)) {
                $io->getErrorStyle()->warning(sprintf('The event "%s" does not have any registered listeners.', $event));

                return;
            }

            $options = array('event' => $event);
        }

        $helper = new DescriptorHelper();
        $options['format'] = $input->getOption('format');
        $options['raw_text'] = $input->getOption('raw');
        $options['output'] = $io;
        $helper->describe($io, $this->dispatcher, $options);
    }

    /**
     * Loads the Event Dispatcher from the container.
     *
     * BC to removed in 4.0
     *
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->getContainer()->get('event_dispatcher');
    }
}
