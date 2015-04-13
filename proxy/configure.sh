#!/usr/bin/env bash
ps aux | grep 'sshd:' | awk '{print $2}' | xargs kill
cp /vagrant/.dockercfg /home/vagrant
