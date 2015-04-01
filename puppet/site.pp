# The node definition.

node default {

  include apt

  ##
  # PHP.
  ##

  apt::ppa { 'ppa:ondrej/php5-oldstable': }
  package { 'libapache2-mod-php5': ensure => 'installed', require => Apt::Ppa['ppa:ondrej/php5-oldstable'] }
  package { 'php5-gd':             ensure => 'installed', require => Apt::Ppa['ppa:ondrej/php5-oldstable'] }
  package { 'php5-mcrypt':         ensure => 'installed', require => Apt::Ppa['ppa:ondrej/php5-oldstable'] }
  package { 'php5-curl':           ensure => 'installed', require => Apt::Ppa['ppa:ondrej/php5-oldstable'] }
  package { 'php5-xdebug':         ensure => 'installed', require => Apt::Ppa['ppa:ondrej/php5-oldstable'] }

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
  if $httpsonly {
    $rewrite_array = [
      {
        comment      => 'Rewrite to HTTPS',
        rewrite_cond => ['%{HTTP:X-Forwarded-Proto} !https'],
        rewrite_rule => ['^.*$ https://%{SERVER_NAME}%{REQUEST_URI}'],
      },
    ]
  } else {
    $rewrite_array = [ ]
  }

  apache::vhost { $fqdn:
    port           => '80',
    docroot        => '/var/www/api/public',
    manage_docroot => false,
    priority       => '25',
    override       => [ 'ALL' ],
    rewrites       => $rewrite_array,
    setenvif       => [
      'X-Forwarded-Proto https HTTPS=on',
    ],
  }
  if $httpsonly {
    apache::custom_config { 'httpsrewrite':
      content => '# Rewrite to HTTPS
RewriteCond %{HTTP:X-Forwarded-Proto} !=https
RewriteRule ^/(.*$) https://%{HTTP_HOST}/$1 [R=permanent,NE]',
    }
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
