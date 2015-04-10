#!/bin/bash

if [ ! -d ".modman" ]; then
  modman init
fi

if [ ! -d "bin/vagrant-up.sh" ]; then
  modman clone git@github.com:Sysla/vagrant-docker-utils.git
fi

sh bin/core-vagrant-up.sh