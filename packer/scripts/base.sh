#!/bin/bash -eux

# Name:        base.sh
# Author:      Nick Schuch (nick@myschuch.com)
# Description: Install base packages and configuration.

# Packages.
apt-get -y update
apt-get -y install curl wget git vim make
apt-get clean
