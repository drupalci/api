#!/bin/bash

# Script: vagrant.sh
# Author: Nick Schuch

DIR='/var/www/api/current/puppet'

cd $DIR && sh provision.sh
