# The node definition.

node default {

  include apt
  include mysql::server
  include mysql::server::mysqltuner
  include mysql::client
  include mysql::bindings

  ##
  # PHP.
  ##

  apt::ppa { 'ppa:ondrej/php5-oldstable': }
  package { 'libapache2-mod-php5': ensure => 'installed', require => Apt::Ppa['ppa:ondrej/php5-oldstable'] }
  package { 'php5-gd':             ensure => 'installed', require => Apt::Ppa['ppa:ondrej/php5-oldstable'] }
  package { 'php5-mcrypt':         ensure => 'installed', require => Apt::Ppa['ppa:ondrej/php5-oldstable'] }
  package { 'php5-curl':           ensure => 'installed', require => Apt::Ppa['ppa:ondrej/php5-oldstable'] }
  package { 'php5-xdebug':         ensure => 'installed', require => Apt::Ppa['ppa:ondrej/php5-oldstable'] }
  package { 'php5-mysql':          ensure => 'installed', require => Apt::Ppa['ppa:ondrej/php5-oldstable'] }

  include pear
  pear::package { 'phing':
    version    => '2.4.13',
    repository => 'pear.phing.info',
  }
  
  class { 'composer':
    command_name => 'composer',
    target_dir   => '/usr/local/bin'
  }

  ##
  # Apache.
  ##

  class { 'apache':
    default_vhost => false,
    mpm_module    => 'prefork',
  }
  apache::listen { '80': }
  include apache::mod::rewrite
  include apache::mod::php

  apache::vhost { $fqdn:
    port           => '80',
    docroot        => '/var/www/api/public',
    manage_docroot => false,
    priority       => '25',
    override       => [ 'ALL' ],
    setenvif       => [
      'X-Forwarded-Proto https HTTPS=on',
    ],
  }

  mysql::db { 'drupal':
    user     => 'drupal',
    password => 'drupal',
    host     => 'localhost',
  }

  ##
  # Misc.
  ##

  # Ensure we have an update to date set of packages.
  exec { 'apt-update':
    command => '/usr/bin/apt-get update'
  }
  Exec["apt-update"] -> Package <| |>

}
