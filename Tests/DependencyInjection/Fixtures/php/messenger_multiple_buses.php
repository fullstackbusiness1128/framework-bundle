<?php

$container->loadFromExtension('framework', array(
    'messenger' => array(
        'default_bus' => 'messenger.bus.commands',
        'buses' => array(
            'messenger.bus.commands' => null,
            'messenger.bus.events' => array(
                'middlewares' => array(
                    'tolerate_no_handler',
                ),
            ),
            'messenger.bus.queries' => array(
                'default_middlewares' => false,
                'middlewares' => array(
                    'route_messages',
                    'tolerate_no_handler',
                    'call_message_handler',
                ),
            ),
        ),
    ),
));
