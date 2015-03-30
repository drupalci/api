#!/bin/bash

# Script: vagrant.sh
# Author: Nick Schuch

DIR='/vagrant/puppet'

cd $DIR && sh scripts/provision.sh
