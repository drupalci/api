#!/bin/bash

# Script: provision.sh
# Author: Nick Schuch

DIR=`pwd`

# Helper function to install packages.
yumInstall() {
  if [ $(rpm -qa | grep ${1} | wc -l) -eq 0 ]; then
    yum -y install $1;
  fi
}

# Helper function to install gems packages.
gemInstall() {
  COUNT=`scl enable ruby193 "gem list | grep ${1} | wc -l"`
  if [ "${COUNT}" -eq "0" ]; then
    scl enable ruby193 "gem install $1"
  fi
}

# Packages.
yumInstall centos-release-SCL
yumInstall ruby193
yumInstall ruby193-ruby-devel
yumInstall git-all
yumInstall vim-common

# Gems.
sudo gem update --system
gemInstall bundler

# Run librarian-puppet to pull down contrib modules.
echo "Running bundler....."
cd $DIR && scl enable ruby193 "bundle install --path vendor/bundle"
echo "Running librarian puppet....."
cd $DIR && scl enable ruby193 "bundle exec librarian-puppet install"
echo "Running provision....."
cd $DIR && sudo -E scl enable ruby193 "bundle exec puppet apply --modulepath $DIR/modules $DIR/site.pp"
