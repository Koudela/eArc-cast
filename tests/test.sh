#!/bin/bash

sudo update-alternatives --config php
php /home/koudela/Projekte/eArc-cast/vendor/phpunit/phpunit/phpunit /home/koudela/Projekte/eArc-cast/tests/IntegrationTest.php
