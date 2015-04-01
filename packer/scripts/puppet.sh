#!/bin/bash -eux

# Name:        puppet.sh
# Author:      Nick Schuch (nick@myschuch.com)
# Description: Installs Puppet manifest.

DIR='/tmp/api/puppet'

cd $DIR && sh scripts/provision.sh
